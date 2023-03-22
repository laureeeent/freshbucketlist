<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use App\Services\Censurator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/wish')]
class WishController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/list', name: 'wish_list')]
    public function list(
        WishRepository $wishRepository,
        Censurator $censurator
    ): Response {
        $user = $this->getUser();
        $wishes = $user->getWishes();
        $wishesCensuree = $censurator->censure($wishes);

        return $this->render('wish/list.html.twig', compact('wishesCensuree'));
    }

    #[Route('/detail/{wish}', name: 'wish_detail')]
    public function detail(
        Wish $wish,
        Censurator $censurator
    ): Response {
        $censurator->purifyWish($wish);

        return $this->render('wish/detail.html.twig', compact('wish'));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/ajouter', name: 'wish_ajout')]
    public function ajouter(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $wish = new Wish();
        $user = $this->getUser();
        if (null === $this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            try {
                $wish->setDateCreated(new \DateTime());
                $wish->setIsPublished(true);
                $wish->setUser($user);
                $entityManager->persist($wish);
                $entityManager->flush();
                $this->addFlash('succes', 'le souhait a bien été enregistré !');

                $wishForm = $this->createForm(WishType::class, $wish);

                return $this->redirectToRoute('wish_ajout');
            } catch (\Exception $exception) {
                return $this->redirectToRoute('wish_ajout');
            }
        }

        return $this->render('wish/ajout.html.twig', compact('wishForm'));
    }
}
