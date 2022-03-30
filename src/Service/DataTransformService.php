<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class DataTransformService
{
    public function transformViolationListToArray(ConstraintViolationList $list): array
    {
        $errors = [];
        /** @var ConstraintViolation $error */
        foreach ($list as $error) {
            $errors[] = $error->getMessage();
        }

        return $errors;
    }
}
