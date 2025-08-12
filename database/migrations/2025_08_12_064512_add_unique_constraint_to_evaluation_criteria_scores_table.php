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
        Schema::table('evaluation_criteria_scores', function (Blueprint $table) {
            // Add unique constraint to prevent duplicate criteria scores
            $table->unique(['evaluation_id', 'evaluation_criteria_id'], 'unique_evaluation_criteria_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluation_criteria_scores', function (Blueprint $table) {
            $table->dropUnique('unique_evaluation_criteria_score');
        });
    }
};