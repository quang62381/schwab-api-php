<?php

namespace MichaelDrennen\SchwabAPI\Tests;


use GuzzleHttp\Client;
use MichaelDrennen\SchwabAPI\SchwabAPI;
use PHPUnit\Framework\TestCase;


if ( !function_exists( 'dd' ) ) {
    function dd() {
        echo '<pre>';
        array_map( function ( $x ) {
            var_dump( $x );
        }, func_get_args() );
        die;
    }
}

class CharlesSchwabApiTest extends TestCase {

    protected string $code;
    protected string $session;

    protected SchwabAPI $api;

    protected function setUp(): void {

        $this->code     = $_ENV[ 'CODE' ];
        $this->session  = $_ENV[ 'SESSION' ];
        $apiKey         = $_ENV[ 'SCHWAB_API_KEY' ];
        $apiSecret      = $_ENV[ 'SCHWAB_API_SECRET' ];
        $apiCallbackUri = $_ENV[ 'SCHWAB_CALLBACK_URI' ];


        $this->api = new SchwabAPI( $apiKey,
                                    $apiSecret,
                                    $apiCallbackUri,
                                    $this->code,
                                    NULL,
                                    true );

    }

    /**
     * @test
     */
    public function testConstructorShouldCreateApiObject() {

        $this->assertInstanceOf( SchwabAPI::class, $this->api );
    }

    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetTokenShouldGetToken() {
        $token = $this->api->requestToken();
    }
}