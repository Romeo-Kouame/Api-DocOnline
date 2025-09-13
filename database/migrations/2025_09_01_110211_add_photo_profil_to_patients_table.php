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
        Schema::table('patients', function (Blueprint $table) {
            $table->string('photo_profil')->nullable()->after('email'); // ou après 'prenom'
        });
    }

    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Reverse the migrations.
     */
    /*******  c31c59bd-7fbb-4f1b-8c4d-1fe043b0b61a  *******/
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('photo_profil');
        });
    }
};
