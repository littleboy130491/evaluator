<?php

namespace Tests\Feature;

use App\Models\GroupArea;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class GroupAreaTest extends TestCase
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
    }

    public function test_group_area_can_be_created()
    {
        $groupAreaData = [
            'name' => 'North Region',
            'description' => 'Outlets in the northern region'
        ];

        $groupArea = GroupArea::create($groupAreaData);

        $this->assertInstanceOf(GroupArea::class, $groupArea);
        $this->assertEquals($groupAreaData['name'], $groupArea->name);
        $this->assertEquals($groupAreaData['description'], $groupArea->description);
    }

    public function test_outlet_can_be_assigned_to_group_area()
    {
        $groupArea = GroupArea::factory()->create([
            'name' => 'East Region'
        ]);

        $outlet = Outlet::factory()->create([
            'name' => 'Downtown Store',
            'group_area_id' => $groupArea->id
        ]);

        $this->assertEquals($groupArea->id, $outlet->group_area_id);
        $this->assertTrue($outlet->groupArea->is($groupArea));
        $this->assertTrue($groupArea->outlets->contains($outlet));
    }

    public function test_user_can_be_assigned_as_auditor_to_group_area()
    {
        $groupArea = GroupArea::factory()->create([
            'name' => 'West Region'
        ]);

        $user = User::factory()->create();
        $user->assignRole('evaluator');

        // Assign user as auditor to group area
        $groupArea->auditors()->attach($user->id, ['role' => 'auditor']);

        $this->assertTrue($groupArea->auditors->contains($user));
        $this->assertTrue($user->auditorGroupAreas->contains($groupArea));
        $this->assertEquals('auditor', $groupArea->auditors->first()->pivot->role);
    }

    public function test_user_can_evaluate_in_assigned_group_area()
    {
        $groupArea = GroupArea::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('evaluator');

        // Assign user as auditor to group area
        $user->auditorGroupAreas()->attach($groupArea->id, ['role' => 'auditor']);

        $this->assertTrue($user->canEvaluateInGroupArea($groupArea));
    }

    public function test_admin_can_evaluate_in_any_group_area()
    {
        $groupArea = GroupArea::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Admin should be able to evaluate in any group area without explicit assignment
        $this->assertTrue($admin->canEvaluateInGroupArea($groupArea));
    }

    public function test_super_admin_can_evaluate_in_any_group_area()
    {
        $groupArea = GroupArea::factory()->create();
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        // Super admin should be able to evaluate in any group area without explicit assignment
        $this->assertTrue($superAdmin->canEvaluateInGroupArea($groupArea));
    }

    public function test_user_without_assignment_cannot_evaluate_in_group_area()
    {
        $groupArea = GroupArea::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('evaluator');

        // User without assignment should not be able to evaluate
        $this->assertFalse($user->canEvaluateInGroupArea($groupArea));
    }

    public function test_user_can_get_evaluable_outlets()
    {
        $groupArea1 = GroupArea::factory()->create(['name' => 'Region 1']);
        $groupArea2 = GroupArea::factory()->create(['name' => 'Region 2']);
        
        $outlet1 = Outlet::factory()->create(['group_area_id' => $groupArea1->id]);
        $outlet2 = Outlet::factory()->create(['group_area_id' => $groupArea1->id]);
        $outlet3 = Outlet::factory()->create(['group_area_id' => $groupArea2->id]);

        $user = User::factory()->create();
        $user->assignRole('evaluator');
        $user->auditorGroupAreas()->attach($groupArea1->id, ['role' => 'auditor']);

        $evaluableOutlets = $user->getEvaluableOutlets();

        $this->assertTrue($evaluableOutlets->contains($outlet1));
        $this->assertTrue($evaluableOutlets->contains($outlet2));
        $this->assertFalse($evaluableOutlets->contains($outlet3));
    }

    public function test_admin_can_get_all_outlets()
    {
        $groupArea1 = GroupArea::factory()->create();
        $groupArea2 = GroupArea::factory()->create();
        
        $outlet1 = Outlet::factory()->create(['group_area_id' => $groupArea1->id]);
        $outlet2 = Outlet::factory()->create(['group_area_id' => $groupArea2->id]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $evaluableOutlets = $admin->getEvaluableOutlets();

        $this->assertTrue($evaluableOutlets->contains($outlet1));
        $this->assertTrue($evaluableOutlets->contains($outlet2));
    }

    public function test_group_area_deletion_sets_outlet_group_area_to_null()
    {
        $groupArea = GroupArea::factory()->create();
        $outlet = Outlet::factory()->create(['group_area_id' => $groupArea->id]);

        $this->assertEquals($groupArea->id, $outlet->group_area_id);

        $groupArea->delete();
        $outlet->refresh();

        $this->assertNull($outlet->group_area_id);
    }
}