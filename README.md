# Dot Notation

[![Build Status](https://travis-ci.org/dschoenbauer/dot-notation.svg?branch=develop)](https://travis-ci.org/dschoenbauer/dot-notation)
[![Coverage Status](https://coveralls.io/repos/github/dschoenbauer/dot-notation/badge.svg?branch=develop)](https://coveralls.io/github/dschoenbauer/dot-notation?branch=develop)

## Purpose
Simplifies access to large array structures

## Installation
````
    composer require dschoenbauer/dot-notation
````

## Testing

````
    ./vendor/bin/phpunit tests
````


## Example

```
<?php

use DSchoenbauer\DotNotation\ArrayDotNotation;

$mongoConnection = [
    'mongo' => [
        'default' => [
            'user' => 'username',
            'password' => 's3cr3t'
        ]
    ]
];
$config = new ArrayDotNotation($mongoConnection);

// Get plain value

$user = $config->get('mongo.default.user');
/*
    $user = 'username';
*/ 

// Get array value

$mongoDefault = $config->get('mongo.default'); 
/* 
    $mongoDefault = ['user' => 'username', 'password' => 's3cr3t'];
*/

// Set values

$config = $config->set('mongo.numbers', [2, 3, 5, 7, 11]);
$configDump = $config->getData();
/*
    $configDump = [
        'mongo' => [
            'numbers' => [2, 3, 5, 7, 11]
        ],
        'title' => 'Dot Notation'
    ];
*/
```
