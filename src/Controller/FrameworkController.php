<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\PhpFwTrends\Exception\ShouldNotHappenException;
use TomasVotruba\PhpFwTrends\ValueObject\Option;
use TomasVotruba\PhpFwTrends\ValueObject\RouteName;

final class FrameworkController extends AbstractController
{
    public function __construct(
        private ParameterProvider $parameterProvider
    ) {
    }

    #[Route(path: 'framework/{frameworkName}', name: RouteName::FRAMEWORK)]
    public function __invoke(string $frameworkName): Response
    {
        $frameworkTrend = $this->matchFrameworkTrend($frameworkName);
        if ($frameworkTrend === []) {
            throw new ShouldNotHappenException($frameworkName);
        }

        return $this->render('homepage/framework.twig', [
            'title' => 'PHP Framework Trends',
            'framework_trend' => $frameworkTrend,
        ]);
    }

    /**
     * @todo hydrate to an objetc
     * @return mixed[]
     */
    private function matchFrameworkTrend(string $frameworkName): array
    {
        $phpFrameworkTrends = $this->parameterProvider->provideArrayParameter(Option::PHP_FRAMEWORK_TRENDS)['vendors'];
        foreach ($phpFrameworkTrends as $frameworkTrend) {
            if ($frameworkTrend['vendor_key'] === $frameworkName) {
                return $frameworkTrend;
            }
        }

        return [];
    }
}
