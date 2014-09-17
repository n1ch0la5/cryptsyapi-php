CryptsyAPI PHP
===========

PHP wrapper for [Cryptsy.com](https://www.cryptsy.com/) for use with the [Cryptsy.com API](https://www.cryptsy.com/pages/api). Simple abstraction layer on top of existing API interfaces, and automatic JSON decoding on response.

Pull requests accepted and encouraged. :)

### Usage

First, sign up for an account at [Cryptsy.com](https://www.cryptsy.com/) and request an API key under Account > Settings

Download and include the crypstyapi.php class:

~~~
require_once 'path/to/cryptsyapi.php';
~~~

Or preferably install via [Composer](https://getcomposer.org/)

~~~
"cryptsyapi-php/cryptsyapi-php": "dev-master"
~~~

Instantiate the class and set your API key and API Secret.

~~~
$apiKeys = array('api_key' => 'API_KEY_HERE', 'api_secret' => 'API_SECRET_HERE');

$cryptsy = new CryptsyAPI($apiKeys);

$info = $cryptsy->get_info();
~~~
More usage examples in example.php

The wrapper abstracts most methods listed at https://www.cryptsy.com/pages/api using the same interface names. For example, to get your current open orders:

~~~
$orders =  $cryptsy->get_all_my_orders();
echo $orders;
~~~

To make requests that require parameters (eg. creating a buy or sell order or grabbing orders by market), pass through each parameter in an associative array. For example, the request below will create a buy order using the necessary parameters:

~~~
$create_order = $crypsty->create_order( array('marketid' => 5, 'ordertype' => 'Buy', 'quantity' => 500, 'price' => 0.0000123) );
~~~

**Note:** Error checking has not been fully implemented, please enforce your own checks on top of the wrapper.