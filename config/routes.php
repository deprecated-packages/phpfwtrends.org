<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symplify\Amnesia\ValueObject\Symfony\Routing;

return static function (RoutingConfigurator $routes): void {
    $routes->import(__DIR__ . '/../src/Controller', Routing::TYPE_ANNOTATION);
};
