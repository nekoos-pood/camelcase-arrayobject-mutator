# CamelCase Array/Object Mutator

This is a utility that allows you to use an array-object that ignores key style cases and mutate the keys as camel case style

## Install

```php
composer require nekoos-pood/camelcase-arrayobject-mutator
```

## Definition

This entity implements `ArrayObject` see [Documentation](https://www.php.net/manual/en/class.arrayobject.php)

## Usage

```php
use NekoOs\Pood\Support\CamelCaseArrayObjectMutator;
```

### Basic use

```php
$thing = new CamelCaseArrayObjectMutator([
  'this is first item with increment key',
  'associative-key' => 'this is item with associative key'
]);

$thing->snake_case   = 'this is item with key using snake case';
$thing['kebab-case'] = 'this is item with key using kebab case';
$thing[]             = 'this is item with key using increment key';

$thing['snake_case']    // return 'this is item with key using snake case'
$thing['kebab-case']    // return 'this is item with key using kebab case'
$thing[0]               // return 'this is first item with increment key'
$thing[1]               // return 'this is item with key using increment key'
$thing['kebab-case']    // return 'this is item with key using kebab case'
$thing['snakeCase']     // return 'this is item with key using snake case'
$thing['kebabCase']     // return 'this is item with key using kebab case'
$thing->snake_case      // return 'this is item with key using snake case'
$thing->snakeCase       // return 'this is item with key using snake case'
$thing->kebabCase       // return 'this is item with key using kebab case'
$thing->undefined       // throw error 'Undefined property: NekoOs\Pood\Support\CamelCaseArrayObjectMutator::$undefined'

get_object_vars($thing) // return array (
                         //   'associativeKey' => 'this is item with associative key',
                         //   'snakeCase'      => 'this is item with key using snake case',
                         //   'kebabCase'      => 'this is item with key using kebab case',
                         // )
```

The object ignores the case styles of the keys

```php
$thing->snakeCase    = 'replace value with key using snake case'
$thing['kebabCase']  = 'replace value with key using kebab case'

$thing['snake_case']    // return 'replace value with key using snake case'
$thing['kebab-case']    // return 'replace value with key using kebab case'
$thing['snakeCase']     // return 'replace value with key using snake case'
$thing['kebabCase']     // return 'replace value with key using kebab case'
$thing->snake_case      // return 'replace value with key using snake case'
$thing->snakeCase       // return 'replace value with key using snake case'
$thing->kebabCase       // return 'replace value with key using kebab case'
```

### How to get the values with original indexes?

```php
$thing->getStorage()
```

### Custom use


```php
// change behavior from instance as object common without mutation of keys
$thing->behavior(CamelCaseArrayObjectMutator::PREFER_ORIGINAL_KEYS);

get_object_vars($thing) // return array (
                         //   'associative-key' => 'this is item with associative key',
                         //   'snake_case'      => 'this is item with key using snake case',
                         //   'kebab-case'      => 'this is item with key using kebab case',
                         // )
```

#### Global configuration

```php
// change behavior by default as object common without mutation of keys
CamelCaseArrayObjectMutator::defaultBehavior(CamelCaseArrayObjectMutator::PREFER_ORIGINAL_KEYS);

// Enabled throw errors by default on undefined fields
CamelCaseArrayObjectMutator::defaultBehavior(CamelCaseArrayObjectMutator::DEBUG_ON_UNDEFINDED);
// Disabled throw errors by default on undefined fields
CamelCaseArrayObjectMutator::defaultBehavior(~CamelCaseArrayObjectMutator::DEBUG_ON_UNDEFINDED);
```