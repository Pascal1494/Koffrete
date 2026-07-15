<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserItemRepository;
use App\Service\MediaQuotaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}