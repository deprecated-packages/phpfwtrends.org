<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\EasyHydrator\ArrayToValueObjectHydrator;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\PhpFwTrends\ValueObject\Option;
use TomasVotruba\PhpFwTrends\ValueObject\RouteName;
use TomasVotruba\PhpFwTrends\ValueObject\VendorData;

final class HomepageController extends AbstractController
{
    public function __construct(
        private ParameterProvider $parameterProvider,
        private ArrayToValueObjectHydrator $arrayToValueObjectHydrator
    ) {
    }

    #[Route(path: '/', name: RouteName::HOMEPAGE)]
    public function __invoke(): Response
    {
        $phpFrameworkTrends = $this->parameterProvider->provideArrayParameter(Option::PHP_FRAMEWORK_TRENDS);

        $vendorDatas = [];
        foreach ($phpFrameworkTrends['vendors'] as $phpFrameworkTrend) {
            $vendorDatas[] = $this->arrayToValueObjectHydrator->hydrateArray($phpFrameworkTrend, VendorData::class);
        }

        return $this->render('homepage/index.twig', [
            'title' => 'PHP Framework Trends',
            'promo_post_url' => 'https://tomasvotruba.com/blog/2019/04/11/trends-of-php-frameworks-in-numbers/',
            'vendor_datas' => $vendorDatas,
        ]);
    }
}
