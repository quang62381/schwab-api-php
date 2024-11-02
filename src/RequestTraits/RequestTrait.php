<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

use GuzzleHttp\Client;


trait RequestTrait {


    const BASE_URL = 'https://api.schwabapi.com/trader/v1';


    /**
     * @var \GuzzleHttp\Client The Guzzle HTTP client, so I can make requests to the Schwab API.
     */
    protected Client $client;


    protected ?string $accessToken = NULL;


    protected bool $debug = FALSE;


    /**
     * @return \GuzzleHttp\Client
     */
    protected function _createGuzzleClient(): Client {
        $headers             = [];
        $headers[ 'Accept' ] = 'application/json';

        // The $token param will not be sent on the first API call which should exchange the request code for a token.
        if ( $this->accessToken ):
            $headers[ 'Authorization' ] = 'Bearer ' . $this->accessToken;
        endif;

        $options = [
            'base_uri' => self::BASE_URL,
            'headers'  => $headers,
            'debug'    => $this->debug,
        ];
        return new Client( $options );
    }


    /**
     * @param string $urlSuffix
     * @param string $method
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function _request( string $urlSuffix, string $method = 'GET' ): array {
        $method = strtoupper( $method );

        $url     = self::BASE_URL . $urlSuffix;
        $options = [
            'headers' => [
                'Authorization' => "Bearer " . $this->accessToken,
            ],
            'debug'   => $this->debug,
        ];

        if ( 'POST' == $method ):
            $response = $this->client->post( $url, $options );
        else:
            $response = $this->client->get( $url, $options );
        endif;

        $json = $response->getBody()->getContents();
        $data = json_decode( $json, TRUE );

        return $data;
    }

}