<?php

namespace IwslibLaravel\Util;

use IwslibLaravel\Models\ColumnName;
use IwslibLaravel\Models\Feature\IModelFeature;
use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MigrationHelper
{

    public static function createTable(string $tableName, Closure $schema)
    {
        Schema::create($tableName, function (Blueprint $table) use ($tableName, $schema) {

            $modelClassName = Str::singular('App\\Models\\' . Str::studly($tableName));
            if (class_exists($modelClassName)) {

                /**
                 * @var IModelFeature
                 */
                $model = new $modelClassName();
                $table->comment($model->getModelName());
            }

            $forHistory = false;
            if (Str::endsWith($tableName, "histories")) {
                $forHistory = true;
            }
            $helper = new MigrationHelper($table, $forHistory);
            $schema($table, $helper);
        });
    }

    public static function alterTable(string $tableName, Closure $schema)
    {
        Schema::table($tableName, function (Blueprint $table) use ($tableName, $schema) {
            $forHistory = false;
            if (Str::endsWith($tableName, "histories")) {
                $forHistory = true;
            }
            $helper = new MigrationHelper($table, $forHistory);
            $schema($table, $helper);
        });
    }



    private Blueprint $table;
    private bool $forHistory = false;

    public function __construct(Blueprint $table, bool $forHistory = false)
    {
        $this->table = $table;
        $this->forHistory = $forHistory;
    }


    public  function baseColumn()
    {
        if ($this->forHistory) {
            $this->table->id('history_id')->comment("履歴ID");
            $this->table->uuid('id')->comment("ID");
        } else {
            $this->table->uuid('id')->primary()->comment("ID");
        }
        $this->table->uuid(ColumnName::CREATED_BY)->nullable()->comment("作成者ID");
        $this->table->uuid(ColumnName::UPDATED_BY)->nullable()->comment("更新者ID");
        $this->table->timestamp(ColumnName::CREATED_AT)->nullable()->comment("作成日時");
        $this->table->timestamp(ColumnName::UPDATED_AT)->nullable()->comment("更新日時");
        $this->table->timestamp(ColumnName::DELETED_AT)->nullable()->comment("論理削除日時");

        $this->table->index([ColumnName::CREATED_AT], sprintf("%s_idx_CREATED_AT", $this->table->getTable()));
        $this->table->index([ColumnName::UPDATED_AT], sprintf("%s_idx_UPDATED_AT", $this->table->getTable()));

        return $this;
    }

    public function index(int $number, array $columns)
    {
        $indexName = $this->getIndexName($number);
        if ($this->forHistory) {
            $this->table->index($columns, $indexName);
        } else {
            $this->table->index([...$columns, ColumnName::DELETED_AT], $indexName);
        }
        return $this;
    }

    public function unique(int $number, array $columns)
    {
        $uniqueName = $this->getUniqueName($number);
        if ($this->forHistory) {
            $this->table->unique($columns, $uniqueName);
        } else {
            $this->table->unique([...$columns, ColumnName::DELETED_AT,], $uniqueName);
        }
        return $this;
    }

    public function dropIndex(int $number)
    {
        $indexName = $this->getIndexName($number);
        $this->table->dropIndex($indexName);
        return $this;
    }
    public function dropUnique(int $number)
    {
        $uniqueName = $this->getUniqueName($number);
        $this->table->dropUnique($uniqueName);
        return $this;
    }

    private function getIndexName(int $number)
    {
        return sprintf("%s_idx_%02d", $this->table->getTable(), $number);
    }

    private function getUniqueName(int $number)
    {
        return sprintf("%s_uq_%02d", $this->table->getTable(), $number);
    }

    public  function userId(bool $nullable = false, ?string $columnName = null, ?string $comment = null)
    {

        $columnName = $columnName ?? ColumnName::USER_ID;
        $comment = $comment ?? "ユーザーID";
        $this->table->uuid($columnName)->comment($comment)->nullable($nullable);
        return $this;
    }

    public  function emailId(bool $nullable = false)
    {
        $this->table->uuid(ColumnName::EMAIL_ID)->comment("EメールID")->nullable($nullable);
        return $this;
    }
}
