<?php

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
        Schema::create('event_logs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('occurred_at')->comment('イベント発生日時（JST）');
            $table->string('event_type', 50)->comment('page_view, job_view, apply_click, apply_complete など');
            
            $table->unsignedBigInteger('user_id')->nullable()->comment('求職者ユーザーID（未ログインはNULL）');
            $table->unsignedBigInteger('shop_id')->nullable()->comment('店舗ID（店舗／求人に紐づく場合）');
            $table->unsignedBigInteger('job_id')->nullable()->comment('求人ID（求人詳細ページなど）');
            
            $table->string('session_id', 100)->nullable()->comment('ブラウザセッション識別子');
            $table->string('referrer', 255)->nullable()->comment('リファラ（遷移元URL）');
            $table->string('utm_source', 100)->nullable()->comment('utm_source 等の流入元パラメータ');
            $table->string('utm_medium', 100)->nullable()->comment('utm_medium');
            $table->string('utm_campaign', 100)->nullable()->comment('utm_campaign');
            $table->string('device', 20)->nullable()->comment('pc / sp など');
            $table->char('ip_hash', 64)->nullable()->comment('IPアドレスハッシュ（プライバシー配慮）');
            
            $table->timestamp('created_at')->nullable();
            
            $table->index('occurred_at', 'idx_occurred_at');
            $table->index('event_type', 'idx_event_type');
            $table->index(['shop_id', 'occurred_at'], 'idx_shop_date');
            $table->index(['job_id', 'occurred_at'], 'idx_job_date');
            $table->index(['user_id', 'occurred_at'], 'idx_user_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_logs');
    }
};
