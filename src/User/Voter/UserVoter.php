<?php

declare(strict_types=1);

namespace App\User\Voter;

use App\User\Entity\Common\User;
use App\User\Entity\Customer;
use App\User\Entity\Manager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class UserVoter extends Voter
{
    public const CREATE_MANAGER = 'create_manager';
    public const CREATE_CUSTOMER = 'create_customer';
    public const EDIT_MANAGER = 'edit_manager';
    public const EDIT_CUSTOMER = 'edit_customer';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [
            self::CREATE_MANAGER,
            self::CREATE_CUSTOMER,
            self::EDIT_MANAGER,
            self::EDIT_CUSTOMER,
        ]);
    }

    /**
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE_MANAGER:
                return $this->canCreateManager($user);
            case self::CREATE_CUSTOMER:
                return $this->canCreateCustomer($user, $subject instanceof Customer ? $subject : null);
            case self::EDIT_MANAGER:
                return $this->canEditManager($user, $subject instanceof Manager ? $subject : null);
            case self::EDIT_CUSTOMER:
                return $this->canEditCustomer($user, $subject instanceof Customer ? $subject : null);
        }

        throw new \LogicException('Questo codice non dovrebbe essere raggiunto!');
    }

    private function canCreateManager(User $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canCreateCustomer(User $user, ?Customer $subject): bool
    {
        // Un admin o un manager può creare un cliente
        return in_array('ROLE_MANAGER', $user->getRoles()) || in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canEditManager(User $user, ?Manager $subject): bool
    {
        // Solo un admin o il manager stesso può modificare o rimuovere il proprio profilo
        return $user === $subject || in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canEditCustomer(User $user, ?Customer $subject): bool
    {
        // Solo un cliente stesso, il suo manager, o un admin possono modificare il cliente
        return $user === $subject || ($subject && $user === $subject->getManager()) || in_array('ROLE_ADMIN', $user->getRoles());
    }
}
