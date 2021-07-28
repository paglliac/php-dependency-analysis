<?php


namespace DependencyAnalysis\Config;


use DependencyAnalysis\DependencyGraph;
use RuntimeException;

class PhpFileConfigParser implements ConfigParser
{
    public function parse(string $configFilePath): Config
    {
        if (!file_exists($configFilePath)) {
            throw new RuntimeException("Config file {$configFilePath} does not exists");
        }

        /** @noinspection PhpIncludeInspection */
        $configArray = include $configFilePath;

        $this->assertKeysExistsAndNotEmpty(['dependencies', 'path'], $configArray);

        $failOnNonPresentedNameSpace = array_key_exists('fail_on_non_presented_namespace', $configArray) ? $configArray['fail_on_non_presented_namespace'] : true;
        $config = new Config($configArray['path'], new DependencyGraph($configArray['dependencies'], $failOnNonPresentedNameSpace));

        if (array_key_exists('php_version', $configArray)) {
            $config->setPhpVersion($configArray['php_version']);
        }

        if (array_key_exists('allowed_extensions', $configArray)) {
            $config->setAllowedVersions($configArray['allowed_extensions']);
        }

        if (array_key_exists('output', $configArray)) {
            $config->setOutput($configArray['output']);
        }

        if (array_key_exists('output_path', $configArray)) {
            $config->setOutputPath($configArray['output_path']);
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