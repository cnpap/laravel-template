<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function createCategoryTableData(&$data, $pid = null, $label = '0', $level = 1, $leafLevel = 2)
{
    if ($pid === null) {
        $id = 1;
    } else {
        $id = $pid;
    }
    $count = 5;
    for ($i = 0; $i < $count; $i++) {
        $concatLabel = $label;
        $concatLabel .= "-" . ($i + 1);
        $curr        = [
            'pid'        => $pid,
            'level'      => $level,
            'name'       => $concatLabel,
            'code'       => $concatLabel,
            'created_at' => now()->format('Y-m-d H:i:s'),
            'updated_at' => now()->format('Y-m-d H:i:s')
        ];
        $data[]      = &$curr;
        if ($level < $leafLevel) {
            $curr['status'] = _USED;
            $serial         = createCategoryTableData($data, $id + $i, $concatLabel, $level + 1, $leafLevel);
            $id             = $serial - $i;
        } else {
            $curr['status'] = _NEW;
        }
        unset($curr);
    }
    return $id + $count;
}

function createCategoryTable($name, $comment)
{
    Schema::create($name, function (Blueprint $table) {
        $table->bigIncrements('id')->unique()->comment('分类ID');
        $table->bigInteger('pid')->nullable()->comment('上级分类');
        $table->timestamps();

        $table->string('status', 3)->default(_NEW)->comment('分类数据状态: 新数据, 已占用, 已停用, 异常中');
        $table->smallInteger('level')->comment('等级')->nullable();
        $table->string('name', 40)->comment('分类名称');
        $table->string('code', 40)->comment('分类编号');
        $table->string('description', 200)->nullable()->comment('分类描述/备注');

        $table->unique(['name', 'level']);
        $table->unique(['code', 'level']);
    });

    if (config('app.debug')) {
        $data = [];
        createCategoryTableData($data);
        DB::table($name)->insert($data);
    }

    DB::statement("alter table $name comment '$comment'");
}
