<?php

namespace App\Validator;

use App\Entity\CheeseListing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsValidForPublicationValidator extends ConstraintValidator
{
    private $entityManager;
    private $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\IsValidForPublication */

        if (!$value instanceof CheeseListing) {
            throw new \LogicException('Only CheeseListing is supported');
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $originalData = $this->entityManager
            ->getUnitOfWork()
            ->getOriginalEntityData($value);

        $previousIsPublished = $originalData['isPublished'] ?? false;

        if ($previousIsPublished === $value->getIsPublished()) {
            return;
        }

        if ($value->getIsPublished()) {
            // publishing
            if (strlen($value->getDescription()) < 100) {
                $this->context
                ->buildViolation('Cannot publish: description is too short!')
                ->atPath('description')
                ->addViolation();
            }

            return;
        }

        // unpublishing
        throw new AccessDeniedException('Only admin users can unpublish');
    }
}
