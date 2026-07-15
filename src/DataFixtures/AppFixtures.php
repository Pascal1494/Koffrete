<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Dvd;
use App\Entity\Subscription;
use App\Entity\User;
use App\Entity\UserItem;
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
        $freeUser = new User();
        $freeUser->setEmail('free@koffrete.local');
        $freeUser->setPassword($this->passwordHasher->hashPassword($freeUser, 'password123'));
        $manager->persist($freeUser);

        $standardUser = new User();
        $standardUser->setEmail('standard@koffrete.local')
            ->setSubscription($standardSub);
        $standardUser->setPassword($this->passwordHasher->hashPassword($standardUser, 'password123'));
        $manager->persist($standardUser);

        $premiumUser = new User();
        $premiumUser->setEmail('premium@koffrete.local')
            ->setSubscription($premiumSub);
        $premiumUser->setPassword($this->passwordHasher->hashPassword($premiumUser, 'password123'));
        $manager->persist($premiumUser);

        // 3. Create Catalog Media (Releases/Editions)
        $book1 = new Book();
        $book1->setTitle('The Hobbit')
            ->setAuthor('J.R.R. Tolkien')
            ->setIsbn('9780261103344');
        $manager->persist($book1);

        $book2 = new Book();
        $book2->setTitle('1984')
            ->setAuthor('George Orwell')
            ->setIsbn('9780451524935');
        $manager->persist($book2);

        $dvd1 = new Dvd();
        $dvd1->setTitle('Inception')
            ->setDirector('Christopher Nolan')
            ->setActors(['Leonardo DiCaprio', 'Joseph Gordon-Levitt', 'Marion Cotillard'])
            ->setDurationInMinutes(148)
            ->setReleaseYear('2010');
        $manager->persist($dvd1);

        // 4. Create User Items (Physical Copies owned by collectors)
        // Showcasing duplicate handling: Free user owns 2 copies of the exact same Catalog book (The Hobbit)
        // but in different conditions and editions!
        $copy1 = new UserItem();
        $copy1->setUser($freeUser)
            ->setMedia($book1)
            ->setCondition('Mint')
            ->setPersonalNotes('Original UK Cover Edition');
        $manager->persist($copy1);

        $copy2 = new UserItem();
        $copy2->setUser($freeUser)
            ->setMedia($book1)
            ->setCondition('Good')
            ->setPersonalNotes('French translated paperback pocket edition');
        $manager->persist($copy2);

        // Free user also owns a copy of Inception
        $copy3 = new UserItem();
        $copy3->setUser($freeUser)
            ->setMedia($dvd1)
            ->setCondition('Very Good')
            ->setPersonalNotes('Special Blu-ray Steelbook edition');
        $manager->persist($copy3);

        $manager->flush();
    }
}