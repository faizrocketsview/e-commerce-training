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
use App\Actions\Formation\Index\Search;
use App\Actions\Formation\Index\Filter;
use App\Actions\Formation\Index\Action;
use App\Actions\Formation\Index\ItemAction;

use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;

class ProductCategoryFormation
{
    public static $model = 'App\Models\ProductCategory';

    public static function index(Object $object): Index
    {
        return Formation::createIndex('index', function (Index $index) use ($object) {
            $index
                ->select(function (Select $select) {
                    $select->field('id')->hide();
                    $select->field('name')->sortable()->highlight();
                    $select->field('slug')->sortable();
                    $select->field('parent_id')->with('parent')->reference('name')->sortable();
                    $select->field('created_at')->sortable()->display('md')->sortByDefault('desc');
                })
                ->export(function (Export $export) {
                    $export->field('id');
                    $export->field('name');
                    $export->field('slug');
                    $export->field('parent_id')->with('parent')->reference('name');
                    $export->field('created_at');
                })
                ->search(function (Search $search) {
                    $search->field('name');
                    $search->field('slug');
                })
                ->filter(function (Filter $filter) {
                    $filter->text('name')->operator('like');
                    $filter->text('slug')->operator('like');
                    
                })
                ->action(function (Action $action) {
                    $action->operation('create');
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
                        $field->rules(['required', 'max:150']);
                    });
                    
                    $column->text('slug')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'max:160', 'unique:product_categories,slug']);
                    });
                    
                    $column->select('parent_id')->span(1)->group(function (Field $field) {
                        $field->option('', '');
                        foreach(ProductCategory::all() as $category) {
                            $field->option($category->id, $category->name);
                        }
                        $field->rules(['nullable', 'exists:product_categories,id']);
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
                        $field->rules(['required', 'max:150']);
                    });
                    
                    $column->text('slug')->span(1)->group(function (Field $field) use ($object) {
                        $field->rules(['required', 'max:160', 'unique:product_categories,slug,' . $object->formId]);
                    });
                    
                    $column->select('parent_id')->span(1)->group(function (Field $field) use ($object) {
                        $field->option('', '');
                        foreach(ProductCategory::where('id', '!=', $object->formId)->get() as $category) {
                            $field->option($category->id, $category->name);
                        }
                        $field->rules(['nullable', 'exists:product_categories,id']);
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
                    $column->displayText('parent_id')->with('parent')->reference('name')->span(1);
                    $column->displayText('created_at')->span(1);
                });
            });
        });
    }
}
