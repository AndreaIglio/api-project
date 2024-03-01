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
    public const EDIT_OR_REMOVE_MANAGER = 'edit_or_remove_manager';
    public const EDIT_OR_REMOVE_CUSTOMER = 'edit_or_remove_customer';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [
            self::CREATE_MANAGER,
            self::CREATE_CUSTOMER,
            self::EDIT_OR_REMOVE_MANAGER,
            self::EDIT_OR_REMOVE_CUSTOMER,
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
                return $this->canCreateCustomer($user);
            case self::EDIT_OR_REMOVE_MANAGER:
                return $this->canEditOrRemoveManager($user, $subject instanceof Manager ? $subject : null);
            case self::EDIT_OR_REMOVE_CUSTOMER:
                return $this->canEditOrRemoveCustomer($user, $subject instanceof Customer ? $subject : null);
        }

        throw new \LogicException('Questo codice non dovrebbe essere raggiunto!');
    }

    private function canCreateManager(User $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canCreateCustomer(User $user): bool
    {
        // A manager can create a customer
        return in_array('ROLE_MANAGER', $user->getRoles());
    }

    private function canEditOrRemoveManager(User $user, ?Manager $subject): bool
    {
        // The manager itself can edit or remove his profile or the admin
        return $user === $subject || in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canEditOrRemoveCustomer(User $user, ?Customer $subject): bool
    {
        // The customer itself can edit or remove his profile or the manager related or admin
        return $user === $subject || ($subject && $user === $subject->getManager()) || in_array('ROLE_ADMIN', $user->getRoles());
    }
}
