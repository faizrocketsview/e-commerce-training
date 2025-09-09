<?php

namespace Formation\Form;

trait Option
{
    public function __construct(string $name, string $label = null)
    {
        $this->name = $name;
        $this->label = $label ?? $name;
    }
}