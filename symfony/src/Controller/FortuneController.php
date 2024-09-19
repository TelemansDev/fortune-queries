<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\FortuneCookieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class FortuneController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(CategoryRepository $categoryRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('q');

        $categories = $searchTerm ? $categoryRepository->searchByName($searchTerm) : $categoryRepository->findAllOrdered();

        return $this->render('fortune/homepage.html.twig',[
            'categories' => $categories
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_show')]
    public function showCategory(
        int                     $id,
        CategoryRepository      $categoryRepository,
        FortuneCookieRepository $fortuneCookieRepository
    ): Response {
        $category = $categoryRepository->findWithFortuneJoins($id);
        if (!$category) {
            throw $this->createNotFoundException('Category not found!');
        }

        $categoryFortuneStats = $fortuneCookieRepository->countNumberPrintedForCategory($category);

        return $this->render('fortune/showCategory.html.twig',[
            'category' => $category,
            'fortunesPrinted' => $categoryFortuneStats->getFortunesPrinted(),
            'fortunesAverage' => $categoryFortuneStats->getFortunesAverage(),
            'categoryName' => $categoryFortuneStats->getCategoryName(),
        ]);
    }
}
