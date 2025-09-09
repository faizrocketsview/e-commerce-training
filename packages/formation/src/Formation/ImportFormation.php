<?php

namespace App\Formation;

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
use App\Actions\Formation\Index\GroupBy;
use App\Actions\Formation\Index\Action;
use App\Actions\Formation\Index\ItemAction;
use App\Actions\Formation\Index\Operation;
use App\Actions\Formation\Index\IndexTab;

use Illuminate\Support\Facades\Auth;

class ImportFormation
{
    public static function index(Object $object): Index
    {
        return Formation::createIndex('index', function (Index $index) use ($object) {
            $index
                ->select(function (Select $select) {
                    $select->field('file_name')->sortable();
                    $select->field('status')->sortable();
                    $select->field('started_at')->sortable()->sortByDefault('desc');
                    $select->field('ended_at')->sortable();
                    $select->field('total_inserted')->sortable();
                    $select->field('total_failed')->sortable();
                    $select->field('total_rows')->sortable();
                    $select->field('created_by')->with('createdBy')->reference('name')->sortable()->display('md');
                })
                ->search(function (Search $search) {
                    $search->field('file_name');
                    $search->field('status');
                    $search->field('started_at');
                    $search->field('ended_at');
                    $search->field('total_inserted');
                    $search->field('total_failed');
                    $search->field('total_rows');
                    $search->field('created_by')->with('createdBy')->reference('name');
                    $search->field('created_at');
                })
                ->filter(function (Filter $filter) {
                    $filter->text('file_name')->lazy()->operator('like');
                    $filter->text('status')->lazy()->operator('like');
                    $filter->date('started_at')->lazy()->operator('like');
                    $filter->date('ended_at')->lazy()->operator('like');
                    $filter->text('total_inserted')->lazy()->operator('=');
                    $filter->text('total_failed')->lazy()->operator('=');
                    $filter->text('total_rows')->lazy()->operator('=');
                    $filter->text('created_by')->with('createdBy')->reference('name')->lazy()->operator('like');
                    $filter->date('created_at')->lazy()->operator('like');
                })
                ->action(function (Action $action) {
                    $action->operation('create');
                })
                ->itemAction(function (ItemAction $itemAction) use ($object){
                    $itemAction->operation('show')->rowClickable();
                    if ($object->importType == 'import') {
                        $itemAction->operation('manage import errors')->custom($object->moduleSection.'/'.$object->moduleGroup.'/'.$object->module.'/import-errors')->itemType($object->importType);
                    }elseif ($object->importType == 'bulkEdit') {
                        $itemAction->operation('manage bulk edit errors')->custom($object->moduleSection.'/'.$object->moduleGroup.'/'.$object->module.'/import-errors')->itemType($object->importType);
                    }
                })
                ->guard(function (Guard $guard) use ($object) {
                    $guard->field('model')->operator('=')->value($object->getModuleModel());
                })
                ->indexTab(function (IndexTab $indexTab) use ($object) {
                });
        });
    }

    public static function form(Object $object): Form
    {
        return Formation::createForm('form', function (Form $form) use ($object) {
            $tabCount = 0;
            $form->create('tab')->group($object, $tabCount, function (Tab $tab) use ($object) {
                $tab->create('')->description('')->group(function (Card $card) use ($object) {
                    $card->create('')->column(2)->group(function (Section $section) use ($object) {
                        $section->create('')->span(1)->column(1)->group(function (Column $column) use ($object) {

                            if ($object->type === 'create' || $object->type === 'edit') {
                                $column->file('attachment')->sampleFile()->model($object->getModuleModel())->folderPath('/imports/')->span(1)->group(function (Field $field) {
                                    $field->rules(['mimes:csv', 'max:20480', 'required']);
                                });
                                $column->preset('created_by')->default(Auth::id());
                            }elseif ($object->type === 'show') {
                                $column->displayText('model');
                                $column->displayText('file_name');
                                $column->displayText('started_at');
                                $column->displayText('ended_at');
                                $column->displayText('status');
                                $column->displayText('total_rows');
                            }

                            $column->preset('updated_by')->default(Auth::id());
                        });
                        $section->create('')->span(1)->column(1)->group(function (Column $column) use ($object) {

                            if ($object->type === 'show') {
                                $column->displayText('total_inserted');
                                $column->displayText('total_failed');
                                $column->displayText('total_completed_chunk');
                                $column->displayText('total_chunk');
                                $column->displayText('created_by')->with('createdBy')->reference('name');
                                $column->displayText('created_at');
                            }

                            $column->preset('updated_by')->default(Auth::id());
                        });
                    }); 
                });
            });
        });
    }
}