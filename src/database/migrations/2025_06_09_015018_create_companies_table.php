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
