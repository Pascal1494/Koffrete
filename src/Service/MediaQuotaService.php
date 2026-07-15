<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserItemRepository;

class MediaQuotaService
{
    public const DEFAULT_FREE_LIMIT = 10;

    public function __construct(
        private readonly UserItemRepository $userItemRepository
    ) {}

    /**
     * Checks if a user is allowed to add another physical media item copy.
     */
    public function canUserAddMedia(User $user): bool
    {
        $subscription = $user->getSubscription();

        // If no active subscription, default to free tier limit (10)
        $limit = $subscription ? $subscription->getMediaLimit() : self::DEFAULT_FREE_LIMIT;

        // null limit represents unlimited access (e.g. Premium tier)
        if ($limit === null) {
            return true;
        }

        $currentCount = $this->userItemRepository->count(['user' => $user]);

        return $currentCount < $limit;
    }
}