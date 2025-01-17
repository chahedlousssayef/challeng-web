<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function loadUserByUsername(string $username): UserInterface
    {
        // Chargez l'utilisateur à partir de la base de données ou d'une autre source
        // Par exemple, utilisez un repository pour trouver l'utilisateur par email
        return new User();
    }
    public function loadUserByIdentifier(string $identifier): UserInterface
{
    // Chargez l'utilisateur à partir de la base de données ou d'une autre source
    // Par exemple, utilisez un repository pour trouver l'utilisateur par email
    return new User();
}


    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}

