<?php

namespace MichaelDrennen\SchwabAPI;

use GuzzleHttp\Client;

class SchwabAPI {


    use APIClientTrait;


    private string $apiKey;
    private string $apiSecret;
    private string $apiCallbackUrl;

    protected bool $debug = false;

    /**
     * @var \GuzzleHttp\Client The Guzzle HTTP client so I can make requests to the Schwab API.
     */
    protected Client $client;

    public function __construct( string $apiKey, string $apiSecret, string $apiCallbackUrl, bool $debug = FALSE ) {

        $this->apiKey         = $apiKey;
        $this->apiSecret      = $apiSecret;
        $this->apiCallbackUrl = $apiCallbackUrl;
        $this->debug          = $debug;


        $this->client = $this->createGuzzleClient();
    }


    /**
     * @url https://developer.schwab.com/products/trader-api--individual/details/documentation/Retail%20Trader%20API%20Production
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function authenticate() {
        $uri      = 'https://api.schwabapi.com/v1/oauth/authorize?client_id=' . $this->apiKey . '&redirect_uri=' . $this->apiCallbackUrl;

        try {
            $response = $this->client->get( $uri );
            print_r( $response );
        }
        catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            print_r( $responseBodyAsString );
        }



    }


}