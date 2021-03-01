<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\PhpFwTrends\ValueObject\Option;
use TomasVotruba\PhpFwTrends\ValueObject\RouteName;

final class HomepageController extends AbstractController
{
    public function __construct(
        private ParameterProvider $parameterProvider
    ) {
    }

    #[Route(path: '/', name: RouteName::PHP_FRAMEWORK_TRENDS)]
    public function __invoke(): Response
    {
        $phpFrameworkTrends = $this->parameterProvider->provideArrayParameter(Option::PHP_FRAMEWORK_TRENDS);

        return $this->render('homepage/index.twig', [
            'title' => 'PHP Framework Trends',
            'promo_post_url' => 'https://tomasvotruba.com/blog/2019/04/11/trends-of-php-frameworks-in-numbers/',
            'php_framework_trends' => $phpFrameworkTrends,
        ]);
    }
}
