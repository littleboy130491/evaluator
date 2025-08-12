<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseMigrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function migrations_run_successfully(): void
    {
        // Check if all expected tables exist
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('outlets'));
        $this->assertTrue(Schema::hasTable('evaluation_criteria'));
        $this->assertTrue(Schema::hasTable('evaluations'));
        $this->assertTrue(Schema::hasTable('evaluation_criteria_scores'));
        $this->assertTrue(Schema::hasTable('histories'));
        $this->assertTrue(Schema::hasTable('reports'));
    }

    /** @test */
    public function users_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('users', [
            'id', 'name', 'email', 'email_verified_at', 'password', 'remember_token', 'created_at', 'updated_at'
        ]));
    }

    /** @test */
    public function outlets_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('outlets', [
            'id', 'name', 'address', 'phone_number', 'manager_name', 'notes', 'created_at', 'updated_at', 'deleted_at'
        ]));
    }

    /** @test */
    public function evaluation_criteria_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('evaluation_criteria', [
            'id', 'name', 'description', 'max_score', 'category', 'is_active', 'created_at', 'updated_at', 'deleted_at'
        ]));
    }

    /** @test */
    public function evaluations_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('evaluations', [
            'id', 'user_id', 'outlet_id', 'evaluation_date', 'status', 'notes', 'approved_by', 'approved_at', 'created_at', 'updated_at', 'deleted_at'
        ]));
    }

    /** @test */
    public function evaluation_criteria_scores_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('evaluation_criteria_scores', [
            'id', 'evaluation_id', 'evaluation_criteria_id', 'score', 'notes', 'created_at', 'updated_at'
        ]));
    }

    /** @test */
    public function histories_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('histories', [
            'id', 'historiable_type', 'historiable_id', 'user_id', 'action', 'old_values', 'new_values', 'created_at', 'updated_at'
        ]));
    }

    /** @test */
    public function reports_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('reports', [
            'id', 'name', 'type', 'parameters', 'user_id', 'file_path', 'generated_at', 'created_at', 'updated_at', 'deleted_at'
        ]));
    }
}