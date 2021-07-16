<?php


namespace DependencyAnalysis;


class ConfigParser
{
    public function parse(string $configPath): Config
    {
        return new Config();
    }

}