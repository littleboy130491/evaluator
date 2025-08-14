<?php

namespace Tests\Feature;

use App\Models\Evaluation;
use App\Models\GroupArea;
use App\Models\Outlet;
use App\Models\User;
use App\Policies\OutletPolicy;
use App\Policies\EvaluationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;

class GroupAreaAuditorAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'evaluator']);

        // Create permissions
        Permission::create(['name' => 'view_outlet']);
        Permission::create(['name' => 'view_evaluation']);
        Permission::create(['name' => 'create_evaluation']);

        // Assign permissions to evaluator role
        $evaluatorRole = Role::findByName('evaluator');
        $evaluatorRole->givePermissionTo(['view_outlet', 'view_evaluation', 'create_evaluation']);

        // Assign permissions to admin role
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo(['view_outlet', 'view_evaluation', 'create_evaluation']);

        // Assign permissions to super_admin role
        $superAdminRole = Role::findByName('super_admin');
        $superAdminRole->givePermissionTo(['view_outlet', 'view_evaluation', 'create_evaluation']);
    }

    public function test_auditor_can_view_outlets_in_assigned_group_area()
    {
        $groupArea = GroupArea::factory()->create(['name' => 'Test Region']);
        $outlet = Outlet::factory()->create(['group_area_id' => $groupArea->id]);
        
        $auditor = User::factory()->create();
        $auditor->assignRole('evaluator');
        $auditor->auditorGroupAreas()->attach($groupArea->id, ['role' => 'auditor']);

        $policy = new OutletPolicy();
        
        $this->assertTrue($policy->view($auditor, $outlet));
    }

    public function test_auditor_cannot_view_outlets_in_unassigned_group_area()
    {
        $groupArea1 = GroupArea::factory()->create(['name' => 'Region 1']);
        $groupArea2 = GroupArea::factory()->create(['name' => 'Region 2']);
        
        $outlet = Outlet::factory()->create(['group_area_id' => $groupArea2->id]);
        
        $auditor = User::factory()->create();
        $auditor->assignRole('evaluator');
        $auditor->auditorGroupAreas()->attach($groupArea1->id, ['role' => 'auditor']);

        $policy = new OutletPolicy();
        
        $this->assertFalse($policy->view($auditor, $outlet));
    }

    public function test_admin_can_view_all_outlets()
    {
        $groupArea = GroupArea::factory()->create();
        $outlet = Outlet::factory()->create(['group_area_id' => $groupArea->id]);
        
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $policy = new OutletPolicy();
        
        $this->assertTrue($policy->view($admin, $outlet));
    }

    public function test_super_admin_can_view_all_outlets()
    {
        $groupArea = GroupArea::factory()->create();
        $outlet = Outlet::factory()->create(['group_area_id' => $groupArea->id]);
        
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $policy = new OutletPolicy();
        
        $this->assertTrue($policy->view($superAdmin, $outlet));
    }

    public function test_auditor_can_view_evaluations_for_assigned_group_area()
    {
        $groupArea = GroupArea::factory()->create();
        $outlet = Outlet::factory()->create(['group_area_id' => $groupArea->id]);
        $evaluation = Evaluation::factory()->create(['outlet_id' => $outlet->id]);
        
        $auditor = User::factory()->create();
        $auditor->assignRole('evaluator');
        $auditor->auditorGroupAreas()->attach($groupArea->id, ['role' => 'auditor']);

        $policy = new EvaluationPolicy();
        
        $this->assertTrue($policy->view($auditor, $evaluation));
    }

    public function test_auditor_cannot_view_evaluations_for_unassigned_group_area()
    {
        $groupArea1 = GroupArea::factory()->create(['name' => 'Region 1']);
        $groupArea2 = GroupArea::factory()->create(['name' => 'Region 2']);
        
        $outlet = Outlet::factory()->create(['group_area_id' => $groupArea2->id]);
        $evaluation = Evaluation::factory()->create(['outlet_id' => $outlet->id]);
        
        $auditor = User::factory()->create();
        $auditor->assignRole('evaluator');
        $auditor->auditorGroupAreas()->attach($groupArea1->id, ['role' => 'auditor']);

        $policy = new EvaluationPolicy();
        
        $this->assertFalse($policy->view($auditor, $evaluation));
    }

    public function test_auditor_can_create_evaluation_for_assigned_group_area_outlet()
    {
        $groupArea = GroupArea::factory()->create();
        $outlet = Outlet::factory()->create(['group_area_id' => $groupArea->id]);
        
        $auditor = User::factory()->create();
        $auditor->assignRole('evaluator');
        $auditor->auditorGroupAreas()->attach($groupArea->id, ['role' => 'auditor']);

        $policy = new EvaluationPolicy();
        
        $this->assertTrue($policy->createForOutlet($auditor, $outlet));
    }

    public function test_auditor_cannot_create_evaluation_for_unassigned_group_area_outlet()
    {
        $groupArea1 = GroupArea::factory()->create(['name' => 'Region 1']);
        $groupArea2 = GroupArea::factory()->create(['name' => 'Region 2']);
        
        $outlet = Outlet::factory()->create(['group_area_id' => $groupArea2->id]);
        
        $auditor = User::factory()->create();
        $auditor->assignRole('evaluator');
        $auditor->auditorGroupAreas()->attach($groupArea1->id, ['role' => 'auditor']);

        $policy = new EvaluationPolicy();
        
        $this->assertFalse($policy->createForOutlet($auditor, $outlet));
    }

    public function test_outlets_without_group_area_accessible_to_all_evaluators()
    {
        $outlet = Outlet::factory()->create(['group_area_id' => null]);
        
        $evaluator = User::factory()->create();
        $evaluator->assignRole('evaluator');

        $policy = new OutletPolicy();
        
        $this->assertTrue($policy->view($evaluator, $outlet));
    }

    public function test_evaluations_for_outlets_without_group_area_accessible_to_all_evaluators()
    {
        $outlet = Outlet::factory()->create(['group_area_id' => null]);
        $evaluation = Evaluation::factory()->create(['outlet_id' => $outlet->id]);
        
        $evaluator = User::factory()->create();
        $evaluator->assignRole('evaluator');

        $policy = new EvaluationPolicy();
        
        $this->assertTrue($policy->view($evaluator, $evaluation));
    }

    public function test_multiple_auditors_can_be_assigned_to_same_group_area()
    {
        $groupArea = GroupArea::factory()->create();
        $outlet = Outlet::factory()->create(['group_area_id' => $groupArea->id]);
        
        $auditor1 = User::factory()->create();
        $auditor1->assignRole('evaluator');
        $auditor1->auditorGroupAreas()->attach($groupArea->id, ['role' => 'auditor']);
        
        $auditor2 = User::factory()->create();
        $auditor2->assignRole('evaluator');
        $auditor2->auditorGroupAreas()->attach($groupArea->id, ['role' => 'auditor']);

        $policy = new OutletPolicy();
        
        $this->assertTrue($policy->view($auditor1, $outlet));
        $this->assertTrue($policy->view($auditor2, $outlet));
    }

    public function test_auditor_can_be_assigned_to_multiple_group_areas()
    {
        $groupArea1 = GroupArea::factory()->create(['name' => 'Region 1']);
        $groupArea2 = GroupArea::factory()->create(['name' => 'Region 2']);
        
        $outlet1 = Outlet::factory()->create(['group_area_id' => $groupArea1->id]);
        $outlet2 = Outlet::factory()->create(['group_area_id' => $groupArea2->id]);
        
        $auditor = User::factory()->create();
        $auditor->assignRole('evaluator');
        $auditor->auditorGroupAreas()->attach([
            $groupArea1->id => ['role' => 'auditor'],
            $groupArea2->id => ['role' => 'auditor']
        ]);

        $policy = new OutletPolicy();
        
        $this->assertTrue($policy->view($auditor, $outlet1));
        $this->assertTrue($policy->view($auditor, $outlet2));
    }
}