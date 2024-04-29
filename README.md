[![Current version](https://img.shields.io/packagist/v/maatify/huawei-hms-core)][pkg]
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/maatify/huawei-hms-core)][pkg]
[![Monthly Downloads](https://img.shields.io/packagist/dm/maatify/huawei-hms-core)][pkg-stats]
[![Total Downloads](https://img.shields.io/packagist/dt/maatify/huawei-hms-core)][pkg-stats]
[![Stars](https://img.shields.io/packagist/stars/maatify/huawei-hms-core)](https://github.com/maatify/huawei-hms-core/stargazers)

[pkg]: <https://packagist.org/packages/maatify/huawei-hms-core>
[pkg-stats]: <https://packagist.org/packages/maatify/huawei-hms-core/stats>

# PostValidatorJsonCode

maatify.dev Admin Portal Handler, known by our team


# Installation

```shell
composer require maatify/huawei-hms-core
```
    
## Important
```php
<?php

use Maatify\HMS\HMSCoreRequest;

/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2024-04-29
 * Time: 11:19 AM
 * https://www.Maatify.dev
 */
class HMSConnector extends HMSCoreRequest
{
    protected string $APP_ID_FROM_CONSOLE = __APP_ID_FROM_CONSOLE__;

    protected string|int $client_id = __client_id__;

    protected string $client_credentials = __client_credentials__;

    public function CallHMSByToken(string $title, string $message, string $token)
    {
        return $this
            ->SetTitle($title)
            ->SetMessage($message)
            ->SetTokens([$token])
            ->Load();
    }

}
