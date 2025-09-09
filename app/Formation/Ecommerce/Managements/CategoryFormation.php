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

class CategoryFormation
{
    public static $model = 'App\Models\ProductCategory';

    public static function index(Object $object): Index
    {
        return Formation::createIndex('index', function (Index $index) use ($object) {
            $index
                ->select(function (Select $select) {
                    $select->field('id')->hide();
                    $select->field('name')->sortable()->highlight();
                    $select->field('type')->sortable();
                    $select->field('status')->sortable()->localize();
                    $select->field('slug')->sortable();
                    $select->field('created_at')->sortable()->display('md')->sortByDefault('desc');
                })
                ->export(function (Export $export) {
                    $export->field('id');
                    $export->field('name');
                    $export->field('type');
                    $export->field('status');
                    $export->field('slug');
                    $export->field('created_at');
                })
                ->search(function (Search $search) {
                    $search->field('name');
                    $search->field('type');
                    $search->field('slug');
                })
                ->filter(function (Filter $filter) {
                    $filter->text('name')->operator('like');
                    $filter->text('type')->operator('like');
                    $filter->text('slug')->operator('like');
                    $filter->select('status')->operator('=')->debounce()->group(function (Field $field) {
                        $field->option('', '');
                        $field->option('active', 'Active');
                        $field->option('inactive', 'Inactive');
                    });
                    
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
                    
                    $column->text('type')->span(1)->group(function (Field $field) {
                        $field->rules(['nullable', 'max:255']);
                    });
                    
                    $column->select('status')->span(1)->group(function (Field $field) {
                        $field->option('active', 'Active');
                        $field->option('inactive', 'Inactive');
                        $field->rules(['required', 'in:active,inactive']);
                    });
                    
                    $column->text('slug')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'max:160', 'unique:product_categories,slug']);
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
                    
                    $column->text('type')->span(1)->group(function (Field $field) {
                        $field->rules(['nullable', 'max:255']);
                    });
                    
                    $column->select('status')->span(1)->group(function (Field $field) {
                        $field->option('active', 'Active');
                        $field->option('inactive', 'Inactive');
                        $field->rules(['required', 'in:active,inactive']);
                    });
                    
                    $column->text('slug')->span(1)->group(function (Field $field) use ($object) {
                        $field->rules(['required', 'max:160', 'unique:product_categories,slug,' . $object->formId]);
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
                    $column->displayText('type')->span(1);
                    $column->displayText('status')->span(1);
                    $column->displayText('slug')->span(1);
                    $column->displayText('created_at')->span(1);
                });
            });
        });
    }
}
