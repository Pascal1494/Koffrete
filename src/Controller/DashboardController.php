<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Dvd;
use App\Entity\User;
use App\Entity\UserItem;
use App\Form\BookCopyType;
use App\Form\DvdCopyType;
use App\Repository\SubscriptionRepository;
use App\Repository\UserItemRepository;
use App\Service\MediaQuotaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'app_dashboard')]
    public function index(
        UserItemRepository $userItemRepository,
        MediaQuotaService $quotaService
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $items = $userItemRepository->findBy(['user' => $user], ['acquiredAt' => 'DESC']);
        $quotaStats = $quotaService->getQuotaStats($user);

        return $this->render('dashboard/index.html.twig', [
            'items' => $items,
            'quota' => $quotaStats,
        ]);
    }

    #[Route('/add-book', name: 'app_dashboard_add_book')]
    public function addBook(
        Request $request,
        EntityManagerInterface $entityManager,
        MediaQuotaService $quotaService
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $userItem = new UserItem();
        $userItem->setUser($user);
        
        $book = new Book();
        $userItem->setMedia($book);

        $form = $this->createForm(BookCopyType::class, $userItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->persist($userItem);
            $entityManager->flush();

            $this->addFlash('success', 'Livre ajouté avec succès à votre bibliothèque !');

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard/add_item.html.twig', [
            'form' => $form->createView(),
            'title' => 'Ajouter un Livre',
            'type' => 'book'
        ]);
    }

    #[Route('/add-dvd', name: 'app_dashboard_add_dvd')]
    public function addDvd(
        Request $request,
        EntityManagerInterface $entityManager,
        MediaQuotaService $quotaService
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $userItem = new UserItem();
        $userItem->setUser($user);
        
        $dvd = new Dvd();
        $userItem->setMedia($dvd);

        $form = $this->createForm(DvdCopyType::class, $userItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dvd);
            $entityManager->persist($userItem);
            $entityManager->flush();

            $this->addFlash('success', 'DVD ajouté avec succès à votre bibliothèque !');

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard/add_item.html.twig', [
            'form' => $form->createView(),
            'title' => 'Ajouter un DVD',
            'type' => 'dvd'
        ]);
    }

    #[Route('/subscription', name: 'app_dashboard_subscription')]
    public function subscription(
        SubscriptionRepository $subscriptionRepository,
        MediaQuotaService $quotaService
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $subscriptions = $subscriptionRepository->findAll();
        $quotaStats = $quotaService->getQuotaStats($user);

        return $this->render('dashboard/subscription.html.twig', [
            'subscriptions' => $subscriptions,
            'quota' => $quotaStats,
        ]);
    }

    #[Route('/subscription/upgrade/{id}', name: 'app_dashboard_subscription_upgrade')]
    public function upgrade(
        int $id,
        SubscriptionRepository $subscriptionRepository,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $subscription = $subscriptionRepository->find($id);

        if (!$subscription) {
            $this->addFlash('error', 'Plan d\'abonnement introuvable.');
            return $this->redirectToRoute('app_dashboard_subscription');
        }

        $user->setSubscription($subscription);
        $entityManager->flush();

        $this->addFlash('success', sprintf('Félicitations, vous êtes désormais membre %s ! Votre quota a été mis à jour.', $subscription->getName()));

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/subscription/downgrade-free', name: 'app_dashboard_subscription_free')]
    public function downgradeFree(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $user->setSubscription(null); // Back to free plan
        $entityManager->flush();

        $this->addFlash('success', 'Votre abonnement payant a été résilié. Vous êtes repassé sur le Plan Freemium (Gratuit).');

        return $this->redirectToRoute('app_dashboard');
    }
}