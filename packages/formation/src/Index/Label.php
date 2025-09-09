<?php

namespace Formation\Index;

trait Label
{
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}