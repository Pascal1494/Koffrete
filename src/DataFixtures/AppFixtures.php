<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // 1. Create Subscription Tiers
        $standardSub = new Subscription();
        $standardSub->setName('Standard')
            ->setPrice(499) // 4.99 €
            ->setMediaLimit(100);
        $manager->persist($standardSub);

        $premiumSub = new Subscription();
        $premiumSub->setName('Premium')
            ->setPrice(999) // 9.99 €
            ->setMediaLimit(null); // Unlimited
        $manager->persist($premiumSub);

        // 2. Create Users
        // User A: Free Tier (no subscription)
        $freeUser = new User();
        $freeUser->setEmail('free@koffrete.local');
        $freeUser->setPassword($this->passwordHasher->hashPassword($freeUser, 'password123'));
        $manager->persist($freeUser);

        // User B: Standard Tier
        $standardUser = new User();
        $standardUser->setEmail('standard@koffrete.local')
            ->setSubscription($standardSub);
        $standardUser->setPassword($this->passwordHasher->hashPassword($standardUser, 'password123'));
        $manager->persist($standardUser);

        // User C: Premium Tier
        $premiumUser = new User();
        $premiumUser->setEmail('premium@koffrete.local')
            ->setSubscription($premiumSub);
        $premiumUser->setPassword($this->passwordHasher->hashPassword($premiumUser, 'password123'));
        $manager->persist($premiumUser);

        $manager->flush();
    }
}