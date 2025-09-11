<?php

namespace App\Formation\Ecommerce\Managements;

use App\Actions\Formation\Form\Card;
use App\Actions\Formation\Form\Column;
use App\Actions\Formation\Form\Field;
use App\Actions\Formation\Form\Form;
use App\Actions\Formation\Form\Section;
use App\Actions\Formation\Form\Tab;
use App\Actions\Formation\Formation;

use App\Actions\Formation\Index\Index;
use App\Actions\Formation\Index\Select;
use App\Actions\Formation\Index\Export;
use App\Actions\Formation\Index\Import;
use App\Actions\Formation\Index\Search;
use App\Actions\Formation\Index\Filter;
use App\Actions\Formation\Index\Action;
use App\Actions\Formation\Index\ItemAction;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductFormation
{
    public static $model = 'App\Models\Product';

    public static function index(Object $object): Index
    {
        return Formation::createIndex('index', function (Index $index) use ($object) {
            $index
                ->select(function (Select $select) {
                    $select->field('id')->hide();
                    $select->field('name')->sortable()->highlight()->lang(['en']);
                    $select->field('sku')->sortable();
                    $select->field('category_id')->with('category')->reference('name')->sortable()->lang(['en']);
                    $select->field('price')->sortable()->display('currency');
                    $select->field('stock')->sortable();
                    $select->field('image')->hide(); // Include image field for catalog display
                    $select->field('description')->hide(); // Include description for catalog display
                    $select->field('status')->sortable()->display('badge');
                    $select->field('created_at')->sortable()->display('md')->sortByDefault('desc');
                })
                ->import(function (Import $import){
                    // // Get active product categories for validation
                    // $activeCategories = ProductCategory::where('status', 'active')->pluck('id')->toArray();
                    
                    // // Basic product information
                    // $import->label('name')->rules(['required', 'string', 'max:255']);
                    // $import->label('sku')->rules(['required', 'string', 'max:64', 'unique:products,sku']);
                    // $import->label('slug')->rules(['required', 'string', 'max:220', 'unique:products,slug']);
                    
                    // // Category validation with active categories only
                    // $import->label('category_id')->rules(['nullable', 'exists:product_categories,id', 'in:'.implode(',', $activeCategories)]);
                    
                    // // Pricing and inventory
                    // $import->label('price')->rules(['required', 'numeric', 'min:0']);
                    // $import->label('stock')->rules(['required', 'integer', 'min:0']);
                    
                    // // Product details
                    // $import->label('description')->rules(['nullable', 'string']);
                    // $import->label('image')->rules(['nullable', 'string', 'max:500']); // Image path/URL
                    
                    // // Status with validation
                    // $import->label('status')->rules(['required', 'in:active,inactive']);
                    
                    // // Preset values for new products
                    // $import->label('created_by')->preset(Auth::id());
                    // $import->label('updated_by')->preset(Auth::id());
                    
                    // // Sample file for import template
                    // $import->sampleFile('samples/sample-product-import.csv');

                    $import->field('id');
                    $import->field('name');
                    $import->field('sku');
                    $import->field('slug');
                    $import->field('price');
                    $import->field('stock');
                    $import->field('description');
                    $import->field('status');
                })
                
                ->export(function (Export $export) {
                    $export->field('id');
                    $export->field('name');
                    $export->field('sku');
                    $export->field('category_id')->with('category')->reference('name');
                    $export->field('price');
                    $export->field('stock');
                    $export->field('image');
                    $export->field('description');
                    $export->field('status');
                    $export->field('created_at');
                })
                ->search(function (Search $search) {
                    $search->field('name')->json();
                    $search->field('sku');
                    $search->field('description')->json();
                })
                ->filter(function (Filter $filter) {
                    $filter->text('name')->operator('like');
                    $filter->text('sku')->operator('like');
                    $filter->select('category_id')->operator('=')->debounce()->group(function (Field $field) {
                        $field->option('', '');
                        foreach(ProductCategory::all() as $category) {
                            $field->option($category->id, $category->name);
                        }
                    });
                    $filter->select('status')->operator('=')->debounce()->group(function (Field $field) {
                        $field->option('', '');
                        $field->option('active', 'Active');
                        $field->option('inactive', 'Inactive');
                    });
                })
                ->action(function (Action $action) {
                    $action->operation('create');
                    $action->operation('export');
                    $action->operation('import');
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
            $tabCount = 0;
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
                    $column->text('name')->span(1)->autofocus()->group(function (Field $field) {
                        $field->rules(['required']);
                        $field->lang('en');
                    });
                    
                    $column->text('slug')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'max:220', 'unique:products,slug']);
                    });
                    
                    $column->text('sku')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'max:64', 'unique:products,sku']);
                    });
                    
                    $column->select('category_id')->span(1)->group(function (Field $field) {
                        $field->option('', '');
                        foreach(ProductCategory::all() as $category) {
                            $field->option($category->id, $category->name);
                        }
                        $field->rules(['nullable', 'exists:product_categories,id']);
                    });
                    
                    $column->select('status')->span(1)->group(function (Field $field) {
                        $field->option('active', 'Active');
                        $field->option('inactive', 'Inactive');
                        $field->rules(['required', 'in:active,inactive']);
                        $field->value(function () { return 'active'; });
                    });
                    
                    $column->textarea('description')->span(2)->group(function (Field $field) {
                        $field->rules(['nullable']);
                        $field->lang('en');
                    });
                    
                    $column->number('price')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'numeric', 'min:0']);
                    });
                    
                    $column->number('stock')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'integer', 'min:0']);
                    });
                    
                    // Product main image (S3)
                    $column->file('image')->folderPath('products/')->span(2)->group(function (Field $field) {
                        $field->rules(['nullable', 'mimes:jpeg,bmp,png,gif,svg', 'max:5120']);
                    });

                    $column->preset('created_by')->default(Auth::id());
                });
            });
        });
    }

    public static function getEditCard($object, $tab)
    {
        return $tab->create('details')->description('')->group(function (Card $card) use ($object) {
            $card->create('')->column(1)->group(function (Section $section) use ($object) {
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    $column->text('name')->span(1)->autofocus()->group(function (Field $field) {
                        $field->rules(['required']);
                        $field->lang('en');
                    });
                    
                    $column->text('slug')->span(1)->group(function (Field $field) use ($object) {
                        $field->rules(['required', 'max:220', 'unique:products,slug,' . $object->formId]);
                    });
                    
                    $column->text('sku')->span(1)->group(function (Field $field) use ($object) {
                        $field->rules(['required', 'max:64', 'unique:products,sku,' . $object->formId]);
                    });
                    
                    $column->select('category_id')->span(1)->group(function (Field $field) {
                        $field->option('', '');
                        foreach(ProductCategory::all() as $category) {
                            $field->option($category->id, $category->name);
                        }
                        $field->rules(['nullable', 'exists:product_categories,id']);
                    });
                    
                    $column->select('status')->span(1)->group(function (Field $field) {
                        $field->option('active', 'Active');
                        $field->option('inactive', 'Inactive');
                        $field->rules(['required', 'in:active,inactive']);
                    });
                    
                    $column->textarea('description')->span(2)->group(function (Field $field) {
                        $field->rules(['nullable']);
                        $field->lang('en');
                    });
                    
                    $column->number('price')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'numeric', 'min:0']);
                    });
                    
                    $column->number('stock')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'integer', 'min:0']);
                    });
                    // Product main image (S3)
                    $column->file('image')->folderPath('products/')->span(2)->group(function (Field $field) {
                        $field->rules(['nullable', 'mimes:jpeg,bmp,png,gif,svg', 'max:5120']);
                    });
                });
            });
        });
    }

    public static function getShowCard($object, $tab)
    {
        return $tab->create('details')->description('')->group(function (Card $card) use ($object) {
            $card->create('')->column(1)->group(function (Section $section) use ($object) {
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    $column->displayText('name')->span(1);
                    $column->displayText('slug')->span(1);
                    $column->displayText('sku')->span(1);
                    $column->displayText('category_id')->with('category')->reference('name')->span(1);
                    $column->displayText('description')->span(2);
                    $column->displayText('price')->span(1)->display('currency');
                    $column->displayText('stock')->span(1);
                    $column->displayText('status')->span(1)->display('badge');
                    $column->file('image')->folderPath('products/')->span(2);
                    $column->displayText('created_at')->span(1);
                });
            });
        });
    }
}
