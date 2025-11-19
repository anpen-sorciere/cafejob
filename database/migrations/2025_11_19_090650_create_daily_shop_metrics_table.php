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
        Schema::create('daily_shop_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('shop_id');
            
            $table->unsignedBigInteger('shop_page_views')->default(0)->comment('店舗プロフィール等のPV');
            $table->unsignedBigInteger('job_page_views')->default(0)->comment('求人詳細ページPV合計');
            $table->unsignedInteger('apply_clicks')->default(0)->comment('応募ボタンクリック数');
            $table->unsignedInteger('applications')->default(0)->comment('応募完了数');
            $table->unsignedInteger('keeps')->default(0)->comment('キープ追加数（実装する場合）');
            
            $table->timestamps();
            
            $table->unique(['date', 'shop_id'], 'uq_daily_shop');
            $table->index(['shop_id', 'date'], 'idx_shop_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_shop_metrics');
    }
};
