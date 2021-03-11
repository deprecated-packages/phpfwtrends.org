<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\PhpFwTrends\FileSystem\ParametersConfigDumper;
use TomasVotruba\PhpFwTrends\Result\VendorDataFactory;
use TomasVotruba\PhpFwTrends\ValueObject\Option;

final class GenerateStatsCommand extends Command
{
    /**
     * @var string
     */
    private const VENDORS = 'vendors';

    public function __construct(
        private SymfonyStyle $symfonyStyle,
        private VendorDataFactory $vendorDataFactory,
        private ParameterProvider $parameterProvider,
        private ParametersConfigDumper $parametersConfigDumper
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Generates downloads stats data for MVC PHP vendors');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $frameworksVendorToName = $this->parameterProvider->provideArrayParameter(Option::FRAMEWORKS_VENDOR_TO_NAME);
        $vendorsData = $this->vendorDataFactory->createVendorsData($frameworksVendorToName);

        foreach ($vendorsData[self::VENDORS] as $key => $vendorData) {
            $vendorsData[self::VENDORS][$key] = $vendorData->toArray();
        }

        $fileInfo = $this->parametersConfigDumper->dumpPhp(Option::PHP_FRAMEWORK_TRENDS, $vendorsData);

        $message = sprintf(
            'Data for %d frameworks dumped into" %s" file',
            count($vendorsData[self::VENDORS]),
            $fileInfo->getRelativeFilePathFromCwd()
        );
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
