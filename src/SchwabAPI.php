<?php

namespace MichaelDrennen\SchwabAPI;

use Carbon\Carbon;
use GuzzleHttp\Client;
use MichaelDrennen\SchwabAPI\Exceptions\RequestException;


class SchwabAPI {


    use APIClientTrait;

    /**
     * @var \GuzzleHttp\Client The Guzzle HTTP client, so I can make requests to the Schwab API.
     */
    protected Client $client;


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
     * @var string Returned from https://api.schwabapi.com/v1/oauth/authorize?
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
     * @var string Ex: "refresh_token" => big-long-string_that_ends-in-a-@"
     */
    protected ?string $refreshToken = NULL;

    /**
     * @var string Ex: "access_token" => "another-big-long-string_that_ends-in-a-@"
     */
    protected ?string $accessToken = NULL;

    /**
     * @var string Ex: "id_token" => "a-super-long-string"
     */
    protected string $idToken;

    // END PROPERTIES THAT ARE SET FROM $this->requestToken()


    protected bool $debug = FALSE;


    const BASE_URL = 'https://api.schwabapi.com/trader/v1';

    /**
     * @param string      $apiKey
     * @param string      $apiSecret
     * @param string      $apiCallbackUrl
     * @param string|NULL $authenticationCode
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


        $this->client = $this->createGuzzleClient( $token, $this->debug );
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


    /**
     * REQUESTS
     */


    /**
     * ACCOUNTS
     */

    /**
     * print_r() of $data
     * Array ( [0] => Array ( [accountNumber] => 27834695236 [hashValue] => 397465203847059238764059762304 ) [1] => Array ( [accountNumber] => 08347502745 [hashValue] => A34502983740527304857203947580A535A67529CC ) )
     * The 'hashValue' is the 'encrypted' accountNumber that is used in all other REQUESTS to the Schwab API system.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function accountNumbers(): array {
        $suffix = '/accounts/accountNumbers';
        return $this->_request( $suffix );
    }


    /**
     * All the linked account information for the user logged in.
     * The balances on these accounts are displayed by default, however
     * the positions on these accounts will be displayed based on the "positions" flag.
     *
     * @param bool $positions Setting to TRUE adds a query parameter to the endpoint that tells Schwab to return POSITION data as well.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function accounts( bool $positions = FALSE ): array {
        $suffix = '/accounts';
        if ( $positions ):
            $suffix .= '?fields=positions';
        endif;
        return $this->_request( $suffix );
    }

    /**
     * @param string $hashValueOfAccountNumber This hash value is returned by the '/accounts/accountNumbers' endpoint. Ex: E49D5746FD010E582E364C28E9D6A763D972C3A0C0C90170878260D0A6C65453
     * @param array  $fields                   In the Schwab docs, the only 'field' mentioned is 'positions' like in the accounts() method above. However, they elude to there being more.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function account( string $hashValueOfAccountNumber, array $fields = [] ): array {
        $suffix = '/accounts/' . $hashValueOfAccountNumber;

        if ( $fields ):
            $suffix .= '?' . http_build_query( $fields );
        endif;

        return $this->_request( $suffix );
    }


    /**
     * ORDERS
     */

    /**
     * All orders for a specific account. Orders retrieved can be filtered based on input parameters below. Maximum date range is 1 year.
     *
     * @param string              $hashValueOfAccountNumber
     * @param int|NULL            $maxResults The max number of orders to retrieve. Default is 3000.
     * @param \Carbon\Carbon|NULL $fromTime   Specifies that no orders entered before this time should be returned.
     * @param \Carbon\Carbon|NULL $toTime     Specifies that no orders entered after this time should be returned.
     * @param string|NULL         $status     Specifies that only orders of this status should be returned. Valid values can be found in the method body below.
     *
     * @return array
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function orders( string $hashValueOfAccountNumber,
                            int    $maxResults = NULL,
                            Carbon $fromTime = NULL,
                            Carbon $toTime = NULL,
                            string $status = NULL ): array {
        $suffix = '/accounts/' . $hashValueOfAccountNumber . '/orders';

        $validStatuses = [
            'AWAITING_PARENT_ORDER', 'AWAITING_CONDITION', 'AWAITING_STOP_CONDITION', 'AWAITING_MANUAL_REVIEW', 'ACCEPTED', 'AWAITING_UR_OUT', 'PENDING_ACTIVATION', 'QUEUED', 'WORKING', 'REJECTED', 'PENDING_CANCEL', 'CANCELED', 'PENDING_REPLACE', 'REPLACED', 'FILLED', 'EXPIRED', 'NEW', 'AWAITING_RELEASE_TIME', 'PENDING_ACKNOWLEDGEMENT', 'PENDING_RECALL', 'UNKNOWN',
        ];

        // This Exception is just to help Developers keep from getting confused or wasting time.
        if ( $fromTime xor $toTime ):
            throw new \Exception( "If you set fromTime, you are required to set toTime as well." );
        endif;

        $queryParameters = [];

        if ( $maxResults ):
            $queryParameters[ 'maxResults' ] = $maxResults;
        endif;

        if ( $fromTime && $toTime ):
            $queryParameters[ 'fromTime' ] = $fromTime->toIso8601String();
            $queryParameters[ 'toTime' ]   = $fromTime->toIso8601String();
        endif;

        if ( $status ):
            $status = strtoupper( $status );
            if ( !in_array( $status, $validStatuses ) ):
                throw new \Exception( "Invalid status. Your input of '" . $status . "' is not in the array of validStatuses." );
            endif;

            $queryParameters[ 'status' ] = $status;
        endif;

        if ( $queryParameters ):
            $suffix .= '?' . http_build_query( $queryParameters );
        endif;

        return $this->_request( $suffix );
    }

}