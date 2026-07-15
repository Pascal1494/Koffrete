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

    /**
     * Returns detailed statistics about user's active quota for UI presentation.
     *
     * @return array{
     *     current: int,
     *     limit: ?int,
     *     percentage: int,
     *     isFull: bool,
     *     subscriptionName: string
     * }
     */
    public function getQuotaStats(User $user): array
    {
        $subscription = $user->getSubscription();
        $limit = $subscription ? $subscription->getMediaLimit() : self::DEFAULT_FREE_LIMIT;
        $currentCount = $this->userItemRepository->count(['user' => $user]);

        $percentage = 0;
        $isFull = false;

        if ($limit !== null && $limit > 0) {
            $percentage = (int) min(100, round(($currentCount / $limit) * 100));
            $isFull = $currentCount >= $limit;
        }

        return [
            'current' => $currentCount,
            'limit' => $limit,
            'percentage' => $percentage,
            'isFull' => $isFull,
            'subscriptionName' => $subscription ? $subscription->getName() : 'Gratuit (Freemium)',
        ];
    }
}