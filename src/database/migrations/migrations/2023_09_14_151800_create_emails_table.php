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
        MigrationHelper::createTable('emails', $this->schema());
        MigrationHelper::createTable('email_histories', $this->schema());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emails');
        Schema::dropIfExists('email_histories');
    }

    private function schema()
    {

        return function (Blueprint $table,  MigrationHelper $helper) {
            $helper->baseColumn()
                ->userId(true);

            $table->dateTime("confirm_datetime")->comment("確定時刻")->nullable();
            $table->dateTime('send_datetime')->comment("送信時刻")->nullable();
            $table->string('email')->comment("Email");
            $table->string('subject')->comment("件名");
            $table->text('content')->comment("本文");
            $table->string('type')->comment("Emailタイプ");
            $table->boolean('is_failed')->comment("失敗")->nullable();


            $helper->index(1, [ColumnName::USER_ID]);
            $helper->index(2, ['email']);
            $helper->index(3, ['is_failed']);
        };
    }
};
