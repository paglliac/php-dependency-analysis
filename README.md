# PHP Dependency analyzer
<p>
	<a href="https://github.com/paglliac/php-dependency-analysis/actions"><img src="https://github.com/paglliac/php-dependency-analysis/actions/workflows/php.yml/badge.svg" alt="Build Status"></a>
</p>

PHP DA is tool for check and support dependencies inside your project clear.

For example:
- You have project with 3 root namespaces: Domain, Application, Infrastructure
- You want to be sure dependencies in your project defined as graph
```php
[
    'dependencies' => [
        '\Domain' => null,
        '\Application' => ['\Domain'],
        '\Infrastructure' => ['\Domain', '\Application']
    ]
];
```
That means all classes from **Domain** namespace should use only classes from this namespace, and possibly vendor (it s configured).

All classes from **Application** can use classes from **Domain** and **Application** namespaces, but not from **Infrastructure**, etc

If some classes using dependencies not satisfied defined dependency graph, you give errors in report:

```
Have been analyzed 4 files
You have dependency problems in 2 files in your project:

Class \Application\TrackingService have errors:
    - Class \Application\TrackingService using class \Infrastructure\ShipImplementation which not satisfy dependency graph

Class \Domain\Cargo have errors:
    - Class \Domain\Cargo using class \Application\TrackingService which not satisfy dependency graph
    - Class \Domain\Cargo using class \Infrastructure\ShipImplementation which not satisfy dependency graph

```

## Use cases

It can be useful in some cases for example:
- You want to extract part of your application in separate service, you define valid dependencies and run php-da for investigate workload
- You want to support low coupling in your application, you define valid dependencies and run php-da on your CI server for every MR, only for changed files
- You want to make visible structure changes of your application for all developers, now it is visible in php-da config  



## Quick start

Install library using composer 

```
composer require paglliac/dependency-analysis
```

### Configuration

Add configuration file `config.php` to root of your project :

```php
return [
    /**
     * REQUIRED
     * Dependencies Graph
     *
     * Description of valid dependencies in project
     *
     * This config means
     *
     * Package (every class in namespace) \Domain can use only classes from namespace \Domain or vendor dependencies
     * Package \Application can use only classes from namespaces \Domain, \Application or vendor dependencies
     * Package \Infrastructure can use only classes from namespaces \Domain, \Application, \Infrastructure or vendor dependencies
     */
    'dependencies' => [
        '\Domain' => null,
        '\Application' => ['\Domain'],
        '\Infrastructure' => ['\Domain', '\Application']
    ],

    /**
     * REQUIRED
     * Source path where dependencies will be analyzed
     */
    'path' => __DIR__,

    /**
     * OPTIONAL
     *
     * Make available to use vendor dependencies in whole project
     *
     * true - all project classes can use vendor dependencies
     * false - all project can not use vendor dependencies
     */
    'skip_vendor_dir' => true,

    /**
     * OPTIONAL
     * Flag that define how to do when some files placed in namespaces not presented in Dependencies Graph
     *
     * true - mark class as having incorrect dependencies
     * false - skip this file
     *
     * For example, in directory we have namespace \SomeNamespace with class \SomeNamespace\SomeClass
     * if flag is true, it will be marked as incorrect file, if flag is true, this file wil be marked as correct
     */
    'fail_on_non_presented_namespace' => false,

    /**
     * OPTIONAL
     * Flag for php parser, correct values:
     *
     * PhpParser\ParserFactory::PREFER_PHP7 - 1 (default)
     * PhpParser\ParserFactory::PREFER_PHP5 - 2
     * PhpParser\ParserFactory::ONLY_PHP7 - 3
     * PhpParser\ParserFactory::ONLY_PHP5 - 4
     */
    'php_version' => PhpParser\ParserFactory::PREFER_PHP7,

    /**
     * OPTIONAL
     * 
     * List of allowed files extensions, all files with other extensions will be skipped from analysis
     * 
     * Default - ['php']
     */
    'allowed_extensions' => ['php']
];
```

### Running

Run dependency validation:

```
/vendor/bin/php-da -c config.php [files filter]
```
Options:
- `-c` or `--config` is required option with the relative path to config file

Arguments:
- `[files filter]` list of files for analysis, it's useful to use in CI combine with --diff 



### Example of output

```
Have been analyzed 4 files
You have dependency problems in 2 files in your project:

Class \Application\TrackingService have errors:
    - Class \Application\TrackingService using class \Infrastructure\ShipImplementation which not satisfy dependency graph

Class \Domain\Cargo have errors:
    - Class \Domain\Cargo using class \Application\TrackingService which not satisfy dependency graph
    - Class \Domain\Cargo using class \Infrastructure\ShipImplementation which not satisfy dependency graph

```
