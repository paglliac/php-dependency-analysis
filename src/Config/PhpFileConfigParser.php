<?php


namespace DependencyAnalysis\Config;


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

        $config = new Config($configArray['path'], new DependencyGraph($configArray['dependencies']));

        if (array_key_exists('php_version', $configArray)) {
            $config->setPhpVersion($config['php_version']);
        }

        if (array_key_exists('allowed_extensions', $configArray)) {
            $config->setAllowedVersions($config['allowed_extensions']);
        }

        return $config;
    }

    /**
     * @param string[] $keys
     * @param array $configArray
     *
     * @throws RuntimeException
     *
     * @return void
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