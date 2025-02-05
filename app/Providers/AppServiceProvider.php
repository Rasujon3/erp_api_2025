<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use App\Modules\Common\Repositories\CurrencyRepository;

class AppServiceProvider extends ServiceProvider
{




    public function boot()
    {
        // Define the modules directory
        $modulesPath = base_path('app/Modules'); // Define the modules directory

        // Check for module directories
        $modules = collect(File::directories($modulesPath))->mapWithKeys(function ($path) { // Check for module directories
            $moduleName = ucfirst(basename($path)); // Get the module name
            return [$moduleName => $path]; // Return the module name
        });

        $availableModules = collect(File::directories($modulesPath))
            ->map(function ($path) {
                return strtolower(basename($path));
            })
            ->toArray();

        // View::share(
        //     'modules',
        //     $availableModules
        // );
        // Register modules
        foreach ($modules as $module => $modulePath) { // Loop through the modules
            $this->registerModule($module, $modulePath); // Register the module
        }

        //Load Laravel’s default routes/api.php
        if (File::exists(base_path('routes/api.php'))) {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        }
    }

    /**
     * Register routes, views, and migrations for a module.
     *
     * @param string $module
     * @param string $modulePath
     */
    private function registerModule(string $module, string $modulePath): void
    {
        $routesPath = "{$modulePath}/Routes"; // Define the routes directory
        $viewsPath = "{$modulePath}/Resources/views"; // Define the views directory
        $migrationsPath = "{$modulePath}/Database/Migrations"; // Define the migrations directory

        // Load Web Routes
        if (File::exists("{$routesPath}/web.php")) { // Check for web routes
            Route::middleware('web')->group("{$routesPath}/web.php"); // Load web routes
        }


        if (File::exists("{$routesPath}/api.php")) {
            Route::middleware('api')
                ->prefix('api')
                ->group(function () use ($routesPath) {
                    require "{$routesPath}/api.php";
                });
            // Ensure API routes always return JSON
            request()->headers->set('Accept', 'application/json');
        }


        // Add View Namespace
        if (File::exists($viewsPath)) { // Check for views
            View::addNamespace(strtolower($module), $viewsPath); // Add view namespace
        }

        // Load Migrations
        if (File::exists($migrationsPath)) { // Check for migrations
            $this->loadMigrationsFrom($migrationsPath); // Load migrations
        }
    }
}
