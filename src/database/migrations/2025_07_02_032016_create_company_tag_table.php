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
        Schema::create('company_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            // 複合主キーでユニーク制約
            $table->primary(['company_id', 'tag_id']);

            // 外部キー制約
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_tag');
    }
};
