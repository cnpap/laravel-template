<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    protected $topParentId;

    public function __construct()
    {
        $this->topParentId = (int)config('database.region_top_id');
    }

    function levelPatten($level)
    {
        $patten = '';
        switch ($level) {
            case 1:
                $patten = '0000000000';
                break;
            case 2:
                $patten = '00000000';
                break;
            case 3:
                $patten = '000000';
                break;
            case 4:
                $patten = '000';
                break;
        }
        return $patten;
    }

    function parentRegions($childId, $patten, $children = [])
    {
        /** @var Region $parentRegion */
        $parentRegion = Region::query()->where('id', $childId)->firstOrFail();
        $brother = Region::query()
            ->whereIn(
                'parent_id',
                Region::query()
                    ->where('id', $parentRegion->parent_id)
                    ->select('parent_id')
            )
            ->when(
                $patten,
                function (Builder $builder, $val) {
                    $builder->where('code', 'like', '%' . $val);
                }
            )
            ->get();
        $options = [];
        $isLeaf  = !count($children);
        /** @var Region $region */
        foreach ($brother as $region) {
            $option           = [
                'value' => $region->code,
                'label' => $region->name,
            ];
            $option['isLeaf'] = $isLeaf && !preg_match("@0$patten$@", $region->code);
            if (!$isLeaf && $region->id === $parentRegion->parent_id) {
                $option['children'] = $children;
            }
            $options[] = $option;
        }
        /** @var Region $sample */
        $sample = $brother[0];
        if ($sample->parent_id === $this->topParentId) {
            return $options;
        }
        return $this->parentRegions($sample->parent_id, $patten, $options);
    }

    public function region(Request $request)
    {
        $this->validate(
            $request,
            [
                'code'  => 'string',
                'level' => 'integer'
            ]
        );
        $code  = $request->input('code', false);
        $level = $request->input('level', 8);
        if (!$code) {
            $regions = Region::query()
                ->where('parent_id', $this->topParentId)
                ->get();
            $options = [];
            /** @var Region $region */
            foreach ($regions as $region) {
                $options[] = [
                    'value'  => $region->code,
                    'label'  => $region->name,
                    'isLeaf' => false
                ];
            }
            $result = $options;
        } else {
            $brother = Region::query()
                ->whereIn(
                    'parent_id',
                    Region::query()
                        ->where('code', $code)
                        ->select('parent_id')
                )
                ->get();
            $options = [];
            /** @var Region $region */
            foreach ($brother as $region) {
                $option           = [
                    'value' => $region->code,
                    'label' => $region->name,
                ];
                $option['isLeaf'] = true;
                $options[]        = $option;
            }
            /** @var Region $example */
            $example = $brother[0];
            $patten  = $this->levelPatten($level);
            $result  = $this->parentRegions($example->id, $patten, $options);
        }
        return result($result);
    }

    function childRegions(Request $request)
    {
        $this->validate(
            $request,
            [
                'code'  => 'required|string',
                'level' => 'integer'
            ]
        );
        $level   = $request->input('level');
        $patten  = $this->levelPatten($level);
        $regions = Region::query()
            ->whereIn(
                'parent_id',
                Region::query()
                    ->where('code', $request->input('code'))
                    ->select('id')
            )
            ->get();
        $options = [];
        /** @var Region $region */
        foreach ($regions as $region) {
            $options[] = [
                'value'  => $region->code,
                'label'  => $region->name,
                'isLeaf' => !preg_match("@0$patten$@", $region->code)
            ];
        }
        return result($options);
    }
}