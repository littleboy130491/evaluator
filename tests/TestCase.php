<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Configure Filament for testing
        $this->app->singleton(\Filament\FilamentManager::class, function ($app) {
            return new \Filament\FilamentManager();
        });
        
        // Register Filament routes for testing
        $this->app->booted(function () {
            \Filament\Facades\Filament::serving(function () {
                //
            });
        });
    }
}
