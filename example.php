<?php
require_once 'lib/cryptsyapi.php';

$apiKeys = array('api_key' => 'API_KEY_HERE', 'api_secret' => 'API_SECRET_HERE');

$cryptsy = new CryptsyAPI($apiKeys);

// Return getinfo, getmarkets, allmytrades, allmyorders in a multidimensional array
$all = $cryptsy->get_all();
// Optionally specify which methods you want to return
$all = $cryptsy->get_all(array('getinfo', 'getmarkets'));
$info = $cryptsy->get_info();
$market_trades = $crypsty->get_market_trades( array( 'marketid' => 5 ));
$create_order = $crypsty->create_order( array('marketid' => 5, 'ordertype' => 'Buy', 'quantity' => 500, 'price' => 0.0000123) );

print_r($all);
print_r($info);
print_r($market_trades);

// For Codeigniter Usage
// Include cryptsyapi.php in the application/libraries folder
$params = array('api_key' => 'API_KEY_HERE', 'api_secret' => 'API_SECRET_HERE');
$this->load->library('cryptsyapi', $params); 
$info = $this->cryptsyapi->get_info();

print_r($info);
?>