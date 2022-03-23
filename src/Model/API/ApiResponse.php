<?php

declare(strict_types=1);

namespace App\Model\API;

use Symfony\Component\HttpFoundation\Response;

class ApiResponse extends Response
{
    public function __construct(?string $content = '', int $status = 200, array $headers = [])
    {
        parent::__construct(
            $content,
            $status,
            // TODO: potential bug of duplicating content-type
            array_merge(['content-type' => 'application/json'], $headers)
        );
    }
}
