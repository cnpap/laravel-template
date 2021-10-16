<?php

namespace App\Http\Controllers\EnterpriseControllers;

use App\Http\Controllers\Controller;
use App\TraitService\BaiDuTraitService;

class AuthController extends Controller
{
    use BaiDuTraitService;

    function uploadZj()
    {
        $ocrResult = $this->zjOcr();
        return result($ocrResult);
    }

    function uploadId()
    {
        $ocrResult = $this->idOcr();
        return result($ocrResult);
    }

    function uploadMm()
    {
        $path   = upload([
            'path'    => '/enterprise/mm',
            'extType' => 'image'
        ]);
        $result = [
            'path' => $path
        ];
        return result($result);
    }

    function uploadHj()
    {
        $path   = upload([
            'path'    => '/enterprise/hj',
            'extType' => 'image'
        ]);
        $result = [
            'path' => $path
        ];
        return result($result);
    }
}