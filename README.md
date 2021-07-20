# PHP Dependency analyzer

PHP DA oriented on analysis of project component dependencies

<p>
	<a href="https://github.com/paglliac/php-dependency-analysis/actions"><img src="https://github.com/paglliac/php-dependency-analysis/actions/workflows/php.yml/badge.svg" alt="Build Status"></a>
</p>

## Quick start

Install library using composer 

```
composer require paglliac/dependency-analysis
```

Add configuration file `config.php` to root of your project :

```
return [
    'dependencies' => [
        '\Domain' => null,
        '\Application' => ['\Domain'],
        '\Infrastructure' => ['\Domain']
    ],
    'path' => __DIR__,
    'fail_on_non_presented_namespace' => false,
    'php_version' => 1,
    'allowed_extensions' => ['php']
]
```

Run dependency validation:

```
/vendor/bin/php-da -c config.php
```