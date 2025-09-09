<?php

namespace Formation\Actions;

class SaveAction
{
    public function execute(Object $object, String $actionType)
    {
        $object->save();
        return $object->id;
    }
}