<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AssertQuota extends Constraint
{
    public string $message = 'Vous avez atteint la limite de votre quota de médias gratuit pour votre abonnement actuel.';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}