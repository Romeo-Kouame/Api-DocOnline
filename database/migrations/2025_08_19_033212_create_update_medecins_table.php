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
        Schema::table('medecins', function (Blueprint $table) {
            $table->integer('experience_years')->nullable()->after('specialite'); // Années d'expérience
            $table->string('languages')->nullable()->after('experience_years');   // Langues parlées
            $table->text('professional_background')->nullable()->after('languages'); // Parcours professionnel (expérience clinique + formations)
            $table->integer('consultation_price')->nullable()->after('professional_background'); // Prix consultation standard
            $table->boolean('insurance_accepted')->default(false)->after('consultation_price'); // Assurances acceptées
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medecins', function (Blueprint $table) {
            $table->dropColumn([
                'experience_years',
                'languages',
                'professional_background',
                'consultation_price',
                'insurance_accepted',
            ]);
        });
    }
};
