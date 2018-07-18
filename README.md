Codelocks API
=============

Lightweight PHP wrapper for the Codelocks Netcode API version 5.

Installing
----------

Install via composer:

```
composer require drinkynet/codelocks-api
```

Examples
--------

Create an instance of the Codelocks class with your API key and default Access Key

```php
$codelocks = new \drinkynet\Codelocks\Codelocks($key, $accessKey);

$netcode = $codelocks->netcode();
```

Get a netcode for lock `0000000000000000000000000000001a` that is valid now:

```php
$netcode->lock('0000000000000000000000000000001a')->get();
```

Get a netcode for lock `0000000000000000000000000000001a` that is valid for a specific time and date:

```php
$code = $netcode->lock('0000000000000000000000000000001a')
    ->date(new \DateTime('2016-09-23'))
    ->hour(9)
    ->duration(1)
    ->get();
```

Note: You can get the lock ID for each lock from the lock list returned by the ```->lock()``` method call


Get an initialisation sequence for a lock model:

```php
$codelocks = new \drinkynet\Codelocks\Codelocks($key, $accessKey);

// Init sequence data with default master code
$init = $codelocks->init()
    ->lockModel('K3CONNECT')
    ->get();

// Init sequence data with custom master code
$init = $codelocks->init()
    ->lockModel('K3CONNECT')
    ->masterCode('12345678')
    ->get();
```

Get a list of locks associated with the API credentials:

```php
$codelocks = new \drinkynet\Codelocks\Codelocks($key, $accessKey);

// Uses the accessKey set earlier
$locks = $codelocks->lock()->get();

// Use a different accessKey associated with the API key
$locks $codelocks->lock('abcde12345')->get();
```

Previous version
----------------

If you are using the version 4 API install the 2.0.1 version of this wrapper via composer.

If you are using the version 3 API install the 1.0.1 version of this wrapper via composer.
