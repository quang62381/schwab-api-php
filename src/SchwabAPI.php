<?php

namespace MichaelDrennen\SchwabAPI;


use MichaelDrennen\SchwabAPI\Exceptions\RequestException;
use MichaelDrennen\SchwabAPI\RequestTraits\AccountRequests;
use MichaelDrennen\SchwabAPI\RequestTraits\OrderRequests;
use MichaelDrennen\SchwabAPI\RequestTraits\RequestTrait;


class SchwabAPI {


    use RequestTrait;
    use AccountRequests;
    use OrderRequests;


    /**
     * @var string
     */
    protected string $apiKey;


    /**
     * @var string
     */
    protected string $apiSecret;


    /**
     * @var string This is set within the Schwab Developer web UI.
     */
    protected string $apiCallbackUrl;


    /**
     * @var string|NULL Returned from https://api.schwabapi.com/v1/oauth/authorize?
     * @url https://developer.schwab.com/products/trader-api--individual/details/documentation/Retail%20Trader%20API%20Production
     */
    protected ?string $code = NULL;


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
     * You need to use this about every 30 minutes to keep your access token active.
     *
     * @var string|NULL Ex: "refresh_token" => big-long-string_that_ends-in-a-@"
     */
    protected ?string $refreshToken = NULL;


    /**
     * Given to the Guzzle Client to be added to the header: "Bearer $accessToken"
     *
     * @var string|NULL Ex: "access_token" => "another-big-long-string_that_ends-in-a-@"
     */
    protected ?string $accessToken = NULL;


    /**
     * @var string Ex: "id_token" => "a-super-long-string"
     */
    protected string $idToken;
    // END PROPERTIES THAT ARE SET FROM $this->requestToken()


    /**
     * @param string      $apiKey
     * @param string      $apiSecret
     * @param string      $apiCallbackUrl
     * @param string|NULL $authenticationCode
     * @param string|null $bearerToken
     * @param string|NULL $accessToken
     * @param string|NULL $refreshToken
     * @param bool        $debug
     */
    public function __construct( string $apiKey,
                                 string $apiSecret,
                                 string $apiCallbackUrl,
                                 string $authenticationCode = NULL,
                                 string $accessToken = NULL,
                                 string $refreshToken = NULL,
                                 bool   $debug = FALSE ) {

        $this->apiKey         = $apiKey;
        $this->apiSecret      = $apiSecret;
        $this->apiCallbackUrl = $apiCallbackUrl;
        $this->code           = $authenticationCode;
        $this->accessToken    = $accessToken;
        $this->refreshToken   = $refreshToken;
        $this->debug          = $debug;

        $this->client = $this->createGuzzleClient($this->accessToken, $this->debug);
    }


    /**
     * This method will get called from the CALLBACK URL of yours, after the
     * Schwab site directs back to your CALLBACK URL after authorization.
     *
     *  curl -X POST https://api.schwabapi.com/v1/oauth/token \
     *  -H 'Authorization: Basic {BASE64_ENCODED_Client_ID:Client_Secret} \
     *  -H 'Content-Type: application/x-www-form-urlencoded' \
     *  -d 'grant_type=authorization_code&code={AUTHORIZATION_CODE_VALUE}&redirect_uri=https://example_url.com/callback_example'
     *
     * @param bool $doRefreshToken
     *
     * @return void
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \MichaelDrennen\SchwabAPI\Exceptions\RequestException
     */
    public function requestToken( bool $doRefreshToken = FALSE ): void {
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


        if ( $doRefreshToken ):
            if ( !$this->refreshToken ):
                throw new \Exception( "You are asking to refresh the access token, but you don't have a refresh token." );
            endif;
            $options[ 'form_params' ][ 'grant_type' ]    = 'refresh_token';
            $options[ 'form_params' ][ 'refresh_token' ] = $this->refreshToken;
        endif;

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

            throw new RequestException( "Schwab API returned an error.", 0, NULL, $responseBodyAsString );
        }
    }


    /**
     * A simple getter for the accessToken.
     *
     * @return string
     */
    public function getAccessToken(): string {
        return $this->accessToken;
    }


    /**
     * A simple getter for the refreshToken.
     *
     * @return string
     */
    public function getRefreshToken(): string {
        return $this->refreshToken;
    }


    /**
     * The first step in authorizing your app to access your Schwab trading account, is to direct
     * your user to the URL returned by this method.
     *
     * @url https://developer.schwab.com/products/trader-api--individual/details/documentation/Retail%20Trader%20API%20Production
     *
     * @return string
     */
    public function getAuthorizeUrl(): string {
        return 'https://api.schwabapi.com/v1/oauth/authorize?client_id=' . $this->apiKey . '&redirect_uri=' . $this->apiCallbackUrl;
    }


    /**
     * @return int The number of seconds until the access token expires.
     */
    public function getExpiresIn(): int {
        return $this->expiresIn;
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

}