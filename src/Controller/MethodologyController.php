<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\PhpFwTrends\ValueObject\Option;
use TomasVotruba\PhpFwTrends\ValueObject\RouteName;

final class MethodologyController extends AbstractController
{
    public function __construct(
        private ParameterProvider $parameterProvider
    ) {
    }

    #[Route(path: 'how-is-it-measured', name: RouteName::METHODOLOGY)]
    public function __invoke(): Response
    {
        $phpFrameworkTrends = $this->parameterProvider->provideArrayParameter(Option::PHP_FRAMEWORK_TRENDS);

        return $this->render('homepage/methodology.twig', [
            'title' => 'PHP Framework Trends',
        ]);
    }
}
