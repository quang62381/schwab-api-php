<?php

namespace MichaelDrennen\SchwabAPI;

use GuzzleHttp\Client;


/**
 * Customer Service Number for when I get locked out
 * 800-780-2755
 */
class SchwabAPI {


    use APIClientTrait;


    protected string $apiKey;


    protected string $apiSecret;


    protected string $apiCallbackUrlAuthenticate;

    protected string $apiCallbackUrlToken;

    /**
     * @var string Returned from https://api.schwabapi.com/v1/oauth/authorize?
     * @url https://developer.schwab.com/products/trader-api--individual/details/documentation/Retail%20Trader%20API%20Production
     */
    protected ?string $code = null;

    protected ?string $token = null;


    /**
     * @var string Also returned from https://api.schwabapi.com/v1/oauth/authorize?
     * @url https://developer.schwab.com/products/trader-api--individual/details/documentation/Retail%20Trader%20API%20Production
     */
    protected string $session;


    protected bool $debug = FALSE;


    /**
     * @var \GuzzleHttp\Client The Guzzle HTTP client, so I can make requests to the Schwab API.
     */
    protected Client $client;


    /**
     * @param string      $apiKey
     * @param string      $apiSecret
     * @param string      $apiCallbackUrlAuthenticate
     * @param string      $apiCallbackUrlToken
     * @param string|null $authenticationCode
     * @param string|null $token
     * @param bool        $debug
     */
    public function __construct( string $apiKey,
                                 string $apiSecret,
                                 string $apiCallbackUrlAuthenticate,
                                 string $apiCallbackUrlToken,
                                 string $authenticationCode = NULL,
                                 string $token = NULL,
                                 bool   $debug = FALSE ) {

        $this->apiKey                     = $apiKey;
        $this->apiSecret                  = $apiSecret;
        $this->apiCallbackUrlAuthenticate = $apiCallbackUrlAuthenticate;
        $this->apiCallbackUrlToken        = $apiCallbackUrlToken;
        $this->code                       = $authenticationCode;
        $this->token                      = $token;
        $this->debug                      = $debug;


        $this->client = $this->createGuzzleClient( $token, $this->debug );
    }

    public function getAuthorizeUrl(): string {
        return 'https://api.schwabapi.com/v1/oauth/authorize?client_id=' . $this->apiKey . '&redirect_uri=' . urlencode( $this->apiCallbackUrlAuthenticate );
    }


    /**
     * Setter for the 'code' returned from the Schwab authorization request
     *
     * @param string $code
     *
     * @return void
     */
    public function setCode( string $code ): void {
        $this->code = $code;
    }


    /**
     * Setter for the 'session' returned from the Schwab authorization request
     *
     * @param string $session
     *
     * @return void
     */
    public function setSession( string $session ): void {
        $this->session = $session;
    }


    /**
     * @url https://developer.schwab.com/products/trader-api--individual/details/documentation/Retail%20Trader%20API%20Production
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestAuthenticate() {
        $uri = 'https://api.schwabapi.com/v1/oauth/authorize?client_id=' . $this->apiKey . '&redirect_uri=' . urlencode( $this->apiCallbackUrlAuthenticate );

        var_dump( $uri );
        //die('asdf');
        try {
            $response = $this->client->get( $uri );
            print_r( $response );
        } catch ( \GuzzleHttp\Exception\ClientException $e ) {
            $response             = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            print_r( $responseBodyAsString );
        }
    }


    /**
     * curl -X POST https://api.schwabapi.com/v1/oauth/token \
     * -H 'Authorization: Basic {BASE64_ENCODED_Client_ID:Client_Secret} \
     * -H 'Content-Type: application/x-www-form-urlencoded' \
     * -d 'grant_type=authorization_code&code={AUTHORIZATION_CODE_VALUE}&redirect_uri=https://example_url.com/callback_example'
     *
     * @return void
     */
    public function requestToken(): string {
        try {

            $options = [
                'headers' => [
                    'Authorization' => "Basic " . base64_encode( $this->apiSecret ),
                    'Content-Type'  => 'application/x-www-form-urlencoded',
                ],
                //'body'    => 'grant_type=authorization_code&code=' . $this->code . '&redirect_uri=' . urlencode( $this->apiCallbackUrlToken ),
                'debug' => true,
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => urlencode($this->code),
                    'redirect_uri' => urlencode( $this->apiCallbackUrlToken )
                ]
            ];

            //dd($options);


            $response = $this->client->post( 'https://api.schwabapi.com/v1/oauth/token', $options  );
            print_r( $response );
        } catch ( \GuzzleHttp\Exception\ClientException $e ) {
            echo "\nThis is the error response from the guzzle client:\n";
            $response             = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            print_r( $responseBodyAsString );
        }
        return "test token";
    }


}