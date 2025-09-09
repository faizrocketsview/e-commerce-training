# Formation Laravel Livewire Package Documentation

**Written by: Faiz Nasir**

## Overview

Formation is a comprehensive Laravel Livewire package designed to rapidly generate CRUD (Create, Read, Update, Delete) interfaces with advanced features like data tables, forms, permissions, and import/export functionality. It provides a structured approach to building administrative interfaces with minimal code.

### Key Features

- **CRUD Generation**: Automatic generation of index, create, edit, show, and delete operations
- **Data Tables**: Advanced data tables with sorting, filtering, searching, and pagination. EVERY DATABASE TABLE NAME MUST USE 'PLURAL' NOUNS
- **Form Builder**: Dynamic form generation with various field types
- **Permission System**: Integrated role-based access control using Spatie Laravel Permission
- **Import/Export**: CSV import/export functionality with validation
- **Bulk Operations**: Bulk edit, delete, and other operations
- **Responsive UI**: Built with Tailwind CSS for modern, responsive design
- **Multi-language Support**: Localization support for internationalization

---

## Architecture

### Package Structure

```
app/packages/formation/
├── src/
│   ├── Actions/           # Core action classes
│   ├── DataTable/         # Data table traits and functionality
│   ├── Form/              # Form builder classes
│   ├── Index/             # Index page components
│   ├── Http/              # Controllers and Livewire components
│   ├── Models/            # Package models
│   └── FormationServiceProvider.php
├── resources/
│   ├── views/             # Blade templates
│   ├── css/               # Stylesheets
│   └── js/                # JavaScript files
└── composer.json
```

### Core Dependencies

- **Laravel Framework**: ^10.10
- **Livewire**: ^2.4
- **Laravel Jetstream**: ^3.2
- **Laravel Sanctum**: ^3.0
- **Spatie Laravel Permission**: ^6.17

---

## Installation & Setup

### 1. Package Installation

The Formation package is included as a local package in your project:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "app/packages/formation",
            "options": {
                "symlink": false
            }
        }
    ],
    "require": {
        "leekhengteck/formation": "dev-develop"
    }
}
```

### 2. Service Provider Registration

The package is automatically registered via the `composer.json` configuration:

```json
{
    "extra": {
        "laravel": {
            "providers": [
                "Formation\\FormationServiceProvider"
            ]
        }
    }
}
```

### 3. Publishing Assets

```bash
# Publish views
php artisan vendor:publish --tag=views

# Publish language files
php artisan vendor:publish --tag=lang

# Publish migrations
php artisan vendor:publish --tag=migrations

# Publish models
php artisan vendor:publish --tag=models
```

---

## Core Components

### 1. Formation Trait

The core trait that provides the main functionality:

```php
<?php

namespace Formation;

trait Formation
{
    public static function createForm(String $name, Closure $callback): Form
    {
        return new Form($name, $callback);
    }

    public static function createIndex(string $name, Closure $callback): Index
    {
        return new Index($name, $callback);
    }
}
```

### 2. WithDataTable Trait

The main trait used by Livewire components to provide data table functionality:

```php
<?php

namespace Formation\DataTable;

trait WithDataTable
{
    use AuthorizesRequests;
    use WithPagination;
    use WithSorting;
    use WithItemsSelection;
    use WithFiltering;
    use WithSearch;
    use WithCache;
    
    // Core properties and methods for data table functionality
}
```

### 3. Resource Component

Base Livewire component for handling CRUD operations:

```php
<?php

namespace App\Http\Livewire;

use Formation\DataTable\WithDataTable;
use Livewire\Component;

class Resource extends Component
{
    use WithDataTable;

    protected $listeners = ['updateFolderPath', 'updateFileColumnName', 'setFiles', 'previewImage'];
    
    protected $queryString = ['itemId', 'itemType', 'sorts', 'search', 'filters', 'showFilter', 'perPage', 'type', 'formId', 'tab'];

    public $view = [
        'index' => 'formation.index',
        'form' => 'formation.form',
        'reorder' => 'formation.reorder',
    ];
}
```

---

## Authentication & Authorization

### 1. Authentication System

The application uses Laravel Fortify and Jetstream for authentication:

#### Fortify Configuration

```php
// config/fortify.php
'username' => 'username',
'features' => [
    Features::resetPasswords(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]),
],
```

#### Custom Authentication Logic

```php
// app/Providers/FortifyServiceProvider.php
Fortify::authenticateUsing(function (Request $request) {
    $user = User::where('username', $request->username)->where('status', 'active')->first();

    if ($user && Hash::check($request->password, $user->password)) {
        return $user;
    }
});
```

### 2. User Model

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles; // Spatie Permission integration
}
```

---

## Routing System

### 1. Route Structure

The application uses a dynamic routing system based on module structure:

```php
// routes/web.php
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    
    // Dynamic module routes
    Route::get('/{moduleSection}/{moduleGroup}/{module}', App\Http\Livewire\Resource::class);
    
    // Specialized routes for specific modules
    Route::get('/{moduleSection}/{moduleGroup}/{module}/import', App\Http\Livewire\ImportResource::class);
    Route::get('/{moduleSection}/{moduleGroup}/{module}/import-errors', App\Http\Livewire\ImportErrorResource::class);
});
```

### 2. Route Examples

- `/administrations/managements/sim-cards` - Sim Cards index
- `/administrations/managements/sim-cards/create` - Create new Sim Card
- `/administrations/managements/sim-cards/1/edit` - Edit Sim Card with ID 1
- `/administrations/managements/sim-cards/1/show` - View Sim Card with ID 1
- `/administrations/managements/sim-cards/import` - Import Sim Cards

---

## Formation Classes

### 1. Formation Class Structure

Each module has a corresponding Formation class that defines its behavior:

```php
<?php

namespace App\Formation\Administrations\Managements;

use App\Actions\Formation\Formation;
use App\Models\SimCard;

class SimCardFormation
{
    public static $model = 'App\Models\SimCard';

    public static function index(Object $object): Index
    {
        return Formation::createIndex('index', function (Index $index) use ($object) {
            // Index configuration
        });
    }

    public static function form(Object $object): Form
    {
        return Formation::createForm('form', function (Form $form) use ($object) {
            // Form configuration
        });
    }
}
```

### 2. Index Configuration

```php
public static function index(Object $object): Index
{
    return Formation::createIndex('index', function (Index $index) use ($object) {
        $index
            ->select(function (Select $select) {
                $select->field('id')->hide();
                $select->field('iccid')->sortable()->highlight();
                $select->field('delivery_status')->sortable()->localize();
                $select->field('contact_number');
                $select->field('created_at')->sortable()->display('md')->sortByDefault('desc');
            })
            ->export(function (Export $export) {
                $export->field('id');
                $export->field('iccid');
                $export->field('delivery_status');
            })
            ->search(function (Search $search) {
                $search->field('iccid');
                $search->field('contact_number');
            })
            ->filter(function (Filter $filter) {
                $filter->text('iccid')->operator('like');
                $filter->select('delivery_status')->operator('=')->debounce();
            })
            ->guard(function (Guard $guard) use ($object) {
                $guard->field('user_id')->operator('=')->value(Auth::id());
            })
            ->action(function (Action $action) {
                $action->operation('create');
                $action->operation('import');
                $action->operation('export');
                $action->operation('bulkDelete')->danger();
            })
            ->itemAction(function (ItemAction $itemAction) {
                $itemAction->operation('show')->rowClickable();
                $itemAction->operation('edit');
                $itemAction->operation('delete')->danger();
            });
    });
}
```

### 3. Form Configuration

```php
public static function form(Object $object): Form
{
    return Formation::createForm('form', function (Form $form) use ($object) {
        $form->create('tab')->group($object, $tabCount, function (Tab $tab) use ($object) {
            if($object->type == 'show') {
                static::getShowCard($object, $tab);
            } elseif($object->type == 'edit') {
                static::getEditCard($object, $tab);
            } else {
                static::getCreateCard($object, $tab);
            }
        });
    });
}
```

---

## UI Components & Views

### 1. Main Layout

The application uses a consistent layout structure:

```php
// resources/views/layouts/app.blade.php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Meta tags, CSS, JS -->
</head>
<body class="font-sans antialiased">
    <div x-data="{ isSideNavigationMenuOpen: false }" class="h-screen flex overflow-hidden bg-gray-100">
        @include('side-navigation-menu')
        
        <div class="flex-1 overflow-auto focus:outline-none" tabindex="0">
            @include('top-navigation-menu')
            
            <main class="pb-8 z-0">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
```

### 2. Navigation Menu

The side navigation is dynamically generated based on permissions:

```php
// resources/views/side-navigation-menu-item.blade.php
<x-side-navigation-menu.module-section name="administrations">
    <x-side-navigation-menu.module-group sectionname="administrations" name="managements">
        <x-side-navigation-menu.module sectionname="administrations" groupname="managements" name="sim-cards" />
        <x-side-navigation-menu.module sectionname="administrations" groupname="managements" name="follow-ups" />
    </x-side-navigation-menu.module-group>

    @canany(['administrations.messagings.receiver-groups:show', 'administrations.messagings.messages:show'])
        <x-side-navigation-menu.module-group sectionname="administrations" name="messagings">
            @can('administrations.messagings.messages:show')
                <x-side-navigation-menu.module sectionname="administrations" groupname="messagings" name="messages" />
            @endcan
        </x-side-navigation-menu.module-group>
    @endcanany
</x-side-navigation-menu.module-section>
```

### 3. Index View

The index view provides a comprehensive data table interface:

```php
// resources/views/formation/index.blade.php
<div {{ $poll?'wire:poll.'.$poll:'' }}>
    <!-- Header with title and description -->
    <div class="bg-white shadow">
        <div class="px-4 max-w-7xl mx-auto sm:px-6 lg:px-8 relative">
            <div class="py-6 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 capitalize">
                        {{ ucwords(__($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$module)) }}
                    </h1>
                    <div class="text-sm text-gray-500 font-medium">
                        {{ __($moduleSection.'/'.$moduleGroup.'/'.$module.'.module_description') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search, Filter, and Actions -->
    <div class="max-w-7xl mx-auto mt-8 px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Search and filter controls -->
        <!-- Data table -->
        <!-- Pagination -->
    </div>
</div>
```

### 4. Form View

The form view provides dynamic form generation:

```php
// resources/views/formation/form.blade.php
<div>
    <!-- Header -->
    <div class="bg-white shadow">
        <!-- Title and tabs -->
    </div>

    <!-- Form -->
    <div class="max-w-7xl mx-auto mt-8 px-4 sm:px-6 lg:px-8">
        <form wire:submit.prevent="save()">
            <div class="space-y-6 lg:col-span-9">
                @foreach($form->items as $tab)
                    @foreach($tab->items as $card)
                        @foreach($card->items as $section)
                            @foreach($section->items as $column)
                                @foreach($column->items as $field)
                                    <!-- Dynamic field rendering -->
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            </div>
        </form>
    </div>
</div>
```

---

## Folder Structure & Naming Conventions

### 1. Application Structure

```
app/
├── Actions/                    # Action classes for business logic
│   ├── Formation/             # Formation-specific actions
│   ├── API/                   # API integration actions
│   └── [Module]/              # Module-specific actions
├── Formation/                 # Formation classes
│   └── [Section]/             # Module sections
│       └── [Group]/           # Module groups
│           └── [Module]Formation.php
├── Http/
│   ├── Livewire/              # Livewire components
│   └── Controllers/           # Controllers
├── Models/                    # Eloquent models
├── Policies/                  # Authorization policies
└── packages/                  # Local packages
    └── formation/             # Formation package
```

### 2. Views Structure

```
resources/views/
├── layouts/                   # Layout templates
├── formation/                 # Formation package views
├── administrations/           # Module-specific views
│   ├── managements/
│   │   └── sim-cards/
│   └── messagings/
│       └── messages/
└── components/                # Reusable components
```

### 3. Language Files

```
lang/en/
├── main.php                   # General translations
├── menu.php                   # Navigation translations
└── administrations/           # Module-specific translations
    ├── managements/
    │   └── sim-cards.php
    └── messagings/
        └── messages.php
```

### 4. Naming Conventions

- **Formation Classes**: `[Module]Formation.php` (e.g., `SimCardFormation.php`)
- **Action Classes**: `[Module][Action]Action.php` (e.g., `SimCardSaveAction.php`)
- **Policy Classes**: `[Model]Policy.php` (e.g., `SimCardPolicy.php`)
- **Livewire Components**: `[Module]Resource.php` (e.g., `SimCardResource.php`)
- **Views**: `[module].blade.php` (e.g., `sim-cards.blade.php`)
- **Language Files**: `[module].php` (e.g., `sim-cards.php`)

---

## Permission System

### 1. Permission Configuration

Permissions are defined in the configuration file:

```php
// config/permission.php
'modules' => [
    'administrations.managements.users:show,create,edit,delete',
    'administrations.managements.sim-cards:show,create,edit,delete',
    'administrations.messagings.messages:show,create,edit,delete',
    'administrations.messagings.message-medias:show,create,edit,delete',
],
```

### 2. Policy Implementation

```php
<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('administrations.messagings.messages:show');
    }

    public function view(User $user, Message $message): bool
    {
        if($user->hasPermissionTo('administrations.messagings.messages:show')) {
            return $message->user_id == $user->id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('administrations.messagings.messages:create');
    }

    public function update(User $user, Message $message): bool
    {
        if($user->hasPermissionTo('administrations.messagings.messages:edit')) {
            return $message->user_id == $user->id;
        }
        return false;
    }

    public function delete(User $user, Message $message): bool
    {
        if($user->hasPermissionTo('administrations.messagings.messages:delete')) {
            return $message->user_id == $user->id;
        }
        return false;
    }
}
```

### 3. Permission Usage in Views

```php
@can('administrations.messagings.messages:show')
    <x-side-navigation-menu.module sectionname="administrations" groupname="messagings" name="messages" />
@endcan

@can('administrations.messagings.messages:create')
    <button wire:click="create">Create Message</button>
@endcan
```

---

## Data Table Features

### 1. Field Configuration

```php
->select(function (Select $select) {
    $select->field('id')->hide();                                    // Hidden field
    $select->field('iccid')->sortable()->highlight();                // Sortable and highlighted
    $select->field('delivery_status')->sortable()->localize();       // Sortable with localization
    $select->field('contact_number');                                // Basic field
    $select->field('created_at')->sortable()->display('md')->sortByDefault('desc'); // Sortable with responsive display
})
```

### 2. Search Configuration

```php
->search(function (Search $search) {
    $search->field('iccid');           // Search in ICCID field
    $search->field('contact_number');  // Search in contact number field
    $search->field('tracking_number'); // Search in tracking number field
})
```

### 3. Filter Configuration

```php
->filter(function (Filter $filter) {
    $filter->text('iccid')->operator('like');                    // Text filter with LIKE operator
    $filter->select('delivery_status')->operator('=')->debounce()->group(function (Field $field) {
        $field->option('', '');
        foreach(SimCard::DELIVERY_STATUSES as $status) {
            $field->option($status, __('administrations/managements/sim-cards.'.$status));
        }
    });
    $filter->date('created_at','from_created_at')->operator('>=')->debounce(); // Date range filter
})
```

### 4. Export Configuration

```php
->export(function (Export $export) {
    $export->field('id');
    $export->field('iccid');
    $export->field('delivery_status');
    $export->field('contact_number');
    $export->field('created_at');
})
```

### 5. Import Configuration

```php
->import(function (Import $import) {
    $import->chunkSize(350);           // Process in chunks of 350
    $import->field('iccid');
    $import->field('tracking_number');
    $import->field('delivery_status');
    $import->field('contact_number');
})
```

---

## Form Builder

### 1. Field Types

The form builder supports various field types:

```php
// Text fields
$column->text('name')->span(1)->autofocus();
$column->text('email')->type('email');
$column->text('phone')->type('phoneNumber');

// Select fields
$column->select('status')->span(1)->group(function (Field $field) {
    $field->option('', '');
    $field->option('active', 'Active');
    $field->option('inactive', 'Inactive');
});

// Radio buttons
$column->radio('require_scheduling')->span(1)->group(function (Field $field) {
    $field->option('no', 'No');
    $field->option('yes', 'Yes');
});

// Checkbox multiple
$column->checkboxMultiple('permissions')->span(2)->group(function (Field $field) {
    foreach($permissions as $permission) {
        $field->option($permission->name, $permission->name);
    }
});

// Textarea
$column->textArea('content')->span(2)->height(100);

// File upload
$column->file('attachment')->span(2)->help('Accepted formats: PNG, JPG, JPEG, GIF, MP4, MOV, AVI');

// Date/Time
$column->datetime('schedule_datetime')->span(1);
```

### 2. Field Configuration

```php
$column->text('name')->span(1)->autofocus()->group(function (Field $field) {
    $field->rules(['required', 'max:100']);                    // Validation rules
    $field->placeholder('Enter name');                         // Placeholder text
    $field->help('Enter the full name');                       // Help text
    $field->required(true);                                    // Required field
    $field->disabled(false);                                   // Disabled state
    $field->readonly(false);                                   // Readonly state
});
```

### 3. Form Layout

```php
public static function getCreateCard($object, $tab)
{
    return $tab->create('details')->description('')->group(function (Card $card) use ($object) {
        $card->create('')->column(1)->group(function (Section $section) use ($object) {
            $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                $column->text('name')->span(1)->autofocus();
                $column->text('email')->span(1);
            });
        });

        $card->create('Additional Information')->column(1)->group(function (Section $section) use ($object) {
            $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                $column->textArea('description')->span(2)->height(100);
            });
        });
    });
}
```

---

## Import/Export System

### 1. Import Process

The import system handles CSV file uploads with validation:

```php
// Import configuration
->import(function (Import $import) {
    $import->chunkSize(350);           // Process in chunks
    $import->field('iccid');           // Required fields
    $import->field('tracking_number');
    $import->field('delivery_status');
    $import->field('contact_number');
})
```

### 2. Import Validation

```php
// In Formation class
$column->text('iccid')->lazy()->span(1)->autofocus()->group(function (Field $field) use ($object) {
    if(isset($object->import)) {
        $field->rules(['required','max:18', Rule::unique('sim_cards', 'iccid')->whereNull('deleted_at')]);
    } else {
        $field->rules(['bail','required','max:18', Rule::unique('sim_cards', 'iccid')->whereNull('deleted_at')]);
    }
});
```

### 3. Export Process

```php
// Export configuration
->export(function (Export $export) {
    $export->field('id');
    $export->field('iccid');
    $export->field('delivery_status');
    $export->field('contact_number');
    $export->field('created_at');
})
```

### 4. Bulk Edit

```php
->bulkEdit(function (BulkEdit $bulkEdit) {
    $bulkEdit->chunkSize(350);
    $bulkEdit->field('tracking_number');
    $bulkEdit->field('delivery_status');
    $bulkEdit->field('contact_number');
})
```

---

## Best Practices

### 1. Formation Class Organization

- Keep Formation classes focused on a single module
- Use descriptive method names for different form types
- Separate create, edit, and show logic into different methods
- Use consistent naming conventions

### 2. Permission Management

- Define permissions in the configuration file
- Use descriptive permission names
- Implement proper authorization in policies
- Check permissions in views and components

### 3. Field Configuration

- Use appropriate field types for data
- Provide helpful validation rules
- Include user-friendly help text
- Use consistent field naming

### 4. Performance Optimization

- Use appropriate chunk sizes for imports
- Implement proper indexing for searchable fields
- Use eager loading for relationships
- Cache frequently accessed data

### 5. Code Organization

- Follow the established folder structure
- Use consistent naming conventions
- Separate business logic into action classes
- Keep views clean and focused

---

## Examples

### 1. Complete SimCard Formation

```php
<?php

namespace App\Formation\Administrations\Managements;

use App\Actions\Formation\Formation;
use App\Models\SimCard;
use Illuminate\Support\Facades\Auth;

class SimCardFormation
{
    public static $model = 'App\Models\SimCard';

    public static function index(Object $object): Index
    {
        return Formation::createIndex('index', function (Index $index) use ($object) {
            $index
                ->select(function (Select $select) {
                    $select->field('id')->hide();
                    $select->field('iccid')->sortable()->highlight();
                    $select->field('delivery_status')->sortable()->localize();
                    $select->field('contact_number');
                    $select->field('created_at')->sortable()->display('md')->sortByDefault('desc');
                })
                ->export(function (Export $export) {
                    $export->field('id');
                    $export->field('iccid');
                    $export->field('delivery_status');
                    $export->field('contact_number');
                })
                ->import(function (Import $import) {
                    $import->chunkSize(350);
                    $import->field('iccid');
                    $import->field('tracking_number');
                    $import->field('delivery_status');
                    $import->field('contact_number');
                })
                ->search(function (Search $search) {
                    $search->field('iccid');
                    $search->field('contact_number');
                })
                ->filter(function (Filter $filter) {
                    $filter->text('iccid')->operator('like');
                    $filter->select('delivery_status')->operator('=')->debounce()->group(function (Field $field) {
                        $field->option('', '');
                        foreach(SimCard::DELIVERY_STATUSES as $status) {
                            $field->option($status, __('administrations/managements/sim-cards.'.$status));
                        }
                    });
                })
                ->guard(function (Guard $guard) use ($object) {
                    $guard->field('user_id')->operator('=')->value(Auth::id());
                })
                ->action(function (Action $action) {
                    $action->operation('create');
                    $action->operation('import');
                    $action->operation('export');
                    $action->operation('bulkDelete')->danger();
                })
                ->itemAction(function (ItemAction $itemAction) {
                    $itemAction->operation('show')->rowClickable();
                    $itemAction->operation('edit');
                    $itemAction->operation('delete')->danger();
                });
        });
    }

    public static function form(Object $object): Form
    {
        return Formation::createForm('form', function (Form $form) use ($object) {
            $form->create('tab')->group($object, $tabCount, function (Tab $tab) use ($object) {
                if($object->type == 'show') {
                    static::getShowCard($object, $tab);
                } elseif($object->type == 'edit') {
                    static::getEditCard($object, $tab);
                } else {
                    static::getCreateCard($object, $tab);
                }
            });
        });
    }

    public static function getCreateCard($object, $tab)
    {
        return $tab->create('details')->description('')->group(function (Card $card) use ($object) {
            $card->create('')->column(1)->group(function (Section $section) use ($object) {
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    $column->preset('user_id')->default(Auth::id());
                    
                    $column->text('iccid')->lazy()->span(1)->autofocus()->group(function (Field $field) use ($object) {
                        $field->rules(['required','max:18', Rule::unique('sim_cards', 'iccid')->whereNull('deleted_at')]);
                    });
                    
                    $column->text('tracking_number')->span(1)->group(function (Field $field) {
                        $field->rules(['nullable','max:100']);
                    });
                    
                    $column->select('delivery_status')->span(1)->group(function (Field $field) use($object) {
                        $field->option("", "");
                        foreach(SimCard::DELIVERY_STATUSES as $status) {
                            $field->option($status, __('administrations/managements/sim-cards.'.$status));
                        }
                        $field->rules(['nullable', Rule::in(SimCard::DELIVERY_STATUSES)]);
                    });
                    
                    $column->text('contact_number')->span(1)->group(function (Field $field) {
                        $field->rules(['nullable','digits_between:10,11','starts_with:01']);
                    });
                });
            });
        });
    }
}
```

### 2. Custom Resource Component

```php
<?php

namespace App\Http\Livewire;

use App\Actions\SimCard\UpdateSimCardInBulkAction;
use App\Models\SimCard;
use Formation\DataTable\WithDataTable;
use Livewire\Component;

class SimCardResource extends Component
{
    use WithDataTable;

    protected $listeners = ['updateFolderPath', 'updateFileColumnName', 'setFiles', 'previewImage'];
    
    protected $queryString = ['itemId', 'itemType', 'sorts', 'search', 'filters', 'showFilter', 'perPage', 'type', 'formId', 'tab'];

    public $view = [
        'index' => 'administrations/managements/sim-cards.index',
        'form' => 'formation.form',
        'reorder' => 'formation.reorder',
    ];

    public $showRefreshModal = false;

    public function refresh($simCardId)
    {
        $simCard = SimCard::where('id', $simCardId)->get();
        (new UpdateSimCardInBulkAction)->execute($simCard);
        $this->notify('success', 'Successfully refreshed.');
        $this->resetPage();
    }

    public function bulkRefresh()
    {
        $this->showRefreshModal = ! $this->showRefreshModal;
    }
}
```

---

## Conclusion

The Formation Laravel Livewire package provides a comprehensive solution for building administrative interfaces with minimal code. By following the established patterns and conventions, developers can quickly create robust CRUD applications with advanced features like data tables, forms, permissions, and import/export functionality.

The package emphasizes code organization, reusability, and maintainability, making it an excellent choice for building scalable administrative applications.

---

**Documentation Version**: 1.0  
**Last Updated**: September 2025
**Author**: Faiz Nasir
