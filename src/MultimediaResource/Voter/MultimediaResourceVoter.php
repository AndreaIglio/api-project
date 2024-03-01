<?php

declare(strict_types=1);

namespace App\MultimediaResource\Voter;

use App\MultimediaResource\Entity\MultimediaResource;
use App\User\Entity\Common\User;
use App\User\Entity\Manager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Webmozart\Assert\Assert;

class MultimediaResourceVoter extends Voter
{
    public const VIEW = 'view';
    public const CREATE = 'create_or_remove';
    public const EDIT_OR_REMOVE = 'edit_or_remove';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [
            self::CREATE,
            self::VIEW,
            self::EDIT_OR_REMOVE,
        ]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user, $subject instanceof MultimediaResource ? $subject : null);
            case self::CREATE:
                // Only customer can create or remove multimedia resource
                return in_array('ROLE_CUSTOMER', $user->getRoles());
            case self::EDIT_OR_REMOVE:
                return $this->canEditOrRemove($user, $subject instanceof MultimediaResource ? $subject : null);
        }

        return false;
    }

    private function canView(User $user, ?MultimediaResource $multimediaResource): bool
    {
        Assert::isInstanceOf($multimediaResource, MultimediaResource::class);

        // Admin can see all resources
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Manager can see resources of its customers
        if (in_array('ROLE_MANAGER', $user->getRoles())) {
            Assert::isInstanceOf($user, Manager::class);
            foreach ($user->getCustomers() as $customer) {
                if ($multimediaResource->getCustomer() === $customer) {
                    return true;
                }
            }

            return false;
        }

        // Customer can see only its own resources
        return $multimediaResource->getCustomer() === $user;
    }

    private function canEditOrRemove(User $user, ?MultimediaResource $multimediaResource): bool
    {
        Assert::isInstanceOf($multimediaResource, MultimediaResource::class);

        // The admin can edit or remove any resource
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Customer can edit its own resource
        return $multimediaResource->getCustomer() === $user;
    }
}
