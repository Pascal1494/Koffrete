<?php

namespace App\Validator;

use App\Entity\UserItem;
use App\Service\MediaQuotaService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AssertQuotaValidator extends ConstraintValidator
{
    public function __construct(
        private readonly MediaQuotaService $quotaService
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof AssertQuota) {
            throw new UnexpectedTypeException($constraint, AssertQuota::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof UserItem) {
            throw new UnexpectedTypeException($value, UserItem::class);
        }

        // If the UserItem already exists (has an ID), we are just editing it (e.g. changing notes/condition).
        // We do not block editing of existing items!
        if ($value->getId() !== null) {
            return;
        }

        $user = $value->getUser();
        if ($user === null) {
            return; // No user associated yet, let other validators handle this
        }

        if (!$this->quotaService->canUserAddMedia($user)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('condition') // Attach violation to 'condition' field for UX in the copy form
                ->addViolation();
        }
    }
}