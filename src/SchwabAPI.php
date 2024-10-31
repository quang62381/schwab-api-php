<?php

namespace MichaelDrennen\SchwabAPI;

use GuzzleHttp\Client;


/**
 * Customer Service Number for when I get locked out
 * Doesn't work
 * 800-780-2755
 */
class SchwabAPI {


    use APIClientTrait;

    /**
     * @var \GuzzleHttp\Client The Guzzle HTTP client, so I can make requests to the Schwab API.
     */
    protected Client $client;


    protected string $apiKey;


    protected string $apiSecret;


    /**
     * @var string This is set within the Schwab Developer web UI.
     */
    protected string $apiCallbackUrl;


    /**
     * @var string Returned from https://api.schwabapi.com/v1/oauth/authorize?
     * @url https://developer.schwab.com/products/trader-api--individual/details/documentation/Retail%20Trader%20API%20Production
     */
    protected ?string $code = NULL;

    protected ?string $token = NULL;


    /**
     * @var string Also returned from https://api.schwabapi.com/v1/oauth/authorize?
     * @url https://developer.schwab.com/products/trader-api--individual/details/documentation/Retail%20Trader%20API%20Production
     */
    protected string $session;


    // START PROPERTIES THAT ARE SET FROM $this->requestToken()

    /**
     * @var int Ex: "expires_in" => 1800
     */
    protected int $expiresIn;

    /**
     * @var string Ex: "token_type" => "Bearer"
     */
    protected string $tokenType;

    /**
     * @var string Ex: "scope" => "api"
     */
    protected string $scope;

    /**
     * @var string Ex: "refresh_token" => big-long-string_that_ends-in-a-@"
     */
    protected string $refreshToken;

    /**
     * @var string Ex: "access_token" => "another-big-long-string_that_ends-in-a-@"
     */
    protected string $accessToken;

    /**
     * @var string Ex: "id_token" => "a-super-long-string"
     */
    protected string $idToken;

    // END PROPERTIES THAT ARE SET FROM $this->requestToken()


    protected bool $debug = FALSE;


    /**
     * @param string      $apiKey
     * @param string      $apiSecret
     * @param string      $apiCallbackUrl
     * @param string|null $authenticationCode
     * @param string|null $token
     * @param bool        $debug
     */
    public function __construct( string $apiKey,
                                 string $apiSecret,
                                 string $apiCallbackUrl,
                                 string $authenticationCode = NULL,
                                 string $token = NULL,
                                 bool   $debug = FALSE ) {

        $this->apiKey         = $apiKey;
        $this->apiSecret      = $apiSecret;
        $this->apiCallbackUrl = $apiCallbackUrl;
        $this->code           = $authenticationCode;
        $this->token          = $token;
        $this->debug          = $debug;


        $this->client = $this->createGuzzleClient( $token, $this->debug );
    }

    /**
     * The first step in authorizing your app to access your Schwab trading account, is to direct
     * your user to the URL returned by this method.
     *
     * @return string
     */
    public function getAuthorizeUrl(): string {
        return 'https://api.schwabapi.com/v1/oauth/authorize?client_id=' . $this->apiKey . '&redirect_uri=' . $this->apiCallbackUrl;
    }


    /**
     * Setter for the 'code' returned from the Schwab authorization request
     * You set the callback URL for your APP.
     * After you redirect your USER to the getAutorizeUrl(), and your USER goes
     * through the CAG process on the Schwab website...
     * The user will get directed to your defined CALLBACK URL.
     * On that callback URL you should receive some QUERY parameters.
     * It will look like this:
     * https://your-domain.com/callback?code=AbdfdgrifjmgjkYy5jyskadgfOIYUSGDF394dfsghiluhfsldsf8QT_MBs%40&session=akjsydgf-asdf-asdf-asdf-astuydgfakugiysdf
     * So you will use this method to set the query parameter 'code'
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
     * https://your-domain.com/callback?code=AbdfdgrifjmgjkYy5jyskadgfOIYUSGDF394dfsghiluhfsldsf8QT_MBs%40&session=akjsydgf-asdf-asdf-asdf-astuydgfakugiysdf
     * Same notes as setCode(), except sets the value from the query parameter 'session'
     *
     * @param string $session
     *
     * @return void
     */
    public function setSession( string $session ): void {
        $this->session = $session;
    }


    /**
     * This method will get called from the CALLBACK URL of yours, after the
     * Schwab site directs back to your CALLBACK URL after authorization.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestToken(): void {
        $options = [
            'headers'     => [
                'Authorization' => "Basic " . base64_encode( $this->apiKey . ':' . $this->apiSecret ),
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            'debug'       => FALSE,
            'form_params' => [
                'grant_type'   => 'authorization_code',
                'code'         => $this->code,
                'redirect_uri' => $this->apiCallbackUrl,
            ],
        ];

        try {
            $response = $this->client->post( 'https://api.schwabapi.com/v1/oauth/token', $options );
            $json     = $response->getBody()->getContents();


            /**
             * @var array $data A php array of the data sent back from the ...token URL we just posted to.
             *
             * [
             *      "expires_in" => 1800
             *      "token_type" => "Bearer"
             *      "scope" => "api"
             *      "refresh_token" => big-long-string_that_ends-in-a-@"
             *      "access_token" => "another-big-long-string_that_ends-in-a-@"
             *      "id_token" => "a-super-long-string"
             * ]
             */
            $data = json_decode( $json, TRUE );

            // Set all the protected properties that we gather from the POST in this method.
            $this->expiresIn    = $data[ 'expires_in' ];
            $this->tokenType    = $data[ 'token_type' ];
            $this->scope        = $data[ 'scope' ];
            $this->refreshToken = $data[ 'refresh_token' ];
            $this->accessToken  = $data[ 'access_token' ];
            $this->idToken      = $data[ 'id_token' ];

            return;
        } catch ( \Exception $exception ) {
            $response             = $exception->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $jsonResponse         = json_decode( $responseBodyAsString );

            $errorDescription = $jsonResponse->error_description;

            $pattern               = '/{/';
            $errorDescriptionParts = preg_split( $pattern, $errorDescription );
            $json                  = '{' . $errorDescriptionParts[ 1 ];
            $json                  = trim( $json );
            $json                  = trim( $json, '"' );


            $jsonError = json_decode( $json, TRUE );
            dump( $jsonResponse );
            dump( $jsonError );
            return;
        }
    }









    /**
     * @url https://developer.schwab.com/products/trader-api--individual/details/documentation/Retail%20Trader%20API%20Production
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    //public function requestAuthenticate() {
    //    $uri = $this->getAuthorizeUrl();
    //
    //    var_dump( $uri );
    //    die( 'asdf' );
    //    try {
    //        $response = $this->client->get( $uri );
    //        print_r( $response );
    //    } catch ( \GuzzleHttp\Exception\ClientException $e ) {
    //        $response             = $e->getResponse();
    //        $responseBodyAsString = $response->getBody()->getContents();
    //        print_r( $responseBodyAsString );
    //    }
    //}


    /**
     * curl -X POST https://api.schwabapi.com/v1/oauth/token \
     * -H 'Authorization: Basic {BASE64_ENCODED_Client_ID:Client_Secret} \
     * -H 'Content-Type: application/x-www-form-urlencoded' \
     * -d 'grant_type=authorization_code&code={AUTHORIZATION_CODE_VALUE}&redirect_uri=https://example_url.com/callback_example'
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    //public function requestToken(): string {
    //    try {
    //
    //        $options = [
    //            'headers'     => [
    //                'Authorization' => "Basic " . base64_encode( $this->apiSecret ),
    //                'Content-Type'  => 'application/x-www-form-urlencoded',
    //            ],
    //            'debug'       => TRUE,
    //            'form_params' => [
    //                'grant_type'   => 'authorization_code',
    //                'code'         => urlencode( $this->code ),
    //                'redirect_uri' => urlencode( $this->apiCallbackUrl ),
    //            ],
    //        ];
    //
    //        //dd($options);
    //
    //
    //        $response = $this->client->post( 'https://api.schwabapi.com/v1/oauth/token', $options );
    //        print_r( $response );
    //    } catch ( \GuzzleHttp\Exception\ClientException $e ) {
    //        echo "\nThis is the error response from the guzzle client:\n";
    //        $response             = $e->getResponse();
    //        $responseBodyAsString = $response->getBody()->getContents();
    //        print_r( $responseBodyAsString );
    //    }
    //    return "test token";
    //}


}