<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\Ownable;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OwnerVoter extends Voter
{
    const IS_OWNER = 'IS_OWNER';

    protected function supports(string $attribute, $subject): bool
    {
        if ($attribute !== self::IS_OWNER) {
            return false;
        }

        return $subject instanceof Ownable;
    }

    /**
     * @param Ownable $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();
        if (!$currentUser instanceof UserInterface) {
            return false;
        }

        return $subject->getUser() === $currentUser;
    }
}
