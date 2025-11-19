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
        Schema::create('daily_site_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->comment('集計日（JST）');
            
            $table->unsignedBigInteger('page_views')->default(0)->comment('サイト全体PV');
            $table->unsignedBigInteger('unique_users')->default(0)->comment('ユニークユーザー数（推定）');
            $table->unsignedBigInteger('sessions')->default(0)->comment('セッション数（必要なら）');
            
            $table->unsignedInteger('new_users')->default(0)->comment('新規求職者登録数');
            $table->unsignedInteger('new_shops')->default(0)->comment('新規掲載店舗数');
            $table->unsignedInteger('new_jobs')->default(0)->comment('新規求人掲載数');
            
            $table->unsignedInteger('applications')->default(0)->comment('応募完了数');
            $table->unsignedInteger('apply_clicks')->default(0)->comment('応募ボタンクリック数');
            
            $table->timestamps();
            
            $table->unique('date', 'uq_daily_site');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_site_metrics');
    }
};
