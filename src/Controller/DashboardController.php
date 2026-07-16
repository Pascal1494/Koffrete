<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\CustomMedia;
use App\Entity\Dvd;
use App\Entity\Loan;
use App\Entity\User;
use App\Entity\UserItem;
use App\Form\BookCopyType;
use App\Form\CustomMediaCopyType;
use App\Form\DvdCopyType;
use App\Form\UserItemEditType;
use App\Repository\SubscriptionRepository;
use App\Repository\UserItemRepository;
use App\Service\MediaQuotaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

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

    #[Route('/add-custom', name: 'app_dashboard_add_custom')]
    public function addCustom(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $userItem = new UserItem();
        $userItem->setUser($user);
        
        $customMedia = new CustomMedia();
        $userItem->setMedia($customMedia);

        $form = $this->createForm(CustomMediaCopyType::class, $userItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Read unmapped description field to populate JSON attributes
            $description = $form->get('media')->get('description')->getData();
            $customMedia->setAttributes(['description' => $description]);

            // Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid('item-', true) . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/items',
                        $newFilename
                    );
                    $userItem->setImagePath('uploads/items/' . $newFilename);
                } catch (\Throwable $e) {
                    // Fail silently or handle upload error
                }
            }

            $entityManager->persist($customMedia);
            $entityManager->persist($userItem);
            $entityManager->flush();

            $this->addFlash('success', 'Média personnalisé ajouté avec succès !');

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard/add_item.html.twig', [
            'form' => $form->createView(),
            'title' => 'Ajouter un Autre Média',
            'type' => 'custom'
        ]);
    }

    #[Route('/edit/{id}', name: 'app_dashboard_edit')]
    public function edit(
        int $id,
        Request $request,
        UserItemRepository $userItemRepository,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $userItem = $userItemRepository->findOneBy(['id' => $id, 'user' => $user]);

        if (!$userItem) {
            $this->addFlash('error', 'Exemplaire introuvable.');
            return $this->redirectToRoute('app_dashboard');
        }

        $form = $this->createForm(UserItemEditType::class, $userItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Détails de votre exemplaire mis à jour !');

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard/edit_item.html.twig', [
            'form' => $form->createView(),
            'item' => $userItem,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_dashboard_delete', methods: ['GET', 'POST'])]
    public function delete(
        int $id,
        UserItemRepository $userItemRepository,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $userItem = $userItemRepository->findOneBy(['id' => $id, 'user' => $user]);

        if (!$userItem) {
            $this->addFlash('error', 'Exemplaire introuvable.');
            return $this->redirectToRoute('app_dashboard');
        }

        $entityManager->remove($userItem);
        $entityManager->flush();

        $this->addFlash('success', 'Exemplaire retiré de votre collection avec succès.');

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/lend/{id}', name: 'app_dashboard_lend', methods: ['GET', 'POST'])]
    public function lend(
        int $id,
        Request $request,
        UserItemRepository $userItemRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $userItem = $userItemRepository->findOneBy(['id' => $id, 'user' => $user]);

        if (!$userItem) {
            $this->addFlash('error', 'Exemplaire introuvable.');
            return $this->redirectToRoute('app_dashboard');
        }

        // Verify if currently lent
        foreach ($userItem->getLoans() as $loan) {
            if ($loan->getReturnedAt() === null) {
                $this->addFlash('error', 'Cet exemplaire est déjà prêté !');
                return $this->redirectToRoute('app_dashboard');
            }
        }

        if ($request->isMethod('POST')) {
            $submittedToken = $request->request->get('_token');
            if (!$this->isCsrfTokenValid('submit', $submittedToken)) {
                $this->addFlash('error', 'Jeton CSRF invalide.');
                return $this->redirectToRoute('app_dashboard');
            }

            $borrower = $request->request->get('borrower', '');
            $lentAtStr = $request->request->get('lentAt', '');
            $expectedReturnAtStr = $request->request->get('expectedReturnAt', '');

            if (empty($borrower)) {
                $this->addFlash('error', 'Veuillez renseigner le nom de l\'emprunteur.');
            } else {
                $loan = new Loan();
                $loan->setBorrower($borrower);
                $loan->setUserItem($userItem);

                if (!empty($lentAtStr)) {
                    $loan->setLentAt(new \DateTimeImmutable($lentAtStr));
                }
                if (!empty($expectedReturnAtStr)) {
                    $loan->setExpectedReturnAt(new \DateTimeImmutable($expectedReturnAtStr));
                }

                $entityManager->persist($loan);
                $entityManager->flush();

                // Send Confirmation Email
                try {
                    $email = (new Email())
                        ->from('noreply@koffrete.local')
                        ->to($user->getEmail())
                        ->subject('Confirmation de prêt - Koffrete 🤝')
                        ->html(sprintf(
                            '<p>Bonjour,</p>' .
                            '<p>Vous avez enregistré avec succès le prêt de votre exemplaire de "<strong>%s</strong>" à <strong>%s</strong>.</p>' .
                            '<p><strong>Date de prêt :</strong> %s<br>' .
                            '<strong>Date de retour prévue :</strong> %s</p>' .
                            '<p>Merci d\'utiliser Koffrete !</p>',
                            $userItem->getMedia()->getTitle(),
                            $borrower,
                            $loan->getLentAt()->format('d/m/Y'),
                            $loan->getExpectedReturnAt() ? $loan->getExpectedReturnAt()->format('d/m/Y') : 'Non définie'
                        ));

                    $mailer->send($email);
                } catch (\Throwable $e) {
                    // Fail silently so it doesn't block the redirect if mailer is not configured
                }

                $this->addFlash('success', sprintf('Exemplaire prêté avec succès à %s ! Un email de confirmation vous a été envoyé.', $borrower));

                return $this->redirectToRoute('app_dashboard');
            }
        }

        return $this->render('dashboard/lend_item.html.twig', [
            'item' => $userItem,
        ]);
    }

    #[Route('/return/{id}', name: 'app_dashboard_return')]
    public function returnItem(
        int $id,
        UserItemRepository $userItemRepository,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $userItem = $userItemRepository->findOneBy(['id' => $id, 'user' => $user]);

        if (!$userItem) {
            $this->addFlash('error', 'Exemplaire introuvable.');
            return $this->redirectToRoute('app_dashboard');
        }

        $activeLoan = null;
        foreach ($userItem->getLoans() as $loan) {
            if ($loan->getReturnedAt() === null) {
                $activeLoan = $loan;
                break;
            }
        }

        if (!$activeLoan) {
            $this->addFlash('error', 'Cet exemplaire n\'est pas marqué comme prêté.');
            return $this->redirectToRoute('app_dashboard');
        }

        $activeLoan->setReturnedAt(new \DateTimeImmutable());
        $entityManager->flush();

        $this->addFlash('success', 'Le retour de l\'exemplaire a été enregistré !');

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/loans', name: 'app_dashboard_loans')]
    public function activeLoans(UserItemRepository $userItemRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $items = $userItemRepository->findBy(['user' => $user]);
        $activeLoans = [];

        foreach ($items as $item) {
            foreach ($item->getLoans() as $loan) {
                if ($loan->getReturnedAt() === null) {
                    $activeLoans[] = [
                        'loan' => $loan,
                        'item' => $item,
                        'isOverdue' => $loan->getExpectedReturnAt() !== null && $loan->getExpectedReturnAt() < new \DateTimeImmutable('today'),
                    ];
                }
            }
        }

        return $this->render('dashboard/loans.html.twig', [
            'activeLoans' => $activeLoans,
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