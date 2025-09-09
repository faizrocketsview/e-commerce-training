# Formation Laravel Package Documentation - for Faiz's Reference

## Overview
Formation is a Laravel package developed by your company to rapidly build CRUD (Create, Read, Update, Delete) applications. It provides ready-to-use components, actions, models, and views for resource management, forms, validation, and advanced data tables.

---

## Key Concepts

### 1. Resources
- **Resource**: Any entity you want to manage (e.g., User, Product).
- **CRUD**: Formation automates listing, creating, updating, and deleting resources.

### 2. Index
- **Purpose**: Displays a list of resources with filtering, searching, sorting, and pagination.
- **Location**: `resources/views/formation/index.blade.php`
- **Traits**: Use `WithDataTable`, `WithFiltering`, `WithSorting`, etc. from `src/DataTable/`.

### 3. Form
- **Purpose**: Handles resource creation and editing.
- **Location**: `resources/views/formation/form.blade.php`
- **Fields**: Defined in `src/Form/` (e.g., `Textbox`, `Field`, `Option`).
- **Validation**: Add rules in form configuration or action classes.

### 4. Actions
- **Purpose**: Encapsulate business logic for CRUD operations.
- **Location**: `src/Actions/Formation/`
- **Examples**: `SaveAction.php`, `DestroyAction.php`, `FilterAction.php`

### 5. Models
- **Purpose**: Represent your data entities.
- **Location**: `src/Models/` (e.g., `Import.php`, `BulkEdit.php`)

### 6. Livewire Components
- **Purpose**: Main entry points for UI and CRUD logic.
- **Location**: `src/Http/Livewire/`
- **Usage**: Route to these components for resource management.

### 7. Views
- **Purpose**: Blade templates for UI.
- **Location**: `resources/views/formation/`
- **Examples**: `index.blade.php`, `form.blade.php`, `import.blade.php`, `layouts/app.blade.php`

### 8. Traits
- **Purpose**: Add reusable features to components.
- **Location**: `src/DataTable/`
- **Examples**: `WithDataTable`, `WithFiltering`, `WithImport`, `WithSorting`, `WithTranslation`

---

## Getting Started

### 1. Installation
- Add the package to your Laravel project :
  - Add to `composer.json`:
    ```json
    "repositories": [
        {
            "type": "path",
            "url": "packages/formation",
            "options": { "symlink": false }
        }
    ]
    "require": {
        "leekhengteck/formation": "dev-main"
    }
    ```
  - Run:
    ```bash
    composer require leekhengteck/formation:dev-main
    ```

### 2. Publish Assets
Run these commands to publish package assets:
```bash
php artisan vendor:publish --provider="Formation\FormationServiceProvider" --tag="lang"
php artisan vendor:publish --provider="Formation\FormationServiceProvider" --tag="livewire"
php artisan vendor:publish --provider="Formation\FormationServiceProvider" --tag="controllers"
php artisan vendor:publish --provider="Formation\FormationServiceProvider" --tag="actions"
php artisan vendor:publish --provider="Formation\FormationServiceProvider" --tag="views" --force
php artisan vendor:publish --provider="Formation\FormationServiceProvider" --tag="migrations"
php artisan vendor:publish --provider="Formation\FormationServiceProvider" --tag="models"
php artisan vendor:publish --provider="Formation\FormationServiceProvider" --tag="formation"
php artisan vendor:publish --provider="Formation\FormationServiceProvider" --tag="images"
```

### 3. Build Frontend Assets
```bash
npm run build
```

### 4. Run Migrations
```bash
php artisan migrate
```

---

## Usage

### 1. Routing
Add routes in `routes/web.php`:
```php
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/{moduleSection}/{moduleGroup}/{module}', App\Http\Livewire\Resource::class);
    Route::get('/{moduleSection}/{moduleGroup}/{module}/import', App\Http\Livewire\ImportResource::class);
    Route::get('/{moduleSection}/{moduleGroup}/{module}/import-errors', App\Http\Livewire\ImportErrorResource::class);
});
```

### 2. Creating a Resource
- Create a model in `app/Models` or use Formation’s model.
- Create a Livewire component in `app/Http/Livewire` or use Formation’s.
- Configure your resource’s index and form using provided traits and views.
- Add business logic in `src/Actions/Formation/`.

### 3. DataTable Features
- Use traits like `WithDataTable` for sorting, searching, bulk actions, etc.
- Configure columns, filters, and actions in your Livewire component.

### 4. Form & Validation
- Define fields in your form component or Blade view.
- Add validation rules in the form or action class.

---

## Example: Setting Up a CRUD Resource
1. **Model**: Create `app/Models/Product.php`.
2. **Livewire Component**: Create `app/Http/Livewire/ProductResource.php`.
3. **Route**:
    ```php
    Route::get('/admin/resources/products', App\Http\Livewire\ProductResource::class);
    ```
4. **Views**: Use or customize `resources/views/formation/index.blade.php` and `form.blade.php`.
5. **Actions**: Add logic in `src/Actions/Formation/` if needed.

---

## Advanced Features for This Package
- **Import/Export**: Use provided import/export actions and views.
- **Bulk Edit**: Use `BulkEdit` model and actions for mass updates.
- **Custom Actions**: Extend or override actions for custom business logic.
- **Multi-language**: Use language files in `resources/lang/en/`.

---

## Folder Structure Reference
- `src/FormationServiceProvider.php`: Registers the package.
- `src/Http/Livewire/`: Main Livewire components.
- `src/Actions/Formation/`: Business logic actions.
- `src/Models/`: Data models.
- `resources/views/formation/`: Blade views for UI.
- `src/DataTable/`: Traits for data table features.
- `src/Form/`: Form field and validation classes.

---

## Notes
- Always use Formation naming conventions for controllers and actions.
- Use provided traits for rapid development.
- Customize views and actions as needed for business logic.

---

WROTE BY: FAIZ NASIR
