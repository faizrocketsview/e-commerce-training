<?php

namespace Formation;

use Illuminate\Support\ServiceProvider;
use Formation\Console\FormationMakeCommand;

class FormationServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        // Command
        $this->commands([
            FormationMakeCommand::class,
        ]);
        
        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views'),
        ], 'views');

        // Publish lang
        $this->publishes([
            __DIR__.'/../resources/lang' => base_path('lang'),
        ], 'lang');
        
        // Publish livewire
        $this->publishes([
            __DIR__.'/Http/Livewire' => app_path('Http/Livewire'),
        ], 'livewire');

        // Publish controller
        $this->publishes([
            __DIR__.'/Http/Controllers' => app_path('Http/Controllers'),
        ], 'controllers');

        // Publish action
        $this->publishes([
            __DIR__.'/Actions/Formation' => app_path('Actions/Formation'),
        ], 'actions');

        // Publish permission
        $this->publishes([
            __DIR__.'/Models/Permission.php' => app_path('Models/Permission.php'),
            __DIR__.'/Console/Commands/UpsertPermission.php' => app_path('Console/Commands/UpsertPermission.php'),
        ], 'permission');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations/2024_05_31_092652_create_imports_table.php' => database_path('migrations/2024_05_31_092652_create_imports_table.php'),
            __DIR__.'/../database/migrations/2024_05_31_092702_create_import_errors_table.php' => database_path('migrations/2024_05_31_092702_create_import_errors_table.php'),
            __DIR__.'/../database/migrations/2024_05_31_103208_create_bulk_edits_table.php' => database_path('migrations/2024_05_31_103208_create_bulk_edits_table.php'),
            __DIR__.'/../database/migrations/2024_05_31_103219_create_bulk_edit_errors_table.php' => database_path('migrations/2024_05_31_103219_create_bulk_edit_errors_table.php'),
        ], 'migrations');

        // Publish models
        $this->publishes([
            __DIR__.'/Models/Import.php' => app_path('Models/Import.php'),
            __DIR__.'/Models/ImportError.php' => app_path('Models/ImportError.php'),
            __DIR__.'/Models/BulkEdit.php' => app_path('Models/BulkEdit.php'),
            __DIR__.'/Models/BulkEditError.php' => app_path('Models/BulkEditError.php'),
        ], 'models');

        // Publish formation 
        $this->publishes([
            __DIR__.'/Formation/ImportFormation.php' => app_path('Formation/ImportFormation.php'),
            __DIR__.'/Formation/ImportErrorFormation.php' => app_path('Formation/ImportErrorFormation.php'),
        ], 'formation');

        $this->publishes([
            __DIR__.'/../public/images/formation/default-pdf-icon.png' => public_path('images/formation/default-pdf-icon.png'),
            __DIR__.'/../public/images/formation/default-excel-icon.png' => public_path('images/formation/default-excel-icon.png'),
            __DIR__.'/../public/images/formation/default-word-icon.png' => public_path('images/formation/default-word-icon.png'),
            __DIR__.'/../public/images/formation/default-powerpoint-icon.png' => public_path('images/formation/default-powerpoint-icon.png'),
        ], 'images');

        $this->publishes([
            __DIR__.'/../resources/css' => resource_path('css'),
            __DIR__.'/../resources/js' => resource_path('js'),
        ], 'assets');
    }
}