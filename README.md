# DONNEE

Tiny database abstraction based on sed

## Install

```shell
composer require ubermanu/donnee
```

## Usage

```php
$db = new \Donnee\Donnee('file.txt');

$insertedId = $db->insert('some string');
// 1

echo $db->get(1);
// 'some string'

$db->update(1, 'new string');
echo $db->get(1);
// 'new string'

$db->delete(1);
var_dump($db->get(1));
// null
```

## Tests

```shell
vendor/bin/phpunit
```
