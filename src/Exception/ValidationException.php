<?php

namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends Exception
{
    private ?ConstraintViolationList $errorsList;

    public function __construct(string $message = "", ConstraintViolationListInterface $errorsList = null)
    {
        parent::__construct($message, Response::HTTP_BAD_REQUEST);
        $this->errorsList = $errorsList ?: new ConstraintViolationList;
    }

    public function getErrorsList(): ?ConstraintViolationList
    {
        return $this->errorsList;
    }
}
