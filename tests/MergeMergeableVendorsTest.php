<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\Tests;

use InvalidArgumentException;
use Nette\Utils\Json;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\SmartFileSystem;
use TomasVotruba\PhpFwTrends\MergeableVendors;
use TomasVotruba\PhpFwTrends\MergeMergeableVendors;
use TomasVotruba\PhpFwTrends\ValueObject\Option;
use TomasVotruba\PhpFwTrends\ValueObject\PackageData;
use TomasVotruba\PhpFwTrends\ValueObject\VendorData;

final class MergeMergeableVendorsTest extends TestCase
{
    private MergeMergeableVendors $merger;

    protected function setUp(): void
    {
        $parameterBag = new ParameterBag();
        $parameterBag->set(Option::FRAMEWORKS_MERGEABLE_VENDORS, [
            'laminas' => ['laminas-api-tools', 'mezzio'],
            'zendframework' => ['zfcampus'],
        ]);

        $container = new Container($parameterBag);
        $parameterProvider = new ParameterProvider($container);
        $mergeableVendors = new MergeableVendors($parameterProvider);
        $this->merger = new MergeMergeableVendors($mergeableVendors);
    }

    /**
     * @return VendorData[]
     */
    public function prepareVendorData(): array
    {
        $data = Json::decode((new SmartFileSystem())->readFile(__DIR__ . '/assets/trend_data.json'), Json::FORCE_ARRAY);
        $vendorsData = [];
        foreach ($data as $vendor) {
            $packagesData = [];
            if (isset($vendor['packages_data'])) {
                foreach ($vendor['packages_data'] as $packageData) {
                    $packagesData[] = new PackageData(
                        $packageData['package_name'],
                        $packageData['last_year_trend'],
                        $packageData['younger_chunk'],
                        $packageData['older_chunk'],
                    );
                }
            }

            $vendorsData[$vendor['vendor_key']] = new VendorData(
                $vendor['vendor_key'],
                $vendor['vendor_name'],
                $vendor['vendor_total_last_year'],
                $vendor['vendor_total_previous_year'],
                $packagesData,
            );
        }

        return $vendorsData;
    }

    /**
     * @param VendorData[] $vendorsData
     */
    public function getVendorDataFor(string $vendorKey, array $vendorsData): VendorData
    {
        $vendorData = array_reduce($vendorsData, function (?VendorData $vendorData, VendorData $vendorToTest) use (
            $vendorKey
        ): ?VendorData {
            if ($vendorData instanceof VendorData) {
                return $vendorData;
            }

            if ($vendorKey === $vendorToTest->getVendorKey()) {
                return $vendorToTest;
            }

            return null;
        });

        if ($vendorData === null) {
            throw new InvalidArgumentException(sprintf('Vendor key "%s" not found in list of vendors', $vendorKey));
        }

        return $vendorData;
    }

    /**
     * @return VendorData[]
     */
    public function testMergeableVendorsAreNotRepresentedInTopLevelVendorsFollowingMerge(): array
    {
        $vendorsData = $this->prepareVendorData();
        $vendorsData = $this->merger->merge($vendorsData);

        $vendorNames = array_map(fn (VendorData $vendorData): string => $vendorData->getVendorKey(), $vendorsData);

        $this->assertArrayNotHasKey('laminas-api-tools', $vendorNames);
        $this->assertArrayNotHasKey('mezzio', $vendorNames);
        $this->assertArrayNotHasKey('zfcampus', $vendorNames);
        $this->assertArrayHasKey('laminas', $vendorNames);
        $this->assertArrayHasKey('symfony', $vendorNames);
        $this->assertArrayHasKey('doctrine', $vendorNames);
        $this->assertArrayHasKey('illuminate', $vendorNames);
        $this->assertArrayHasKey('yiisoft', $vendorNames);
        $this->assertArrayHasKey('nette', $vendorNames);
        $this->assertArrayHasKey('cakephp', $vendorNames);
        $this->assertArrayHasKey('zendframework', $vendorNames);

        return $vendorsData;
    }

    /**
     * @depends testMergeableVendorsAreNotRepresentedInTopLevelVendorsFollowingMerge
     * @param VendorData[] $vendorsData
     */
    public function testLaminasVendorDataAggregatesTotalsFromMezzioAndApiToolsFollowingMerge(array $vendorsData): void
    {
        $expectedTotals = [
            'total_last_year' => 78416184,
            'total_previous_year' => 38650253,
        ];

        $laminasVendorData = $this->getVendorDataFor('laminas', $vendorsData);

        $actualTotals = [
            'total_last_year' => $laminasVendorData->getVendorTotalLastYear(),
            'total_previous_year' => $laminasVendorData->getVendorTotalPreviousYear(),
        ];

        $this->assertSame($expectedTotals, $actualTotals);
    }

    /**
     * @depends testMergeableVendorsAreNotRepresentedInTopLevelVendorsFollowingMerge
     * @param VendorData[] $vendorsData
     */
    public function testZendFrameworkVendorDataAggregatesTotalsFromZfcampusFollowingMerge(array $vendorsData): void
    {
        $expectedTotals = [
            'total_last_year' => 75502534,
            'total_previous_year' => 99501340,
        ];

        $zendframeworkVendorData = $this->getVendorDataFor('zendframework', $vendorsData);

        $actualTotals = [
            'total_last_year' => $zendframeworkVendorData->getVendorTotalLastYear(),
            'total_previous_year' => $zendframeworkVendorData->getVendorTotalPreviousYear(),
        ];

        $this->assertSame($expectedTotals, $actualTotals);
    }

    /**
     * @depends testMergeableVendorsAreNotRepresentedInTopLevelVendorsFollowingMerge
     * @param VendorData[] $vendorsData
     */
    public function testLaminasPackagesDataIncludesMezzioAndApiToolsPackagesFollowingMerge(array $vendorsData): void
    {
        $expectedPackageNames = [
            'laminas/laminas-text',
            'laminas/laminas-feed',
            'laminas/laminas-di',
            'laminas/laminas-captcha',
            'laminas/laminas-escaper',
            'laminas/laminas-server',
            'laminas/laminas-psr7bridge',
            'laminas/laminas-log',
            'laminas/laminas-soap',
            'laminas/laminas-session',
            'laminas/laminas-modulemanager',
            'laminas/laminas-serializer',
            'laminas/laminas-config',
            'laminas/laminas-crypt',
            'laminas/laminas-stdlib',
            'laminas/laminas-uri',
            'laminas/laminas-mail',
            'laminas/laminas-view',
            'laminas/laminas-math',
            'laminas/laminas-console',
            'laminas/laminas-db',
            'laminas/laminas-mvc',
            'laminas/laminas-i18n',
            'laminas/laminas-mime',
            'laminas/laminas-hydrator',
            'laminas/laminas-http',
            'laminas/laminas-code',
            'laminas/laminas-loader',
            'laminas/laminas-form',
            'laminas/laminas-eventmanager',
            'laminas/laminas-dependency-plugin',
            'laminas/laminas-servicemanager',
            'laminas/laminas-json',
            'laminas/laminas-barcode',
            'laminas/laminas-validator',
            'laminas/laminas-diactoros',
            'laminas/laminas-filter',
            'laminas/laminas-inputfilter',
            'laminas/laminas-i18n-resources',
            'laminas/laminas-permissions-rbac',
            'laminas/laminas-paginator',
            'laminas/laminas-permissions-acl',
            'laminas/laminas-navigation',
            'laminas/laminas-diagnostics',
            'laminas/laminas-dom',
            'laminas/laminas-authentication',
            'laminas/laminas-mvc-plugin-flashmessenger',
            'laminas/laminas-httphandlerrunner',
            'laminas/laminas-ldap',
            'laminas/laminas-mvc-console',
            'laminas/laminas-router',
            'laminas/laminas-cache',
            'laminas/laminas-xmlrpc',
            'laminas/laminas-stratigility',
            'laminas/laminas-test',
            'laminas/laminas-mvc-i18n',
            'laminas/laminas-component-installer',
            'laminas/laminas-xml',
            'laminas/laminas-development-mode',
            'laminas/laminas-composer-autoloading',
            'laminas-api-tools/api-tools-api-problem',
            'laminas-api-tools/api-tools-oauth2',
            'laminas-api-tools/api-tools-content-negotiation',
            'mezzio/mezzio-template',
            'mezzio/mezzio',
            'mezzio/mezzio-fastroute',
            'mezzio/mezzio-router',
            'mezzio/mezzio-helpers',
        ];

        $laminasVendorData = $this->getVendorDataFor('laminas', $vendorsData);
        $packageNames = array_map(
            fn (PackageData $packageData): string => $packageData->getPackageName(),
            $laminasVendorData->getPackagesData()
        );

        $this->assertSame($expectedPackageNames, $packageNames);
    }

    /**
     * @depends testMergeableVendorsAreNotRepresentedInTopLevelVendorsFollowingMerge
     * @param VendorData[] $vendorsData
     */
    public function testZendFrameworkPackagesDataIncludesZfcampusPackagesFollowingMerge(array $vendorsData): void
    {
        $expectedPackageNames = [
            'zendframework/zend-version',
            'zendframework/zend-file',
            'zendframework/zend-progressbar',
            'zendframework/zend-tag',
            'zendframework/zend-i18n-resources',
            'zendframework/zend-test',
            'zendframework/zend-permissions-rbac',
            'zendframework/zend-navigation',
            'zendframework/zendframework',
            'zendframework/zend-permissions-acl',
            'zendframework/zend-dom',
            'zendframework/zend-debug',
            'zendframework/zend-paginator',
            'zendframework/zend-authentication',
            'zendframework/zendservice-google-gcm',
            'zendframework/zendservice-apple-apns',
            'zendframework/zend-xmlrpc',
            'zendframework/zendxml',
            'zendframework/zend-mvc-i18n',
            'zendframework/zend-cache',
            'zendframework/zend-memory',
            'zendframework/zendframework1',
            'zendframework/zend-mvc-plugin-flashmessenger',
            'zendframework/zend-code',
            'zendframework/zend-eventmanager',
            'zendframework/zend-mvc-console',
            'zendframework/zend-router',
            'zendframework/zend-uri',
            'zendframework/zend-validator',
            'zendframework/zend-hydrator',
            'zendframework/zenddiagnostics',
            'zendframework/zend-loader',
            'zendframework/zend-http',
            'zendframework/zend-json',
            'zendframework/zend-config',
            'zendframework/zend-servicemanager',
            'zendframework/zend-filter',
            'zendframework/zend-soap',
            'zendframework/zend-stratigility',
            'zendframework/zend-server',
            'zendframework/zendpdf',
            'zendframework/zend-i18n',
            'zendframework/zend-httphandlerrunner',
            'zendframework/zend-serializer',
            'zendframework/zend-math',
            'zendframework/zend-mime',
            'zendframework/zend-log',
            'zendframework/zend-mvc',
            'zendframework/zend-mail',
            'zendframework/zend-stdlib',
            'zendframework/zend-modulemanager',
            'zendframework/zend-inputfilter',
            'zendframework/zend-escaper',
            'zendframework/zend-db',
            'zendframework/zend-crypt',
            'zendframework/zend-form',
            'zendframework/zend-console',
            'zendframework/zend-view',
            'zendframework/zend-component-installer',
            'zendframework/zend-expressive-helpers',
            'zendframework/zend-di',
            'zendframework/zend-session',
            'zendframework/zend-expressive-template',
            'zendframework/zend-barcode',
            'zendframework/zend-psr7bridge',
            'zendframework/zend-text',
            'zendframework/zend-expressive',
            'zendframework/zend-expressive-router',
            'zendframework/zend-captcha',
            'zendframework/zend-diactoros',
            'zendframework/zend-feed',
            'zendframework/zend-ldap',
            'zfcampus/zf-rest',
            'zfcampus/zf-hal',
            'zfcampus/zf-content-validation',
            'zfcampus/zf-rpc',
            'zfcampus/zf-mvc-auth',
            'zfcampus/zf-apigility',
            'zfcampus/zf-versioning',
            'zfcampus/zf-oauth2',
            'zfcampus/zf-apigility-provider',
            'zfcampus/zf-content-negotiation',
            'zfcampus/zf-api-problem',
            'zfcampus/zf-development-mode',
        ];

        $zendframeworkVendorData = $this->getVendorDataFor('zendframework', $vendorsData);
        $packageNames = array_map(
            fn (PackageData $packageData): string => $packageData->getPackageName(),
            $zendframeworkVendorData->getPackagesData()
        );

        $this->assertSame($expectedPackageNames, $packageNames);
    }
}
