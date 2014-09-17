<?php

/**
 * CryptsyAPI Wrapper
 *
 * Requirements: cURL
 *
 * @author Nicholas Johnson <the90sarealive@aol.com>
 *
 * Modeled after the DogeAPI Wrapper by Jackson Palmer
 */

class CryptsyAPI
{
    private $keys = array();

    public function __construct( $keys = array() )
    {
        $this->api_key = $keys['api_key'];
        $this->secret = $keys['api_secret'];
    }

    /**
     * cURL GET request driver
     */
    private function _request($method, $args = array())
    {
        // Create a special nonce POST parameter with incrementing integer for the api
        $mt = explode( ' ', microtime() );
        $args['nonce'] = $mt[1];

        // Turn method variable into an array
        if( ! is_array( $method ) )
        {
            $method = array($method);
        }

        $count = count( $method );
        $curl_array = array();
        static $ch = null;
        $ch = curl_multi_init();

        foreach( $method as $count => $val )
        {
            $args['method'] = $val;

            // Check for args and build query string
            if ( !empty($args) )
            {
                $post_data = http_build_query($args, '', '&');
            }

            $sign = hash_hmac("sha512", $post_data, $this->secret);

            $headers = array(
            'Sign: '.$sign,
            'Key: '.$this->api_key
            );

            // Initiate cURL and set headers/options
            $curl_array[$count] = curl_init();
            curl_setopt($curl_array[$count], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_array[$count], CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; Cryptsy API PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
            curl_setopt($curl_array[$count], CURLOPT_URL, 'https://api.cryptsy.com/api');
            curl_setopt($curl_array[$count], CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($curl_array[$count], CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl_array[$count], CURLOPT_SSL_VERIFYPEER, FALSE);

            curl_multi_add_handle( $ch, $curl_array[ $count ] );
        }

        // Execute the cURL request
        $running = NULL;
        do {
            usleep(10000);
            curl_multi_exec( $ch,$running );
        } while($running > 0);

        // Get the content
        foreach( $method as $count => $val )
        {
            $res[$count] = curl_multi_getcontent( $curl_array[$count] );
             curl_multi_remove_handle( $ch, $curl_array[$count] );
        }
        curl_multi_close($ch);

        if ($res === false) throw new Exception('Could not get reply: '.curl_error($curl_array[$count]));
        foreach($method as $count => $val)
        {
           $result[$count] = json_decode($res[$count], true);
        }
        if (!$result) throw new Exception('Invalid data received, please make sure connection is working and requested API exists');

        return $result;
    }

    /**
     * Public methods (CryptsyAPI abstraction layer)
     */

    // Grab multiple methods below in one swoop $cryptsyapi->get_all( array( 'getinfo', 'getmarkets', 'allmytrades', 'allmyorders' ) );
    public function get_all( $array = array( 'getinfo', 'getmarkets', 'allmytrades', 'allmyorders' ) )
    {
        return $this->_request($array);
    }

    // Return currencies and balances available and currencies on hold for open orders
    public function get_info()
    {
        return $this->_request('getinfo');
    }

    // Array of all active markets
    public function get_markets()
    {
        return $this->_request('getmarkets');
    }

    //last 1000 trades for a market.
    // Input: marketid i.e. get_market_trades( array( 'marketid' => 5 ));
    public function get_market_trades($args = array())
    {
        return $this->_request('markettrades', $args);
    }

    // Returns a Buy Array and a Sell array for a market.
    // Input: marketid i.e. get_market_orders( array( 'marketid' => 5 ));
    public function get_market_orders($args = array())
    {
        return $this->_request('marketorders', $args);
    }

    // (Your) User trades by market.
    // Inputs: marketid, limit (optional, default 200)
    public function get_my_trades( $args = array() )
    {
        return $this->_request('mytrades', $args);
    }

    // Returns all (your) user trades.
    public function get_all_my_trades()
    {
        return $this->_request('allmytrades');
    }

    //User orders by market listing your current open sell and buy orders
    //Input: marketid
    public function get_my_orders( $args = array() )
    {
        return $this->_request('myorders', $args);
    }

    // Array of all open orders for your account.
    public function get_all_my_orders()
    {
        return $this->_request('allmyorders');
    }

    //Create Order
    //Inputs:
    //marketid    Market ID for which you are creating an order for
    //ordertype   Order type you are creating (Buy/Sell)
    //quantity    Amount of units you are buying/selling in this order
    //price   Price per unit you are buying/selling at
    //Outputs: orderid
    public function create_order( $args = array() )
    {
        return $this->_request('createorder', $args);
    }

    //Cancel an order.
    //Input: orderid
    public function cancel_order( $args = array() )
    {
        return $this->_request('cancelorder', $args);
    }

    //Cancel all open orders for an entire market.
    //Input: marketid
    public function cancel_market_orders( $args = array() )
    {
        return $this->_request('cancelmarketorders', $args);
    }

    // Cancels all open orders
    public function cancel_all_orders()
    {
        return $this->_request('cancelallorders');
    }

    //Calculate Fees for an order
    //Inputs:
    //ordertype   Order type you are calculating for (Buy/Sell)
    //quantity    Amount of units you are buying/selling
    //price   Price per unit you are buying/selling at
    public function calculate_fees( $args = array() )
    {
        return $this->_request('calculatefees', $args);
    }

    // Generate a new address for a currency
    // Inputs: (either currencyid OR currencycode required - you do not have to supply both)
    // Ouputs: address
    public function generate_address( $args = array() )
    {
        return $this->_request('generatenewaddress', $args);
    }
}
