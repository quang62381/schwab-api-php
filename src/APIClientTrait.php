<?php

namespace MichaelDrennen\SchwabAPI;


use GuzzleHttp\Client;

trait APIClientTrait {

    const BASE_URI = 'https://api.schwabapi.com/trader/v1/';

    /**
     * @var Client
     */
    protected Client $guzzle;


    /**
     * @param string|NULL $token
     * @param bool $debug
     * @return Client
     */
    protected function createGuzzleClient( string $token = NULL, bool $debug = FALSE ): Client {
        $headers             = [];
        $headers[ 'Accept' ] = 'application/json';

        // The $token param will not be sent on the first API call which should exchange the request code for a token.
        if ( $token ):
            $headers[ 'Authorization' ] = 'Bearer ' . $token;
        endif;

        $options = [
            'base_uri' => self::BASE_URI,
            'headers'  => $headers,
            'debug'    => $debug,
        ];
        return new Client( $options );
    }
}