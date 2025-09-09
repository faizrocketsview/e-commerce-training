<?php

namespace Formation;

use Closure;
use App\Actions\Formation\Form\Form;
use App\Actions\Formation\Index\Index;

trait Formation
{
    public static function createForm(String $name, Closure $callback): Form
    {
        return new Form($name, $callback);
    }

    public static function createIndex(string $name, Closure $callback): Index
    {
        return new Index($name, $callback);
    }
}