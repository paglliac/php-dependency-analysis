<?php

namespace DependencyAnalysis\Config;

interface ConfigParser
{
    public function parse(string $configFilePath): Config;
}