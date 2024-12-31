<?php

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
        Schema::create('account', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('password');
            $table->string('access_token', 100)->nullable();
            $table->string('refresh_token', 100)->nullable();
            $table->timestamp('created_at', precision: 0);
            $table->timestamp('updated_at', precision: 0);
        });
        Schema::create('post', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id');
            $table->timestamp('created_at', precision: 0);
            $table->timestamp('updated_at', precision: 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account');
        Schema::dropIfExists('post');
    }
};
