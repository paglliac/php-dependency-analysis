<?php

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
];
