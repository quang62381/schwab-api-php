<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;


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
     * @return \GuzzleHttp\Psr7\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function _request( string $urlSuffix,
                                 string $method = 'GET',
                                 array  $additionalOptions = [],
                                 array  $additionalHeaders = [] ): \GuzzleHttp\Psr7\Response {
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
            return $this->client->post( $url, $options );
        else:
            return $this->client->get( $url, $options );
        endif;
    }


    /**
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return array
     */
    public function json(Response $response): array {
        $responseContents = $response->getBody()->getContents();
        return json_decode( $responseContents, true );
    }


    /**
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return int
     */
    public function responseCode(Response $response): int {
        return $response->getStatusCode();
    }

}