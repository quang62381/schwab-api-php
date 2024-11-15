<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

use GuzzleHttp\Client;


trait RequestTrait {

    const BASE_URL = "https://api.schwabapi.com";

    /**
     * @var \GuzzleHttp\Client The Guzzle HTTP client, so I can make requests to the Schwab API.
     */
    protected Client $client;


    /**
     * @param string|NULL $accessToken
     * @param bool        $debug
     *
     * @return \GuzzleHttp\Client
     */
    protected function createGuzzleClient( string $accessToken = NULL, bool $debug = FALSE ): Client {
        $headers             = [];
        $headers[ 'Accept' ] = 'application/json';

        // The $token param will not be sent on the first API call which should exchange the request code for a token.
        if ( $accessToken ):
            $headers[ 'Authorization' ] = 'Bearer ' . $accessToken;
        endif;

        $options = [
            'base_uri' => self::BASE_URL,
            'headers'  => $headers,
            'debug'    => $debug,
        ];
        return new Client( $options );
    }


    /**
     * @param string $urlSuffix
     * @param string $method
     * @param array  $additionalOptions
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function _request( string $urlSuffix,
                                 string $method = 'GET',
                                 array  $additionalOptions = [],
                                 array  $additionalHeaders = [] ): array {
        $method = strtoupper( $method );

        $url = self::BASE_URL . $urlSuffix;

        $options = [
            'headers' => [
                'Authorization' => "Bearer " . $this->accessToken,
            ],
            'debug'   => $this->debug,
        ];

        foreach ( $additionalHeaders as $header => $value ):
            $options[ 'headers' ][ $header ] = $value;
        endforeach;

        $options = array_merge( $options, $additionalOptions );

        if ( 'POST' == $method ):
            $options[ 'headers' ][ 'Content-Type' ] = 'application/json';
            $response                               = $this->client->post( $url, $options );
        else:
            $response = $this->client->get( $url, $options );
        endif;

        $json = $response->getBody()->getContents();

        $code = $response->getStatusCode();

        $data = json_decode( $json, TRUE );

        return [
            'code' => $code,
            'data' => $data,
        ];
    }

}