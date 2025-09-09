Laravel Development Best Practices
IT Department Guideline
Prepared for Winner Venture Sdn. Bhd. & Rocketsview Management Sdn. Bhd.Confidential | Version 1.0 | Last Updated: September 06, 2025

Table of Contents

Introduction
Spatie Laravel Translatable Package
Overview
Problems Addressed
Objectives
Scope
Installation Guidelines
Component Usage


Laravel Migration Best Practices
Overview
Problems Addressed
Objectives
Scope
Process
Guidelines


Laravel Model Best Practices
Overview
Problems Addressed
Objectives
Scope
Guidelines


Spatie Laravel-Permission Package
Overview
Why Use Spatie Laravel-Permission
Installation Guidelines
Configuration
Database Tables
Basic Usage
Advanced Usage
Custom Commands


Sitemap Guidance
Overview
Problems Addressed
Objectives
Guidelines
Sitemap Template


Policy Guidelines
Overview
Problems Addressed
Objectives
Scope
Background
Process
Guidelines


Conclusion


Introduction
This document serves as a standardized guide for Laravel development within the IT department of Winner Venture Sdn. Bhd. and Rocketsview Management Sdn. Bhd. It consolidates best practices for using the Spatie Laravel Translatable Package, Laravel Migrations, Models, Spatie Laravel-Permission, Sitemap Guidance, and Policy Guidelines. The goal is to ensure consistency, scalability, and maintainability across all Laravel projects, providing a reference for developers and tools like Cursor AI.

Spatie Laravel Translatable Package
Overview
The Spatie Laravel Translatable Package provides a uniform solution for integrating multiple languages into Laravel projects, streamlining locale management and translatable column handling.
Problems Addressed

Cumbersome Locale Addition: Adding new locales requires manual configuration in multiple places.
Navigation Difficulty: Interacting with modules containing multiple language columns is challenging.
Inconsistent Implementation: Lack of standardized approach for multilingual integration.

Objectives

Simplify adding new locales to the system.
Enhance user experience when navigating multilingual modules.
Provide an eloquent approach for handling translatable columns.

Scope

Applicable to all Laravel projects within the organization.

Installation Guidelines

Install the Package:composer require spatie/laravel-translatable:6.9.0


Verify Trait File:
Ensure the WithTranslation.php file exists in \Formation\DataTable\WithTranslation.php.


Update Model:
Add the WithTranslation trait to your model.
Define translatable columns in the $translatable array.

use Formation\DataTable\WithTranslation;
use WithTranslation;

class YourModel extends Model
{
    use WithTranslation;

    public $translatable = ['title', 'description'];
}


Database Schema:
Modify translatable columns to use json type in the database.

Schema::create('your_table', function (Blueprint $table) {
    $table->json('title');
    $table->json('description');
});


Add Functions:
lang($locale): Used for Select, Export, and Column (text/textarea) classes to specify the language.
json(): Used for Search class to indicate a JSON column for proper querying.


Add Filter Operator:
Use ->operator('json') in the Filter class for JSON-based filtering.



Component Usage



Component
Example
Description



lang()
$export->field('title', 'title_en')->lang('en');$column->text('title')->lang('en')->rules(['required']);$column->text('title')->lang('ms')->rules(['nullable']);$column->textarea('description')->lang('en');
Specifies the language for Select, Export, and Column (text/textarea) classes. Takes a locale string (e.g., 'en', 'ms').


json()
$search->field('title')->json();
Notifies the backend that the column is JSON for proper querying. Used in Search class.


json operator
$filter->text('title')->operator('json')->debounce();
Indicates a JSON column for filtering. Used in Filter class.



Laravel Migration Best Practices
Overview
Laravel migrations define the database schema in a structured, version-controlled manner. This section outlines best practices to ensure consistency and clarity in table and column naming.
Problems Addressed

Inconsistent Naming: Developers use different naming conventions for tables and columns.
Unclear Naming: Table and column names do not reflect their actual usage.
Missing Default Columns: Some essential columns are not consistently created.

Objectives

Maintain consistent naming conventions for tables and columns.
Provide clear guidance on naming standards.
Ensure default columns are included in all tables.

Scope

Applies to all database table creation or amendment tasks in Laravel projects.

Process

Task Assignment: Developer receives a task involving database table creation or amendment.
Design and Review: Developer designs the table schema and gets it reviewed by the team leader.
Development: Developer implements the task.
Final Review: Team leader reviews the database changes before export.

Guidelines

Naming Standards:



Type
Standard
Examples



Table
Snake case, plural
locations, public_holidays


Pivot Table
Snake case, singular, alphabetical order
location_public_holiday


Column
Snake case, no table name prefix, full form
name, job_title


DateTime Column
Snake case, [verb]_at
created_at, reset_at


User Column
[table_name]_id, [noun]_[table_name]_id, [verb]_by
user_id, approver_user_id, approved_by


Primary Key
id
id


Foreign Key
[table_name (singular)]_id
article_id


Migration
Snake case, create_[table_name (plural)]_table or update_[table_name (plural)]
create_users_table, update_users_table



Table and Column Naming:

Align table names with module names and column names with frontend field names.
Ensure column order in the database matches frontend presentation.


Default Columns:

Include the following in all tables:Schema::create('your_table', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('created_by');
    $table->unsignedBigInteger('updated_by')->nullable();
    $table->unsignedBigInteger('deleted_by')->nullable();
    $table->timestamp('created_at')->useCurrent();
    $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
    $table->softDeletes();
    $table->string('deleted_token')->nullable();
    $table->timestamp('partition_created_at')->nullable();
});




Integer Data Types:



Type
Storage (Bytes)
Min Signed
Min Unsigned
Max Signed
Max Unsigned



tinyInteger
1
-128
0
127
255


smallInteger
2
-32768
0
32767
65535


mediumInteger
3
-8388608
0
8388607
16777215


integer
4
-2147483648
0
2147483647
4294967295


bigInteger
8
-2⁶³
0
2⁶³-1
2⁶⁴-1



Primary and Foreign Keys:

Use id() for primary keys and foreignId() for foreign keys.

$table->id();
$table->foreignId('article_id')->constrained()->onDelete('cascade');


DateTime:

Prefer timestamp() over dateTime() for UTC compatibility, indexing, and caching.

$table->timestamp('created_at')->useCurrent();


Floating-Point Types:

Avoid float() or double(). Use decimal() with specified maximum length.

$table->decimal('amount', 8, 2);


String Types:

Prefer varchar() over char() and specify maximum length.

$table->string('name', 255);




Value
CHAR(4)
Storage
VARCHAR(4)
Storage



''
'    '
4 bytes
''
1 byte


'ab'
'ab  '
4 bytes
'ab'
3 bytes


'abcd'
'abcd'
4 bytes
'abcd'
5 bytes


'abcdefgh'
'abcd'
4 bytes
'abcd'
5 bytes



Partitioning:

Apply partitioning to high-growth tables (>1M rows) with a 10-year duration.
Examples: top_up_requests, merchant_statements, stock_transactions, invoices.




Laravel Model Best Practices
Overview
Laravel Models represent database tables as objects, enabling object-oriented database interaction. This section defines standards for naming and properties.
Problems Addressed

Inconsistent Naming: Developers use varied naming conventions.
Inconsistent Properties: Common properties are not standardized.

Objectives

Define naming conventions that reflect table and relationship structures.
Standardize common model properties for ease of development.

Scope

Applies to all system development tasks involving models.

Guidelines

Class Naming:

Use singular, camel case names corresponding to table names.


Table
Model



orders
Order


daily_sales_item_reports
DailySalesItemReport


one_time_passwords
OneTimePassword





Soft Deletes:

Add SoftDeletes trait immediately after the class declaration.

use Illuminate\Database\Eloquent\SoftDeletes;

class YourModel extends Model
{
    use SoftDeletes;
}


Deleted Token:

Specify deleted_token in the $attributes array.

class YourModel extends Model
{
    protected $attributes = [
        'deleted_token' => null,
    ];
}


Constants and Enums:

Define constants for ENUM values and non-enum lists (e.g., banks, countries).

class YourModel extends Model
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
}


Partitioning:

Set partition_created_at in the boot function’s creating callback.

class YourModel extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->partition_created_at = now();
        });
    }
}


Date Serialization:

Use serializeDate to return human-readable dates.

class YourModel extends Model
{
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}


Relationships:

Define all possible relationships, even if unused.


Relationship
Standards
Example



Has One
Singular, camel case, table name
public function capitalCity() { return $this->hasOne(CapitalCity::class); }


Has Many
Plural, camel case, table name
public function comments() { return $this->hasMany(Comment::class); }


Belongs To
Singular, camel case, table name
public function video() { return $this->belongsTo(Video::class); }




class YourModel extends Model
{
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


Bidirectional Relationships:

Define relationships in both directions (except for User table).

class Post extends Model
{
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}

class Comment extends Model
{
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}




Spatie Laravel-Permission Package
Overview
The Spatie Laravel-Permission Package simplifies user role and permission management in Laravel, offering a database-driven approach to authorization.
Why Use Spatie Laravel-Permission

Scalability: Avoids bloated AuthServiceProvider::boot().
Simplicity: Eliminates the need to create policies for each module.
Flexibility: Suitable for simpler use cases compared to Laravel’s Gate & Policy.
Integration: Leverages Laravel’s built-in @can and $this->authorize.

Installation Guidelines

Install the Package:composer require spatie/laravel-permission


Publish Configuration:php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"


Clear Cache:php artisan optimize:clear


Run Migrations:php artisan migrate



Configuration

Apply HasRoles Trait:use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}


Cache Management:
Clear permission cache:app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
php artisan permission:cache-reset
php artisan cache:forget spatie.permission.cache




Error Handling:
$user->can('Do Something'): Returns true/false.
Unknown permissions throw errors.


Multiple Guards:
Be cautious with multiple guards; refer to Spatie Documentation.



Database Tables

Created during migration:
roles
role_has_permissions
permissions
model_has_permissions
model_has_roles



Basic Usage

Assigning Permissions/Roles:$user->revokePermissionTo($user->getAllPermissions());
$user->syncPermissions(['edit articles', 'delete articles']);
$user->assignRole('editor');


Checking Permissions:if ($user->hasPermissionTo('edit articles')) {
    // Allow action
}
if ($user->hasAnyPermission(['edit articles', 'delete articles'])) {
    // Allow action if user has any of the permissions
}



Advanced Usage

Multi-Tier Permissions:
Combine role-based and direct permissions:// Role: Member (CRUD on Order)
// Direct: CRUD on QR
if ($user->hasAnyPermission('Manage Order')) {
    // Allow access
}




Middleware:
Apply permissions via middleware:// Controller
public function __construct()
{
    $this->middleware('permission:edit articles');
}

// routes/web.php
Route::group(['middleware' => ['permission:edit articles']], function () {
    Route::get('/articles', [ArticleController::class, 'index']);
});




Custom Middleware:
Create custom middleware for specialized permission handling.

// app/Http/Middleware/CustomPermission.php
class CustomPermission
{
    public function handle($request, Closure $next, $permission)
    {
        if (auth()->user()->hasPermissionTo($permission)) {
            return $next($request);
        }
        abort(403, 'Unauthorized');
    }
}



Custom Commands

Generate Permissions:php artisan permission:upsert


Custom Command for Existing Users:namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class GeneratePermissions extends Command
{
    protected $signature = 'permissions:generate';

    public function handle()
    {
        $permissions = ['edit articles', 'delete articles'];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        $this->info('Permissions generated successfully.');
    }
}




Sitemap Guidance
Overview
Sitemap Guidance provides a reference for designing system structures, ensuring clarity and maintainability in module organization.
Problems Addressed

Lack of Knowledge: Developers are unsure how to create sitemaps.
Incorrect Structure: Improper base structure leads to future rework.
Module Confusion: Incorrect grouping confuses developers and users.
Unnecessary Modules: Excessive modules are created.

Objectives

Provide clear sitemap creation guidelines.
Create maintainable code.
Enhance future development and user experience.
Prevent unnecessary module creation.

Guidelines

Scope: Applies to all developers involved in system development.
Steps:
List all modules.
Identify target users for each module.
Define the purpose of each module.
Specify the associated table name.
Group modules by similar features or target users.
Use plural names for user-level grouping, module groups, and modules.



Sitemap Template

Scenario 1: Module and Table Names Match:



User Level Grouping
Module Grouping
Module
Table Name
Path



Administrations
Finances
Biz Transactions
biz_transactions
partners/self-services/biz-transactions


Administrations
Finances
Credit Transactions
credit_transactions
partners/self-services/credit-transactions


Administrations
Settings
Users
user
administrations/settings/users



Scenario 2: Module and Table Names Differ:



User Level Grouping
Module Grouping
Module
Table Name
Path



Self-services
Finances
Biz Transactions
transactions
partners/self-services/biz_transactions?filters['type']=biz


Self-services
Finances
Credit Transactions
transactions
partners/self-services/transactions?filters['type']=credit


Administrations
Settings
Employees
user
administrations/settings/employees



References:

Sample Sitemap 1
Sample Sitemap 2




Policy Guidelines
Overview
This guideline establishes a standardized approach for module access control using Laravel Policies and the Spatie Laravel-Permission Package, ensuring consistency, security, and maintainability.
Problems Addressed

Inconsistent Access Control: Varied implementation across projects.
Permission Management: Difficulty updating permissions due to lack of structure.
Team Adherence: Challenges in ensuring team members follow standards.
Data Restrictions: Developers overlook data restriction use cases.

Objectives

Provide a consistent framework for access control.
Standardize permission assignments.
Help team members adhere to guidelines.
Ensure data restriction use cases are implemented.

Scope

Covers module access control, permission management, Blade directives, and policy files in Laravel projects.

Background

Access Control: Restricts system access based on user permissions.
Permissions: Define user actions (e.g., show article, edit article).
Laravel Policy: Organizes authorization logic for models.
Spatie Laravel-Permission: Simplifies role and permission management.

Process

Define Permissions: Break down application actions.
Assign Permissions: Use Spatie to assign permissions to users.
Control UI Visibility: Use Blade directives (@can, @canany) for components.
Implement Policies: Define rules for actions and data restrictions.

Guidelines

User Model:

Add HasRoles trait:use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}




Permission Naming:

Format: [user_level_grouping].[module_grouping].[module]:[action].


User Level Grouping
Module Grouping
Module
Action
Permission



administrations
accounts
terminate-account-logs
show
administrations.accounts.terminate-account-logs:show


administrations
accounts
terminate-account-logs
create
administrations.accounts.terminate-account-logs:create





Permission Management:

List permissions in config/permission.php:return [
    'permissions' => [
        'administrations.accounts.terminate-account-logs:show',
        'administrations.accounts.terminate-account-logs:create',
    ],
];


Run:php artisan permission:upsert




Assign Permissions:

Use syncPermissions:$user->syncPermissions(['administrations.accounts.terminate-account-logs:show']);




UI Visibility:

Use Blade directives:@can('administrations.accounts.terminate-account-logs:show')
    <button>View Logs</button>
@endcan
@canany(['administrations.accounts.terminate-account-logs:show', 'administrations.accounts.terminate-account-logs:edit'])
    <button>Manage Logs</button>
@endcanany




Policy Methods:

Map methods to permissions and controller actions:


Policy Method
Permission
Controller Action



viewAny
show
index


view
show
show


create
create
create, store


update
edit
edit, update


delete
delete
destroy





Policy Implementation:

Use BasePolicy for path-based restrictions.
Restrict by module:namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Request;

class YourModelPolicy
{
    use BasePolicy;

    public function viewAny(User $user)
    {
        $path = Request::path();
        return $user->hasPermissionTo("{$path}:show");
    }
}


Restrict by data:public function view(User $user, YourModel $model)
{
    return $user->organization_id === $model->organization_id;
}






Conclusion
This documentation provides a comprehensive guide for Laravel development, covering the Spatie Translatable Package, migrations, models, permissions, sitemap guidance, and policy implementation. By adhering to these standards, developers can ensure consistency, scalability, and maintainability across projects. For further details, refer to the linked sample documents or official package documentation.