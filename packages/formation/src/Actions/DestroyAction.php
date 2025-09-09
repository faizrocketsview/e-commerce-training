<?php

namespace Formation\Actions;

use Illuminate\Support\Str;

class DestroyAction
{
    public function execute(Object $object): void
    {
        $object->update([
            'deleted_at' => now(),
            'deleted_token' => Str::uuid(),
        ]);
    }
}