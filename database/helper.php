<?php

use App\Models\Comm\Category;
use App\Models\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @param Model|string $model
 * @return void
 */
function alterFulltextIndex(string $model)
{
    $fulltext     = $model::Fulltext;
    $indexName    = sprintf(
        "fulltext_%s_%s_index",
        $model::table(),
        implode('_', $fulltext),
    );
    $indexColumns = implode(', ', $fulltext);
    $sql          = sprintf(
        "alter table %s add fulltext index %s (%s) with parser ngram",
        $model::table(),
        $indexName,
        $indexColumns
    );
    DB::statement($sql);
}

/**
 * @param Model|string $model
 * @return void
 */
function alterTableComment(string $model, string $comment)
{
    $sql = sprintf(
        "alter table %s comment '%s'",
        $model::table(),
        $comment
    );
    DB::statement($sql);
}

/**
 * @param Model|string $model
 * @return void
 */
function alterTable(string $model, string $comment = null)
{
    if ($comment !== null) {
        alterTableComment($model, $comment);
    }
    if (count($model::Fulltext) > 0) {
        alterFulltextIndex($model);
    }
}

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

/**
 * @param Category|string $model
 * @param $comment
 * @return void
 */
function createCategoryTable(string $model, $comment)
{
    $tableName = $model::table();
    Schema::create($tableName, function (Blueprint $table) use ($model) {
        $table->bigIncrements('id')->unique()->comment('分类ID');
        $table->bigInteger('pid')->index()->nullable()->comment('上级分类');
        $table->timestamps();

        $table->smallInteger('status')->default(_NEW)->comment('管理员角色数据状态: 1 新数据, 2 已占用, 3 异常中, 4 已停用');
        $table->smallInteger('level')->comment('等级')->nullable();
        $table->string('name', 40)->unique()->comment('分类名称');
        $table->string('code', 40)->comment('分类编号');
        $table->string('description', 200)->nullable()->comment('分类描述/备注');

        $table->unique(['name', 'level']);
    });

    if (config('app.debug')) {
        $data = [];
        createCategoryTableData($data);
        DB::table($tableName)->insert($data);
    }

    alterTable($model, $comment);
}