<?php

use IwslibLaravel\Models\ColumnName;
use IwslibLaravel\Util\MigrationHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigrationHelper::createTable('email_attachments', $this->schema());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_attachments');
    }

    private function schema()
    {

        return function (Blueprint $table,  MigrationHelper $helper) {
            $helper->baseColumn()
                ->emailId();

            $table->string('filepath')->comment("ファイルパス");
            $table->string('send_filename')->comment("送信ファイル名");
            $table->string('mime')->comment("MIMEタイプ");

            $helper->index(1, [ColumnName::EMAIL_ID]);
            $helper->index(2, [ColumnName::CREATED_AT]);
        };
    }
};
