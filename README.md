# PayumPaytrail

## Installation

The preferred way to install the library is using [composer](http://getcomposer.org/).
Run composer require to add dependencies to _composer.json_:

```bash
php composer.phar require paradigm/payum-paytrail
```

## config.php

We have to only add the gateway factory. All the rest remain the same:

```php
<?php
//config.php

use Payum\Core\PayumBuilder;
use Payum\Core\Payum;

/** @var Payum $payum */
$payum = (new PayumBuilder())
    ->addDefaultStorages()
    ->addGateway('gatewayName', [
        'factory' => 'paytrail'
        'merchantId' => 'EDIT IT',
        'merchantSecret' => 'EDIT IT'
    ])

    ->getPayum()
;
```

## prepare.php

Here you have to modify a `gatewayName` value. Set it to `paytrail`. The rest remain the same as described basic [get it started](https://github.com/Payum/Payum/blob/master/src/Payum/Core/Resources/docs/get-it-started.md) documentation.

## Licence

MIT Licence. 