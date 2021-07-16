<?php

return [
    'dependencies' => [
        '\Domain' => null,
        '\Application' => ['\Domain'],
        '\Infrastructure' => ['\Domain']
    ],
    'path' => __DIR__ . '/simpleProject'
];
