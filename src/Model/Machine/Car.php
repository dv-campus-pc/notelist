<?php

declare(strict_types=1);

namespace App\Model\Machine;

abstract class Car implements Machine
{
    public function move(int $x, int $y): void
    {
        echo "Driving to the $x:$y";
    }

    public function parkingNearDepot(): void {
        echo 'parking ...';
    }

    public abstract function fillTheTank(): void;
}