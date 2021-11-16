<?php

declare(strict_types=1);

namespace App\Twig;

use App\Controller\NotelistController;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TemplateExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_categories', [$this, 'getCategories']),
        ];
    }

    public function getCategories(): array
    {
        return NotelistController::$categories;
    }
}
