<?php


namespace DependencyAnalysis\Config;


use DependencyAnalysis\DependencyGraph;
use DependencyAnalysis\Result\AnalysisResultPrinter;
use RuntimeException;

class PhpFileConfigParser implements ConfigParser
{
    public function parse(string $configFilePath): Config
    {
        if (!file_exists($configFilePath)) {
            throw new RuntimeException("Config file {$configFilePath} does not exist");
        }

        /** @noinspection PhpIncludeInspection */
        $configArray = include $configFilePath;

        $this->assertKeysExistsAndNotEmpty(['dependencies', 'path'], $configArray);

        $failOnNonPresentedNameSpace = array_key_exists('fail_on_non_presented_namespace', $configArray) ? $configArray['fail_on_non_presented_namespace'] : true;

        $skipVendorDir = true;
        if (array_key_exists('skip_vendor_dir', $configArray)) {
            $skipVendorDir = $configArray['skip_vendor_dir'];
        }

        $validForAll = [];
        if (array_key_exists('valid_for_all', $configArray)) {
            $validForAll = $configArray['valid_for_all'];
        }

        $vendorDir = realpath(dirname(PHPUNIT_COMPOSER_INSTALL));

        $config = new Config($configArray['path'], new DependencyGraph($configArray['dependencies'], $failOnNonPresentedNameSpace, $skipVendorDir, $vendorDir, $validForAll));

        if (array_key_exists('php_version', $configArray)) {
            $config->setPhpVersion($configArray['php_version']);
        }

        if (array_key_exists('allowed_extensions', $configArray)) {
            $config->setAllowedVersions($configArray['allowed_extensions']);
        }

        if (array_key_exists('output', $configArray)) {

            if (!is_a($configArray['output'], AnalysisResultPrinter::class, true)) {
                throw new RuntimeException(sprintf("Output type (config key: output) should be instance of %s. Got %s", AnalysisResultPrinter::class, $configArray['output']));
            }

            $config->setOutput($configArray['output']);
        }

        if (array_key_exists('output_path', $configArray)) {
            $config->setOutputPath($configArray['output_path']);
        }

        if (array_key_exists('skip_not_readable_files', $configArray)) {
            $config->setSkipNotReadable($configArray['skip_not_readable_files']);
        }

        return $config;
    }

    /**
     * @param string[] $keys
     * @param array $configArray
     *
     * @return void
     * @throws RuntimeException
     *
     */
    private function assertKeysExistsAndNotEmpty(array $keys, array $configArray): void
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $configArray)) {
                throw new RuntimeException("Required key {$key} not presented in config file");
            }

            if (empty($configArray[$key])) {
                throw new RuntimeException("Required key {$key} not present value or value is empty in config file");
            }
        }

    }

}