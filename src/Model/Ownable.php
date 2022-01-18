<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Security\Core\User\UserInterface;

interface Ownable
{
    public function getUser(): UserInterface;
    public function setUser(UserInterface $user);
}
