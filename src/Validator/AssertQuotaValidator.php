<?php

namespace App\Validator;

use App\Entity\Media;
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

        if (!$value instanceof Media) {
            throw new UnexpectedTypeException($value, Media::class);
        }

        // If the media already exists (has an ID), we are just editing it.
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
                ->atPath('title') // Attach violation to the title field for better UI presentation
                ->addViolation();
        }
    }
}