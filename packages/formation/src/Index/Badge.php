<?php

namespace Formation\Index;

trait Badge
{
    public function __construct(string $color)
    {
        $this->color = $color;
    }
}