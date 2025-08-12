<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Outlet;
use App\Models\EvaluationCriteria;
use App\Policies\UserPolicy;
use App\Policies\OutletPolicy;
use App\Policies\EvaluationCriteriaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Outlet::class => OutletPolicy::class,
        EvaluationCriteria::class => EvaluationCriteriaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}