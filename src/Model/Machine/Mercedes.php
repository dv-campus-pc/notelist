<?php

declare(strict_types=1);

namespace App\Model\Machine;

use DateTime;

class Mercedes extends Car
{
    public static $wheelsNumber = 4;

    private int $doorsNumber;
    private float $engineValue;
    public DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function driver(): void {
        echo "Driving on Mercedes";
    }

    public static function getVendor(): string {
        new Mercedes();
        return 'Mercedes-Benz';
    }

    public function fillTheTank(): void
    {
        echo 'Filling from the left side';
    }

    public function parkingNearDepot(): void
    {
        echo 'Parking ...';
    }

    public function getDoorsNumber(): int
    {
        return $this->doorsNumber;
    }

    public function setDoorsNumber(int $doorsNumber): void
    {
        $this->doorsNumber = $doorsNumber;
    }

    public function getEngineValue(): float
    {
        return $this->engineValue;
    }

    public function setEngineValue(float $engineValue): void
    {
        $this->engineValue = $engineValue;
    }
}
