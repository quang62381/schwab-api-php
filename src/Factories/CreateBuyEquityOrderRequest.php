<?php

namespace MichaelDrennen\SchwabAPI\Factories;


use MichaelDrennen\SchwabAPI\Schemas\Instrument;
use MichaelDrennen\SchwabAPI\Schemas\OrderRequest;

class CreateBuyEquityOrderRequest {


    public function __construct() {

    }


    public static function create( string $symbol, float $quantity, Instrument $instrument ): OrderRequest {


        return new OrderRequest( 'NORMAL',
                                 'DAY',
                                 'MARKET',
                                 NULL,
                                 NULL,
                                 $quantity,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL,
                                 NULL
        );
    }


}