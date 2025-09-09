# Translation & Localization Guide (Formation + Spatie Translatable)

Written by: Faiz Nasir

## What this covers
- How `WithTranslation` works in Formation
- How translations are stored and retrieved (English/BM)
- App vs API behavior differences
- Required migrations and model setup for translatable fields
- How Blade/Livewire views display localized strings
- Step-by-step examples

---

## 1) The `WithTranslation` trait at a glance
File: `app/packages/formation/src/DataTable/WithTranslation.php`

Key points:
- Extends `Spatie\Translatable\HasTranslations` but overrides certain behaviors for web vs API requests.
- On every Eloquent model retrieval (retrieved event), it replaces each translatable attribute with a single-language value based on current app locale.
- For API routes (when `Request::path()` starts with `api`), it preserves the original casts/attributes behavior (typically JSON object for translated columns).

Behavior by context:
- Web (non-API): each translatable attribute becomes a plain string for the active locale (e.g. `en` or `ms`).
- API (`/api/*`): attributes remain as originally cast by Spatie (e.g. JSON object with locale map), unless you explicitly fetch a single translation.

Implications:
- Web UI shows a single language value seamlessly.
- API consumers can get the full translations map and decide on the locale client-side.

---

## 2) How locale is decided
- Current locale is read from `App::getLocale()`.
- Default app locale and fallback are configured in `config/app.php`:
  - `locale` = `en`
  - `fallback_locale` = `en`
- To support Bahasa Melayu, set locale to `ms` (commonly used code for Malay) where appropriate (e.g. middleware, user preference, URL switch, etc.).

Example runtime switch:
```php
app()->setLocale('ms'); // switch current request to Malay
```

---

## 3) Defining translatable attributes in your models
To use `WithTranslation`, your model must also be compatible with Spatie Translatable.

Minimal example model:
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Formation\DataTable\WithTranslation; // includes HasTranslations internally

class Product extends Model
{
    use WithTranslation; // pulls in Spatie HasTranslations with Formation behavior

    protected $fillable = ['name', 'description'];

    // Declare which columns are translatable (MUST be json columns in DB)
    public $translatable = ['name', 'description'];
}
```

Notes:
- If you use Spatie HasTranslations directly, you can also do that; `WithTranslation` reuses it and adds the web/API split behavior.
- If you don’t want the web/API split, you can use Spatie’s trait alone.

---

## 4) Database migrations (required changes)
Translatable columns must be JSON (or text storing JSON) so Spatie can store `{ "en": "...", "ms": "..." }`.

Example migration for a model with `name` and `description` as translatable:
```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // translatable
            $table->json('description')->nullable(); // translatable
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

What to check in your app:
- There are currently no migrations using `json(` under `database/migrations` (based on scan). If you plan to translate existing text/varchar columns, you’ll need a migration to convert them to `json` (or add new `json` columns and migrate data).
- For existing modules that need localization (EN/BM), confirm the DB schema supports storing JSON per column you want translated.

Converting an existing string column to JSON (example):
```php
Schema::table('products', function (Blueprint $table) {
    $table->json('name')->change();
});
```
Be mindful of existing data; perform data backfill to JSON format if necessary, e.g. `{"en": "old value"}`.

---

## 5) Seeding / Saving translations
Spatie provides helpers to set/get translations per locale.

Example seeding:
```php
$product = new Product();
$product->setTranslations('name', [
    'en' => 'Internet Plan',
    'ms' => 'Pelan Internet',
]);
$product->setTranslations('description', [
    'en' => 'High-speed 5G data.',
    'ms' => 'Data 5G berkelajuan tinggi.',
]);
$product->save();
```

Example updating a single locale:
```php
$product->setTranslation('name', 'ms', 'Pelan Internet');
$product->save();
```

Reading in web (non-API) context with `WithTranslation`:
```php
app()->setLocale('ms');
$name = $product->name; // returns 'Pelan Internet'
```

Reading in API context:
```php
// Inside routes that start with /api
// $product->name returns the full JSON map, e.g. ['en' => 'Internet Plan', 'ms' => 'Pelan Internet']
```

---

## 6) Using translations in Blade/Livewire
Two parallel translation mechanisms exist:

A) Model attribute translations (Spatie) — covered above.
- Ideal for data stored in DB needing multiple languages.

B) Interface string translations (Laravel lang files)
- Your app already uses `lang/en/...` files and `__()` helpers in views such as:
  - `resources/views/formation/index.blade.php`
  - `resources/views/formation/form.blade.php`
- To support BM, create corresponding `lang/ms/...` files mirroring the `en` structure.

Example:
```
lang/
  en/administrations/managements/sim-cards.php
  ms/administrations/managements/sim-cards.php  // add this
```

Usage:
```php
{{ __('administrations/managements/sim-cards.sim-cards') }}
```
Laravel will pick the string based on `App::getLocale()`.

---

## 7) Where Formation uses translations already
- In list/index rendering (`WithDataTable`), it detects translatable model attributes using:
  - `in_array(\Spatie\Translatable\HasTranslations::class, class_uses_recursive($model::class))`
  - If a field is translatable, it reads a specific locale value for display.
- In views, many labels call `__($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$key)` which maps to lang files under `lang/en` (and should be mirrored in `lang/ms`).

---

## 8) Enabling Bahasa Melayu (BM)
Steps:
1) Add `ms` language files mirroring existing `en` files under `lang/`.
2) Ensure models that need localized content use `WithTranslation` (or Spatie `HasTranslations`) and declare `$translatable` properties.
3) Ensure DB columns for those attributes use `json` type.
4) Provide translations for DB records:
   - Use `setTranslations` / `setTranslation` when saving or via a migration/seed.
5) Switch locale to `ms` for BM user sessions:
   - Middleware, user preference, or route toggle
   - `app()->setLocale('ms')`

Optional: add a language switcher in UI to toggle `en`/`ms`.

---

## 9) API vs Web — what your clients see
- Web: users see a single-language value, thanks to `WithTranslation` auto-resolving using current locale.
- API: clients receive the full translation map (e.g. `{"en":"...","ms":"..."}`) so they can render their own locale.

This split is handled internally by checking `Request::path()` starts with `api`.

---

## 10) Common pitfalls & tips
- Forgetting to convert string/varchar columns to `json`: Spatie will not be able to store language maps.
- Not declaring `$translatable` on the model: translations won’t be handled and attributes return as-is.
- Missing BM lang files: UI labels will fall back to `en`.
- In API responses, don’t expect single strings for translatable columns unless you explicitly transform them.
- When bulk-importing translations, ensure JSON is valid and includes both `en` and `ms` keys where required.

---

## 11) Quick checklist
- Model uses `WithTranslation` (or `HasTranslations`).
- `$translatable = [...]` defined.
- DB columns are `json`.
- `lang/en/...` exists and `lang/ms/...` added.
- Locale switching implemented (middleware or per-request) as needed.
- Seeders or admin forms set translations for `en` and `ms`.

---

## 12) Minimal working example
Model:
```php
class Page extends Model
{
    use \Formation\DataTable\WithTranslation;

    protected $fillable = ['title', 'body'];
    public $translatable = ['title', 'body'];
}
```

Migration:
```php
Schema::create('pages', function (Blueprint $table) {
    $table->id();
    $table->json('title');
    $table->json('body');
    $table->timestamps();
});
```

Seed:
```php
$page = new Page();
$page->setTranslations('title', ['en' => 'About Us', 'ms' => 'Tentang Kami']);
$page->setTranslations('body', ['en' => 'Company info', 'ms' => 'Maklumat syarikat']);
$page->save();
```

Usage (web):
```php
app()->setLocale('ms');
$page->title; // Tentang Kami
```

Usage (api route):
```php
$page->title; // {"en":"About Us","ms":"Tentang Kami"}
```
