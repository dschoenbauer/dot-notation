# Dot Notation

[![Build Status](https://travis-ci.org/dschoenbauer/dot-notation.svg?branch=develop)](https://travis-ci.org/dschoenbauer/dot-notation)
[![Coverage Status](https://coveralls.io/repos/github/dschoenbauer/dot-notation/badge.svg?branch=develop)](https://coveralls.io/github/dschoenbauer/dot-notation?branch=develop)
[![License](https://img.shields.io/packagist/l/dschoenbauer/dot-notation.svg)](https://github.com/dschoenbauer/dot-notation)
[![Downloads](https://img.shields.io/packagist/dt/dschoenbauer/dot-notation.svg)](https://packagist.org/packages/dschoenbauer/dot-notation)
[![Version](https://img.shields.io/packagist/v/dschoenbauer/dot-notation.svg)](https://github.com/dschoenbauer/dot-notation/releases)


[![License](https://img.shields.io/packagist/l/dschoenbauer/dot-notation.svg)](https://github.com/dschoenbauer/dot-notation)
[![Downloads](https://img.shields.io/packagist/dt/dschoenbauer/dot-notation.svg)](https://packagist.org/packages/dschoenbauer/dot-notation)
[![Version](https://img.shields.io/packagist/v/dschoenbauer/dot-notation.svg)](https://github.com/dschoenbauer/dot-notation/releases)


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
use DSchoenbauer\DotNotation\ArrayDotNotation;

$mongoConnection = [ 
    'mongo' => [ 
        'default' => [  'user' => 'username', 'password' => 's3cr3t' ]
    ]
];
$config = new ArrayDotNotation($mongoConnection);
        --- or ---
$config = ArrayDotNotation::with($mongoConnection);
```

### GET
```
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
```

### SET
````
$configDump = $config->set('mongo.numbers', [2, 3, 5, 7, 11])->getData();
/*
    $configDump = [
        'mongo' => [
            'default' => [  'user' => 'username', 'password' => 's3cr3t' ],
            'numbers' => [2, 3, 5, 7, 11]
        ],
        'title' => 'Dot Notation'
    ];
*/
````

### MERGE
````
$configDump = $config->merge('mongo.default', ['user' => 'otherUser','active' => true])->getData();
/*
    $configDump = [
        'mongo' => [
           'default' => [  'user' => 'otherUser', 'password' => 's3cr3t','active' => true ]
        ],
        'title' => 'Dot Notation'
    ];
*/
````

### REMOVE
````
$configDump = $config->remove('mongo.default.user')->getData();
/*
    $configDump = [
        'mongo' => [
           'default' => [  'password' => 's3cr3t' ]
        ],
        'title' => 'Dot Notation'
    ];
*/
````

### NOTATION TYPE
````
// Tired of dots? Change it.
$user = $config->setNotationType(',')->get('mongo,default,user');
/*
    $user = 'username';
*/ 
````

### WITH
````
//Functional fluent fun
$user = ArrayDotNotation::with($mongoConnection)->get('mongo.default.user');
/*
    $user = 'username';
*/ 

````

### HAS
````
// Validates that the dot notation path is present in the data.
$isPresent = $config->has('mongo,default,user');
/*
    $isPresent = true;
*/ 
````
