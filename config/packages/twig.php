<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\Amnesia\ValueObject\Symfony\Extension\TwigExtension;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(TwigExtension::NAME, [
        TwigExtension::PATHS => [__DIR__ . '/../../templates'],
    ]);
};
