<?php

declare(strict_types=1);

namespace App\Model\Machine;

class Bmw extends Car
{
    public function move(int $x, int $y): void
    {
        echo "Drifting to the $x:$y";
    }

    public function fillTheTank(): void
    {
        echo 'Filling from the right side';
    }

    public function parkingNearDepot(): void
    {
        echo 'Parking ...';
    }
}
