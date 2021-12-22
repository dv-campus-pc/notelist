<?php

namespace App\Model\Machine;

abstract class WaterTransport implements Machine
{
    public function move(int $x, int $y): void
    {
        echo "Sail to $x : $y";
    }

}