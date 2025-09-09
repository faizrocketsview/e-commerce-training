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

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderItemFormation
{
    public static $model = 'App\Models\OrderItem';

    public static function index(Object $object): Index
    {
        return Formation::createIndex('index', function (Index $index) use ($object) {
            $index
                ->select(function (Select $select) {
                    $select->field('id')->hide();
                    $select->field('order_id')->with('order')->reference('id')->sortable();
                    $select->field('name')->sortable();
                    $select->field('product_name')->sortable()->highlight();
                    $select->field('sku')->sortable();
                    $select->field('unit_price')->sortable()->display('currency');
                    $select->field('quantity')->sortable();
                    $select->field('line_total')->sortable()->display('currency');
                    $select->field('price')->sortable()->display('currency');
                    $select->field('created_at')->sortable()->display('md')->sortByDefault('desc');
                })
                ->export(function (Export $export) {
                    $export->field('id');
                    $export->field('order_id')->with('order')->reference('id');
                    $export->field('name');
                    $export->field('product_name');
                    $export->field('sku');
                    $export->field('unit_price');
                    $export->field('quantity');
                    $export->field('line_total');
                    $export->field('price');
                    $export->field('created_at');
                })
                ->search(function (Search $search) {
                    $search->field('product_name');
                    $search->field('sku');
                })
                ->filter(function (Filter $filter) {
                    $filter->text('product_name')->operator('like');
                    $filter->text('sku')->operator('like');
                    $filter->select('order_id')->operator('=')->debounce()->group(function (Field $field) {
                        $field->option('', '');
                        foreach(Order::all() as $order) {
                            $field->option($order->id, 'Order #' . $order->id);
                        }
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
                    $column->select('order_id')->span(1)->autofocus()->group(function (Field $field) {
                        $field->option('', '');
                        foreach(Order::all() as $order) {
                            $field->option($order->id, 'Order #' . $order->id);
                        }
                        $field->rules(['required', 'exists:orders,id']);
                    });
                    
                    $column->select('product_id')->span(1)->group(function (Field $field) {
                        $field->option('', '');
                        foreach(Product::all() as $product) {
                            $field->option($product->id, $product->name . ' (' . $product->sku . ')');
                        }
                        $field->rules(['nullable', 'exists:products,id']);
                    });
                    
                    $column->text('name')->span(1)->group(function (Field $field) {
                        $field->rules(['nullable', 'max:200']);
                    });
                    
                    $column->text('product_name')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'max:200']);
                    });
                    
                    $column->text('sku')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'max:64']);
                    });
                    
                    $column->number('unit_price')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'numeric', 'min:0']);
                    });
                    
                    $column->number('quantity')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'integer', 'min:1']);
                    });
                    
                    $column->textarea('description')->span(2)->group(function (Field $field) {
                        $field->rules(['nullable']);
                    });
                    
                    $column->number('price')->span(1)->group(function (Field $field) {
                        $field->rules(['nullable', 'numeric', 'min:0']);
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
                    $column->select('order_id')->span(1)->autofocus()->group(function (Field $field) {
                        $field->option('', '');
                        foreach(Order::all() as $order) {
                            $field->option($order->id, 'Order #' . $order->id);
                        }
                        $field->rules(['required', 'exists:orders,id']);
                    });
                    
                    $column->select('product_id')->span(1)->group(function (Field $field) {
                        $field->option('', '');
                        foreach(Product::all() as $product) {
                            $field->option($product->id, $product->name . ' (' . $product->sku . ')');
                        }
                        $field->rules(['nullable', 'exists:products,id']);
                    });
                    
                    $column->text('name')->span(1)->group(function (Field $field) {
                        $field->rules(['nullable', 'max:200']);
                    });
                    
                    $column->text('product_name')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'max:200']);
                    });
                    
                    $column->text('sku')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'max:64']);
                    });
                    
                    $column->number('unit_price')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'numeric', 'min:0']);
                    });
                    
                    $column->number('quantity')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'integer', 'min:1']);
                    });
                    
                    $column->textarea('description')->span(2)->group(function (Field $field) {
                        $field->rules(['nullable']);
                    });
                    
                    $column->number('price')->span(1)->group(function (Field $field) {
                        $field->rules(['nullable', 'numeric', 'min:0']);
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
                    $column->displayText('order_id')->with('order')->reference('id')->span(1);
                    $column->displayText('product_id')->with('product')->reference('name')->span(1);
                    $column->displayText('name')->span(1);
                    $column->displayText('product_name')->span(1);
                    $column->displayText('sku')->span(1);
                    $column->displayText('unit_price')->span(1)->display('currency');
                    $column->displayText('quantity')->span(1);
                    $column->displayText('line_total')->span(1)->display('currency');
                    $column->displayText('price')->span(1)->display('currency');
                    $column->displayText('created_at')->span(1);
                });
            });
        });
    }
}
