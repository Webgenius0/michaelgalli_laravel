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
        Schema::create('user_family_dna_reports', function (Blueprint $table) {
            $table->id();


            $table->foreignId('user_family_member_id')->constrained()->onDelete('cascade');
            $table->string('file_path'); 
            $table->json('report_data')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_family_dna_reports');
    }
};
