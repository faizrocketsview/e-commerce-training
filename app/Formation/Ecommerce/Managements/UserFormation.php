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

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserFormation
{
    public static $model = 'App\Models\User';

    public static function index(Object $object): Index
    {
        return Formation::createIndex('index', function (Index $index) use ($object) {
            $index
                ->select(function (Select $select) {
                    $select->field('id')->hide();
                    $select->field('username')->sortable();
                    $select->field('name')->sortable()->highlight();
                    $select->field('email')->sortable();
                    $select->field('contact_number')->sortable();
                    $select->field('role')->sortable()->localize();
                    $select->field('created_at')->sortable()->display('md')->sortByDefault('desc');
                })
                ->export(function (Export $export) {
                    $export->field('id');
                    $export->field('username');
                    $export->field('name');
                    $export->field('email');
                    $export->field('contact_number');
                    $export->field('role');
                    $export->field('created_at');
                })
                ->search(function (Search $search) {
                    $search->field('username');
                    $search->field('name');
                    $search->field('email');
                    $search->field('contact_number');
                })
                ->filter(function (Filter $filter) {
                    $filter->text('username')->operator('like');
                    $filter->text('name')->operator('like');
                    $filter->text('email')->operator('like');
                    $filter->text('contact_number')->operator('like');
                    $filter->select('role')->operator('=')->debounce()->group(function (Field $field) {
                        $field->option('', '');
                        $field->option('admin', 'Admin');
                        $field->option('user', 'User');
                    });
                })
                ->guard(function (Guard $guard) use ($object) {
                    // Only show users that the current user can manage
                    if (Auth::user()->role !== 'admin') {
                        $guard->field('id')->operator('=')->value(Auth::id());
                    }
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
                    $column->text('username')->span(1)->autofocus()->group(function (Field $field) {
                        $field->rules(['nullable', 'max:255', 'unique:users,username']);
                    });
                    
                    $column->text('name')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'max:255']);
                    });
                    
                    $column->text('email')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'email', 'unique:users,email']);
                    });
                    
                    $column->text('contact_number')->span(1)->group(function (Field $field) {
                        $field->rules(['nullable', 'max:255']);
                    });
                    
                    $column->password('password')->span(1)->group(function (Field $field) {
                        $field->rules(['nullable', 'min:8']);
                        $field->value(function() { return 'password123'; }); // Default password
                    });
                    
                    $column->select('role')->span(1)->group(function (Field $field) {
                        $field->option('', '');
                        $field->option('admin', 'Admin');
                        $field->option('user', 'User');
                        $field->rules(['required', Rule::in(['admin', 'user'])]);
                    });
                });
            });

            $card->create('Permissions')->column(1)->group(function (Section $section) use ($object) {
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    $allPermissions = Permission::all()->groupBy(function ($permission) {
                        $modelNamespace = explode(":", $permission->name)[0];
                        $modelName = explode(".", $modelNamespace)[2];
                        return $modelName;
                    });

                    foreach ($allPermissions as $model => $modelPermissions) {
                        $column->checkboxButtonMultiple('permissions_' . $model)->span(1)->column(2)->group(function (Field $field) use ($modelPermissions) {
                            foreach ($modelPermissions as $permission) {
                                $permissionName = explode(":", $permission->name)[1];
                                $field->option($permission->id, $permissionName);
                            }
                            // Set default empty array
                            $field->value(function() { return []; });
                            // Add validation rules for the permission field
                            $field->rules(['nullable', 'array']);
                            // Add array element validation rules
                            $field->arrayRules = ['nullable', 'array'];
                        });
                    }

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
                    $column->text('username')->span(1)->autofocus()->group(function (Field $field) use ($object) {
                        $field->rules(['nullable', 'max:255', Rule::unique('users', 'username')->ignore($object->formId)]);
                    });
                    
                    $column->text('name')->span(1)->group(function (Field $field) {
                        $field->rules(['required', 'max:255']);
                    });
                    
                    $column->text('email')->span(1)->group(function (Field $field) use ($object) {
                        $field->rules(['required', 'email', Rule::unique('users', 'email')->ignore($object->formId)]);
                    });
                    
                    $column->text('contact_number')->span(1)->group(function (Field $field) {
                        $field->rules(['nullable', 'max:255']);
                    });
                    
                    $column->select('role')->span(1)->group(function (Field $field) {
                        $field->option('', '');
                        $field->option('admin', 'Admin');
                        $field->option('user', 'User');
                        $field->rules(['required', Rule::in(['admin', 'user'])]);
                    });
                });
            });

            // Add Permissions section for edit form
            $card->create('Permissions')->column(1)->group(function (Section $section) use ($object) {
                $section->create('')->span(1)->column(2)->group(function (Column $column) use ($object) {
                    // Group permissions by the last part of the namespace (e.g., ecommerce.managements.categories -> categories)
                    $permissions = Permission::get();
                    $grouped = $permissions->groupBy(function ($item) {
                        $beforeColon = explode(':', $item->name)[0];        // ecommerce.managements.categories
                        return last(explode('.', $beforeColon));            // categories
                    });

                    // Create checkboxButtonMultiple fields for each permission group
                    foreach ($grouped as $key => $group) {
                        $column->checkboxButtonMultiple($key)->span(1)->group(function (Field $field) use ($group, $object) {
                            foreach ($group as $permission) {
                                $field->option($permission->id, explode(':', $permission->name)[1]);
                            }
                            
                            // Set the current values for editing
                            $field->value(function($field) use ($object) {
                                // Get the current user's permissions
                                $user = \App\Models\User::with('permissions')->find($object->formId);
                                if (!$user) {
                                    return [];
                                }
                                
                                $userPermissions = $user->permissions->pluck('id')->toArray();
                                $module = $field->name;
                                $modulePermissions = \App\Models\Permission::where('name', 'like', "ecommerce.managements.{$module}:%")
                                    ->pluck('id')
                                    ->toArray();
                                
                                $userModulePermissions = array_intersect($userPermissions, $modulePermissions);
                                return array_values($userModulePermissions);
                            });
                            
                            $field->rules(['nullable', 'array']);
                        });
                    }
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
                    $column->displayText('email')->span(1);
                    $column->displayText('role')->span(1);
                    $column->displayText('created_at')->span(1);
                });
            });
        });
    }
}
