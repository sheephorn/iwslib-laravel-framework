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

            $helper->index(1, ['email']);
        });
        Schema::create('user_histories', function (Blueprint $table) {
            $helper = new MigrationHelper($table, true);
            $helper->baseColumn();

            $table->string('email')->comment('Email');
            $table->string('password')->nullable()->comment('ログインパスワード');

            $helper->index(1, ['email']);
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
