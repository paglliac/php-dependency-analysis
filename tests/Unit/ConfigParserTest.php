<?php

namespace DependencyAnalysis\Tests\Unit;

use DependencyAnalysis\Config\Config;
use DependencyAnalysis\Config\PhpFileConfigParser;
use PHPUnit\Framework\TestCase;

class ConfigParserTest extends TestCase
{
    public function testValidConfigParse()
    {
        $configParser = new PhpFileConfigParser();
        $config = $configParser->parse(__DIR__ . '/../Data/simpleProject/config.php');

        $this->assertInstanceOf(Config::class, $config);
        $this->assertStringContainsString('/Data/simpleProject', $config->getPath());
        $this->assertEquals([
            '\Domain' => null,
            '\Application' => ['\Domain'],
            '\Infrastructure' => ['\Domain']
        ], $config->getDependencyGraph()->toArray());

    }

}
