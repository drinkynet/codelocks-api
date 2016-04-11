Codelocks API
=============

Lightweight PHP wrapper for the Codelocks Netcode API version 3.

Installing
----------

Install via composer:

```
composer require drinkynet/codelocks-api
```

Examples
--------

Create an instance of the Codelocks class with your API key and pairing ID

```php
$codelocks = new \drinkynet\Codelocks($key, $pid);

$netcode = $codelocks->netcode();
```

Get a netcode for lock `N000001` that is valid now:

```php
$netcode->lock('N000001')->get();
```

Get a netcode for lock `N000000` that is valid in the future:

```php
$code = $netcode->lock('N000000')
    ->date(new \DateTime('2016-09-23'))
    ->hour(9)
    ->duration(1)
    ->get();
```
