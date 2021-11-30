<?php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AnonymousUserVoter extends Voter
{
    const IS_ANONYMOUS_USER = 'IS_ANONYMOUS_USER';

    protected function supports(string $attribute, $subject)
    {
        return $attribute === self::IS_ANONYMOUS_USER;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        return !$token->getUser();
    }
}
