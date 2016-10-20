## Magento2 Debugbar

[![Total Downloads](https://poser.pugx.org/yong/magento2debugbar/d/total.svg)](https://packagist.org/packages/yong/magento2debugbar)
[![Latest Stable Version](https://poser.pugx.org/yong/magento2debugbar/v/stable.svg)](https://packagist.org/packages/yong/magento2debugbar)
[![Latest Unstable Version](https://poser.pugx.org/yong/magento2debugbar/v/unstable.svg)](https://packagist.org/packages/yong/magento2debugbar)
[![License](https://poser.pugx.org/yong/magento2debugbar/license.svg)](https://packagist.org/packages/yong/magento2debugbar)

This is a package to integrate [PHP Debug Bar](http://phpdebugbar.com/) with Magento 2.

It lightly dynamic inject and collect debug info for Magento2 for development/production mode. 
You can configure enable it by specific IP address or specific cookie name and value matched, and you can extend it by your custom access control functions.

It bootstraps some Collectors to work with Magento2 and implements a couple custom DataCollectors, specific for Magento2.
It is configured to display Redirects and (jQuery) Ajax Requests. (Shown in a dropdown)
Read [the documentation](http://phpdebugbar.com/docs/) for more configuration options.


This package includes some custom collectors:
 - QueryCollector: Show queries, including binding + timing
 - ControllerCollector: Show information about the current/redirect Route Action.
 - TemplateCollector: Show the currently loaded template files.
 - ModelCollector: Show the loaded Models
 - ProfileCollector: Shows the Magento2 profiler details
 - RequestCollector: The default RequestCollector via PHPDebugbar
 - MemoryCollector: The default MemoryCollector via PHPDebugbar
 - MessagesCollector: The default MessagesCollector via PHPDebugbar

And it also replace Magento default exception handler as Whoops Error Handler.Read [filp/whoops](https://github.com/filp/whoops) for more details.


It also provides config interface for easy dynamic extend your functionality.

## Installation

Require this package with composer:

```shell
composer require yongchengchen/magento2debugbar
```

After updating composer, add 'phpdebugbar' configuration to app/etc/env.php
```php
  'phpdebugbar' =>
  array (
    'enabled' => 1,
    'enable_checker' =>
    array (
      'cookie' =>
      array(
        'name' => 'php_debugbar',
        'value' => 'cookievalue'
      ),
    ),
  )
```

and then run 
```shell
bin/magento module:enable Yong_Magento2DebugBar
```

## Usage
Enable/Disable: go to file app/etc/env.php, set 'enabled' of array phpdebugbar as 0 for disable, 1 for enable(but still need cookie pair check)

enable_checker: When phpdebugbar is enabled, it will do further check, you need to set your cookie pair in app/etc/env.php, and also your browser has the same cookie pair. Then phpdebugbar will be launched. Otherwise it will not be launched and will collect nothing, it will not affect the performance. So you can deploy it on your production environment.

## License

And of course:

MIT: http://rem.mit-license.org
