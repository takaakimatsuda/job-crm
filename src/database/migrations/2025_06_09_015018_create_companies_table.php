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
        Schema::create('companies', function (Blueprint $table) {
            $table->id()->comment('企業ID（主キー）');
            $table->string('name')->unique()->comment('企業名');
            $table->string('status')->nullable()->comment('選考状況（例：選考中、内定、辞退など）');
            $table->unsignedTinyInteger('hope_level')->nullable()->comment('希望度（1〜5）');
            $table->json('tags')->nullable()->comment('企業の分類タグ（例：Tech, Finance）');
            $table->string('contact_person')->nullable()->comment('担当者名');
            $table->string('email')->nullable()->comment('メールアドレス');
            $table->string('phone', 50)->nullable()->comment('電話番号');
            $table->string('website_url')->nullable()->comment('会社のWebサイトURL');
            $table->text('memo')->nullable()->comment('自由記述のメモ');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
