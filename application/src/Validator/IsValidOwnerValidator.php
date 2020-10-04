<?php

namespace App\Validator;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsValidOwnerValidator extends ConstraintValidator
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\IsValidOwner */

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof User) {
            throw new \InvalidArgumentException('@IsValidOwner constraint must be put on a property containing a User object');
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            $this->context->buildViolation($constraint->anonymousMessage)
                ->addViolation();

            return;
        }

        if ($this->currentUserIsAdmin()) {
            return;
        }

        if (!$this->usersAreEqual($user, $value)) {
            $this->context->buildViolation($constraint->message)
            ->addViolation();
        }
    }

    private function currentUserIsAdmin()
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }

    private function usersAreEqual($currentUser, $value)
    {
        if ($currentUser->getId() === $value->getId()) {
            return true;
        }

        return false;
    }
}
