<?php

namespace MichaelDrennen\SchwabAPI;


use GuzzleHttp\Client;

trait APIClientTrait {

    protected string $baseUri = 'https://api.schwabapi.com/trader/v1/';

    /**
     * @var Client
     */
    protected Client $guzzle;


    /**
     * @return \GuzzleHttp\Client
     */
    protected function createGuzzleClient(): Client {
        $headers             = [];
        $headers[ 'Accept' ] = 'application/json';

        // The $token param will not be sent on the first API call which should exchange the request code for a token.
        if ( $this->accessToken ):
            $headers[ 'Authorization' ] = 'Bearer ' . $this->accessToken;
        endif;

        $options = [
            'base_uri' => $this->baseUri,
            'headers'  => $headers,
            'debug'    => $this->debug,
        ];
        return new Client( $options );
    }
}