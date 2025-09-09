<?php

namespace Formation\Index;

use Closure;
use App\Actions\Formation\Index\Select;
use App\Actions\Formation\Index\Export;
use App\Actions\Formation\Index\Search;
use App\Actions\Formation\Index\Filter;
use App\Actions\Formation\Index\Guard;
use App\Actions\Formation\Index\GroupBy;
use App\Actions\Formation\Index\Action;
use App\Actions\Formation\Index\BulkEdit;
use App\Actions\Formation\Index\Import;
use App\Actions\Formation\Index\ItemAction;
use App\Actions\Formation\Index\IndexTab;

/**
 * @method $this poll(string $poll) Specify the directive modifier of poll
 */

trait Index
{
    public $select;
    public $export;
    public $import;
    public $bulkEdit;
    public $search;
    public $filter;
    public $guard;
    public $groupBy;
    public $action;

    public function __construct(string $name, Closure $callback = null)
    {
        $this->name = $name;

        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function select(Closure $callback): \App\Actions\Formation\Index\Index
    {
        $this->select = new Select($callback);

        return $this;
    }

    public function export(Closure $callback): \App\Actions\Formation\Index\Index
    {
        $this->export = new Export($callback);

        return $this;
    }

    public function import(Closure $callback): \App\Actions\Formation\Index\Index
    {
        $this->import = new Import($callback);

        return $this;
    }

    public function bulkEdit(Closure $callback): \App\Actions\Formation\Index\Index
    {
        $this->bulkEdit = new BulkEdit($callback);

        return $this;
    }

    public function search(Closure $callback): \App\Actions\Formation\Index\Index
    {
        $this->search = new Search($callback);

        return $this;
    }

    public function filter(Closure $callback): \App\Actions\Formation\Index\Index
    {
        $this->filter = new Filter($callback);

        return $this;
    }

    public function guard(Closure $callback): \App\Actions\Formation\Index\Index
    {
        $this->guard = new Guard($callback);

        return $this;
    }

    public function groupBy(Closure $callback): \App\Actions\Formation\Index\Index
    {
        $this->groupBy = new GroupBy($callback);

        return $this;
    }

    public function action(Closure $callback): \App\Actions\Formation\Index\Index
    {
        $this->action = new Action($callback);

        return $this;
    }

    public function itemAction(Closure $callback): \App\Actions\Formation\Index\Index
    {
        $this->itemAction = new ItemAction($callback);

        return $this;
    }

    public function indexTab(Closure $callback): \App\Actions\Formation\Index\Index
    {
        $this->indexTab = new IndexTab($callback);

        return $this;
    }
}