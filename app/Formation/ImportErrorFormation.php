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

class ImportErrorFormation
{
    public static function index(Object $object): Index
    {
        return Formation::createIndex('index', function (Index $index) use ($object) {
            $index
                ->select(function (Select $select){
                    $select->field('row')->sortable();
                    $select->field('status')->sortable();
                    $select->field('remark')->sortable()->truncate()->wrap();
                    $select->field('created_by')->with('createdBy')->reference('name')->sortable()->display('md');
                    $select->field('created_at');
                })
                ->search(function (Search $search) {
                    $search->field('row');
                    $search->field('status');
                    $search->field('remark');
                    $search->field('created_by')->with('createdBy')->reference('name');
                    $search->field('created_at');
                })
                ->filter(function (Filter $filter) {
                    $filter->text('row')->lazy()->operator('like');
                    $filter->text('status')->lazy()->operator('like');
                    $filter->text('remark')->lazy()->operator('like');
                    $filter->text('created_by')->with('createdBy')->reference('name')->lazy()->operator('like');
                    $filter->date('created_at')->lazy()->operator('like');
                })
                ->guard(function (Guard $guard) use ($object) {
                    if ($object->itemType == 'import')
                    {
                        $guard->field('import_id')->operator('=')->value($object->itemId);
                    }
                    if ($object->itemType == 'bulkEdit')
                    {
                        $guard->field('bulk_edit_id')->operator('=')->value($object->itemId);
                    }
                })
                ->indexTab(function (IndexTab $indexTab) use ($object) {
                });
        });
    }

}
