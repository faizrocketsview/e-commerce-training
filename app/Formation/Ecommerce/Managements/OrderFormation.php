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
use App\Actions\Formation\Index\Guard;
use App\Actions\Formation\Index\Action;
use App\Actions\Formation\Index\ItemAction;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrderFormation
{
    public static $model = 'App\Models\Order';

    public static function index(Object $object): Index
    {
        return Formation::createIndex('index', function (Index $index) use ($object) {
            $index
                ->select(function (Select $select) {
                    // Show: User (name), Status (with badge), Total, Created At
                    $select->field('id')->sortable();
                    $select->field('user_id')->with('user')->reference('name')->sortable();
                    $select->field('status')->sortable()->group(function ($field) {
                        $field->badge('draft', 'gray');
                        $field->badge('pending', 'yellow');
                        $field->badge('paid', 'green');
                        $field->badge('shipped', 'blue');
                        $field->badge('completed', 'indigo');
                        $field->badge('cancelled', 'red');
                    });
                    $select->field('total')->sortable()->align('right');
                    $select->field('created_at')->sortable()->display('md')->sortByDefault('desc');
                })
                ->export(function (Export $export) {
                    $export->field('id');
                    $export->field('user_id')->with('user')->reference('name');
                    $export->field('status');
                    $export->field('subtotal');
                    $export->field('tax');
                    $export->field('shipping');
                    $export->field('discount');
                    $export->field('total');
                    $export->field('total_price');
                    $export->field('placed_at');
                    $export->field('created_at');
                })
                ->search(function (Search $search) {
                    $search->field('user_id')->with('user')->reference('name');
                })
                ->filter(function (Filter $filter) {
                    $filter->select('status')->operator('=')->debounce()->group(function (Field $field) {
                        $field->option('', '');
                        $field->option('draft', 'Draft');
                        $field->option('pending', 'Pending');
                        $field->option('paid', 'Paid');
                        $field->option('shipped', 'Shipped');
                        $field->option('completed', 'Completed');
                        $field->option('cancelled', 'Cancelled');
                    });
                    $filter->select('user_id')->operator('=')->debounce()->group(function (Field $field) {
                        $field->option('', '');
                        foreach(User::all() as $user) {
                            $field->option($user->id, $user->name);
                        }
                    });
                })
                // Show all orders (no role-based guard here)
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
            $card->create('Order Information')->column(1)->group(function (Section $section) use ($object) {
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    $column->select('user_id')->span(1)->autofocus()->group(function (Field $field) {
                        $field->option('', '');
                        foreach(User::all() as $user) {
                            $field->option($user->id, $user->name);
                        }
                        $field->rules(['required', 'exists:users,id']);
                    });
                    
                    $column->select('status')->span(1)->group(function (Field $field) {
                        $field->option('draft', 'Draft');
                        $field->option('pending', 'Pending');
                        $field->option('paid', 'Paid');
                        $field->option('shipped', 'Shipped');
                        $field->option('completed', 'Completed');
                        $field->option('cancelled', 'Cancelled');
                        $field->rules(['required', 'in:draft,pending,paid,shipped,completed,cancelled']);
                    });
                });
                
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    $column->text('currency')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'max:3']);
                    });
                    
                    $column->number('total_price')->span(1)->readonly()->group(function (Field $field) {
                        $field->rules(['nullable', 'numeric', 'min:0']);
                    });
                });
                
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    $column->number('subtotal')->span(1)->readonly()->group(function (Field $field) {
                        $field->rules(['required', 'numeric', 'min:0']);
                    });
                    
                    $column->number('tax')->span(1)->readonly()->group(function (Field $field) {
                        $field->rules(['required', 'numeric', 'min:0']);
                    });
                });
                
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    $column->number('shipping')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'numeric', 'min:0']);
                    });
                    
                    $column->number('discount')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'numeric', 'min:0']);
                    });
                });
                
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    $column->number('total')->span(1)->readonly()->group(function (Field $field) {
                        $field->rules(['required', 'numeric', 'min:0']);
                    });
                    
                    $column->preset('created_by')->span(1)->default(Auth::id());
                });
            });

            // Order Items Section
            $card->create('Order Items')->column(1)->group(function (Section $section) use ($object) {
                $section->create('')->span(1)->column(1)->group(function (Column $column) use ($object) {
                    $column->subfieldBox('orderItems')->with('orderItems')->span(1)->group(function (Field $field) {
                        $field->select('product_id')->span(2)->group(function (Field $subField) {
                            $subField->option('', 'Select Product');
                            foreach(\App\Models\Product::where('status', 'active')->get() as $product) {
                                $subField->option($product->id, $product->name . ' (' . $product->sku . ') - Stock: ' . $product->stock . ' - Price: $' . number_format((float)$product->price, 2));
                            }
                            $subField->rules(['required', 'exists:products,id']);
                        });
                    
                        
                        $field->number('quantity')->span(1)->group(function (Field $subField) {
                            $subField->rules(['required', 'integer', 'min:1', 'max:9999']);
                        });
                        // Display fields for existing values in edit/show
                        // $field->text('product_name')->span(2)->readonly();
                        // $field->text('sku')->span(1)->readonly();
                        // $field->number('unit_price')->span(1)->readonly();
                        
                    });
                });
            });
        });
    }

    public static function getEditCard($object, $tab)
    {
        return $tab->create('details')->description('')->group(function (Card $card) use ($object) {
            $card->create('Order Information')->column(1)->group(function (Section $section) use ($object) {
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    $column->select('user_id')->span(1)->autofocus()->group(function (Field $field) {
                        $field->option('', '');
                        foreach(User::all() as $user) {
                            $field->option($user->id, $user->name);
                        }
                        $field->rules(['required', 'exists:users,id']);
                    });
                    
                    $column->select('status')->span(1)->group(function (Field $field) {
                        $field->option('draft', 'Draft');
                        $field->option('pending', 'Pending');
                        $field->option('paid', 'Paid');
                        $field->option('shipped', 'Shipped');
                        $field->option('completed', 'Completed');
                        $field->option('cancelled', 'Cancelled');
                        $field->rules(['required', 'in:draft,pending,paid,shipped,completed,cancelled']);
                    });
                });
                
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    $column->text('currency')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'max:3']);
                    });
                    
                    $column->number('total_price')->span(1)->readonly()->group(function (Field $field) {
                        $field->rules(['nullable', 'numeric', 'min:0']);
                    });
                });
                
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    $column->number('subtotal')->span(1)->readonly()->group(function (Field $field) {
                        $field->rules(['required', 'numeric', 'min:0']);
                    });
                    
                    $column->number('tax')->span(1)->readonly()->group(function (Field $field) {
                        $field->rules(['required', 'numeric', 'min:0']);
                    });
                });
                
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    $column->number('shipping')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'numeric', 'min:0']);
                    });
                    
                    $column->number('discount')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'numeric', 'min:0']);
                    });
                });
                
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    $column->number('total')->span(1)->readonly()->group(function (Field $field) {
                        $field->rules(['required', 'numeric', 'min:0']);
                    });
                });
            });

            // Order Items Section
            // $card->create('Order Items')->column(1)->group(function (Section $section) use ($object) {
            //     $section->create('')->span(1)->column(1)->group(function (Column $column) use ($object) {
            //         $column->subfieldBox('orderItems')->with('orderItems')->span(1)->group(function (Field $field) {
                        

            //             // Readable display for edit mode
            //             $field->displayText('product_name', 'Product Name')->span(2);
            //             $field->displayText('sku', 'SKU')->span(1);
            //             $field->displayText('unit_price', 'Unit Price')->span(1)->display('currency');
    

            //         });
            //     });
            // });
        });
    }

    public static function getShowCard($object, $tab)
    {
        return $tab->create('details')->description('')->group(function (Card $card) use ($object) {
            $card->create('Order Information')->column(1)->group(function (Section $section) use ($object) {
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    $column->displayText('user_id')->with('user')->reference('name')->span(1);
                    $column->displayText('status')->span(1)->display('badge');
                    $column->displayText('currency')->span(1);
                    $column->displayText('subtotal')->span(1)->display('currency');
                    $column->displayText('tax')->span(1)->display('currency');
                    $column->displayText('shipping')->span(1)->display('currency');
                    $column->displayText('discount')->span(1)->display('currency');
                    $column->displayText('total')->span(1)->display('currency');
                    $column->displayText('total_price')->span(1)->display('currency');
                    $column->displayText('placed_at')->span(1);
                    $column->displayText('created_at')->span(1);
                });
            });

            // Order Items Display Section
            $card->create('Order Items')->column(1)->group(function (Section $section) use ($object) {
                $section->create('')->span(1)->column(1)->group(function (Column $column) use ($object) {
                    $column->subfieldBox('orderItems')->with('orderItems')->span(1)->group(function (Field $field) {
                        $field->displayText('product_name')->span(2);
                        $field->displayText('sku')->span(1);
                        $field->displayText('unit_price')->span(1)->display('currency');
                        $field->displayText('quantity')->span(1);
                    });
                });
            });
        });
    }
}