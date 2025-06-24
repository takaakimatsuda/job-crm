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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id()->comment('履歴ID（主キー）');
            $table->foreignId('company_id')->constrained()->onDelete('cascade')->comment('紐づく企業のID（外部キー）');
            $table->date('interaction_date')->nullable()->comment('接触日・面談日');
            $table->string('type')->comment('接触種別（電話、面談、メールなど）');
            $table->text('memo')->nullable()->comment('面談ややり取りの詳細メモ');
            $table->text('summary')->nullable()->comment('GPTによる要約結果');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
