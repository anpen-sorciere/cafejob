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
        Schema::create('daily_job_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('shop_id');
            
            $table->unsignedBigInteger('page_views')->default(0)->comment('求人ページPV');
            $table->unsignedInteger('apply_clicks')->default(0)->comment('応募ボタン押下');
            $table->unsignedInteger('applications')->default(0)->comment('応募完了');
            $table->unsignedInteger('keeps')->default(0)->comment('キープ追加');
            
            $table->timestamps();
            
            $table->unique(['date', 'job_id'], 'uq_daily_job');
            $table->index(['shop_id', 'date', 'job_id'], 'idx_shop_job_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_job_metrics');
    }
};
