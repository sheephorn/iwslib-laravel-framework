<?php

use IwslibLaravel\Util\MigrationHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $helper = new MigrationHelper($table);
            $helper->baseColumn();
            $table->string('email')->unique()->comment('Email');
            $table->string('password')->nullable()->comment('ログインパスワード');
            $table->string('kintone_id')->nullable()->comment('KintoneID');
            $table->string('kintone_customer_code')->nullable()->comment('顧客コード');

            $helper->index(1, ['email']);
            $helper->index(2, ['kintone_id']);
            $helper->index(3, ['kintone_customer_code']);
        });
        Schema::create('user_histories', function (Blueprint $table) {
            $helper = new MigrationHelper($table, true);
            $helper->baseColumn();

            $table->string('email')->comment('Email');
            $table->string('password')->nullable()->comment('ログインパスワード');
            $table->string('kintone_id')->nullable()->comment('KintoneID');
            $table->string('kintone_customer_code')->nullable()->comment('顧客コード');

            $helper->index(1, ['email']);
            $helper->index(2, ['kintone_id']);
            $helper->index(3, ['kintone_customer_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_histories');
    }
};
