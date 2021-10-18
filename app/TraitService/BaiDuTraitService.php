<?php

namespace App\TraitService;

use App\Exceptions\BaiDuServiceException;
use App\Exceptions\ImageSizeInvalidException;
use App\Models\Enterprise\EnterpriseAuth;
use Carbon\Carbon;
use GuzzleHttp\Client;
use http\Exception\RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Log;

trait BaiDuTraitService
{
    function client()
    {
        return new Client([
            'base_uri' => 'https://aip.baidubce.com',
            'verify'   => false
        ]);
    }

    function encodeDate(&$rawDate)
    {
        $ok = preg_match('@^(\d{4})[^\d]+(\d{2})[^\d]+(\d{2})[^\d]+$@', $rawDate, $clAtMatch);
        if ($ok && count($clAtMatch) === 4) {
            $rawDate = $clAtMatch[1] . '-' . $clAtMatch[2] . '-' . $clAtMatch[3];
        }
    }

    function token()
    {
        $token = Cache::get('baidu token');
        if ($token) {
            return $token;
        }
        $client     = $this->client();
        $response   = $client->get(
            '/oauth/2.0/token',
            [
                'query' => [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => config('ocr.app_access'),
                    'client_secret' => config('ocr.app_secret')
                ]
            ]
        );
        $statusCode = $response->getStatusCode();
        $result     = json_decode($response->getBody()->getContents(), true);
        if ($statusCode !== 200) {
            Log::channel('errorlog')->error('baidu token', $result);
            throw new BaiDuServiceException('调取服务出错' . BaiDuServiceException::TOKEN);
        }
        $ok = Cache::put('baidu token', $result['access_token'], 3600 * 5);
        if (!$ok) {
            Log::channel('errorlog')->error('redis set fail');
            throw new RuntimeException('服务器产生严重错误');
        }
        return $result['access_token'];
    }

    function checkImg()
    {
        /** @var Request $request */
        $request = app('request');
        $file    = $request->file('file');
        $path    = $file->getRealPath();
        $imgCtt  = $file->getContent();
        $imgBs64 = base64_encode($imgCtt);
        $imgUrl  = urlencode($imgBs64);
        if (strlen($imgUrl) >= 4 * 1024 * 1024) {
            throw new ImageSizeInvalidException('请控制文件大小再 2.5m 以内');
        }
        $sizeInfo = getimagesize($path);
        $size     = [$sizeInfo[0], $sizeInfo[1]];
        foreach ($size as $num) {
            if ($num <= 15) {
                throw new ImageSizeInvalidException('请控制图片 宽/高 不小于 15px');
            }
            if ($num >= 4096) {
                throw new ImageSizeInvalidException('请控制图片 宽/高 不大于 4096px');
            }
        }
        return compact('imgBs64');
    }

    function idOcr()
    {
        /** @var Request $request */
        $request  = app('request');
        $side     = $request->input('side');
        $imgInfo  = $this->checkImg();
        $imgBs64  = $imgInfo['imgBs64'];
        $extType  = 'image';
        $path     = '/enterprise/id_img';
        $path     = upload(compact('path', 'extType'));
        $client   = $this->client();
        $response = $client->post(
            '/rest/2.0/ocr/v1/idcard',
            [
                'query'       => ['access_token' => $this->token()],
                'form_params' => ['image' => $imgBs64, 'id_card_side' => $side]
            ]
        );
        $result     = json_decode($response->getBody()->getContents(), true);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            Log::channel('errorlog')->error('baidu id ocr', $result);
            throw new BaiDuServiceException('调取服务出错' . BaiDuServiceException::OCR);
        }
        $result = $result['words_result'];
        $qyAuth = new EnterpriseAuth();
        if ($side === 'front') {
            $qyAuth->fr_name      = $result['姓名']['words'];
            $qyAuth->fr_card_code = $result['公民身份号码']['words'];
            $qyAuth->fr_img_1     = $path;
        } else {
            $qyAuth->fr_img_2      = $path;
            $qyAuth->fr_card_qf_sj = Carbon::parse($result['签发日期']['words'])->toDateString();
            if ($result['失效日期']['words'] !== '长期') {
                $qyAuth->fr_card_yx_sj = Carbon::parse($result['失效日期']['words'])->toDateString();
            } else {
                $qyAuth->fr_card_yx_sj = '长期';
            }
        }
        EnterpriseAuth::query()->updateOrCreate([
            'id'     => sess('eid'),
            'status' => EnterpriseAuth::INVITE_ALLOW
        ], array_merge($qyAuth->toArray()));
        return [
            'path' => $path,
            'data' => $qyAuth->toArray()
        ];
    }

    function zjOcr()
    {
        $imgInfo    = $this->checkImg();
        $imgBs64    = $imgInfo['imgBs64'];
        $extType    = 'image';
        $path       = '/enterprise/zj_img';
        $path       = upload(compact('path', 'extType'));
        $client     = $this->client();
        $response   = $client->post(
            '/rest/2.0/ocr/v1/business_license',
            [
                'query'       => ['access_token' => $this->token()],
                'form_params' => ['image' => $imgBs64]
            ]
        );
        $result     = json_decode($response->getBody()->getContents(), true);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            Log::channel('errorlog')->error('baidu zj ocr', $result);
            throw new BaiDuServiceException('调取服务出错' . BaiDuServiceException::OCR);
        }
        $result = $result['words_result'];
        $clSj   = $result['成立日期']['words'];
        $yxSj   = $result['有效期']['words'];
        $this->encodeDate($clSj);
        $this->encodeDate($yxSj);
        if ($yxSj === '长期') {
            $yxSj = null;
        }
        $this->encodeDate($yxSj);
        $qyAuth          = new EnterpriseAuth();
        $qyAuth->cl_sj   = $clSj;
        $qyAuth->yx_sj   = $yxSj;
        $qyAuth->jy_fw   = $result['经营范围']['words'];
        $qyAuth->zc_zb   = $result['注册资本']['words'];
        $qyAuth->fr_name = $result['法人']['words'];
        $qyAuth->zj_code = $result['证件编号']['words'];
        $qyAuth->qy_name = $result['单位名称']['words'];
        $qyAuth->xy_code = $result['社会信用代码']['words'];
        $qyAuth->qy_site = $result['地址']['words'];
        $qyAuth->qy_type = $result['类型']['words'];
        $qyAuth->zj_img  = $path;
        $this->encodeDate($clSj);
        EnterpriseAuth::query()->updateOrCreate([
            'id'     => sess('eid'),
            'status' => EnterpriseAuth::INVITE_ALLOW
        ], array_merge($qyAuth->toArray()));
        return [
            'path' => $path,
            'data' => $qyAuth->toArray()
        ];
    }
}