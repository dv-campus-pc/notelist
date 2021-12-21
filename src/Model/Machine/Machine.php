<?php

declare(strict_types=1);

namespace App\Model\Machine;

interface Machine
{
    public function move(int $x, int $y): void;
    public function fillTheTank(): void;
}