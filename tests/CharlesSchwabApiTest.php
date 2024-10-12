<?php

namespace MichaelDrennen\SchwabAPI\Tests;


use MichaelDrennen\SchwabAPI\SchwabAPI;
use PHPUnit\Framework\TestCase;

class CharlesSchwabApiTest extends TestCase {


    /**
     * @test
     */
    public function testConstructorShouldCreateApiObject() {
        $apiKey         = $_ENV[ 'SCHWAB_API_KEY' ];
        $apiSecret      = $_ENV[ 'SCHWAB_API_SECRET' ];
        $apiCallbackUrl = $_ENV[ 'SCHWAB_CALLBACK_URL' ];
        $debug          = FALSE;
        $api            = new SchwabAPI( $apiKey, $apiSecret, $apiCallbackUrl, $debug );





        $api->authenticate();
        $this->assertInstanceOf( SchwabAPI::class, $api );
    }
}