<?php

namespace MichaelDrennen\SchwabAPI\Schemas;


class CreatePayloadEquity {


    /**
     * @param string $symbol
     * @param int    $quantity
     *
     * @return string
     */
    public static function createBuy( string $symbol, int $quantity ): string {
        $payload = [
            'orderType'          => 'MARKET',
            'session'            => 'NORMAL',
            'duration'           => 'DAY',
            'orderStrategyType'  => 'SINGLE',
            'orderLegCollection' => [
                'instruction' => 'BUY',
                'quantity'    => $quantity,
                'instrument'  => [
                    'symbol'    => $symbol,
                    'assetType' => 'EQUITY',
                ],
            ],
        ];

        return json_encode( $payload );
    }


    /**
     * @param string $symbol
     * @param int    $quantity
     *
     * @return string
     */
    public static function createSell( string $symbol, int $quantity ): string {
        $payload = [
            'orderType'          => 'MARKET',
            'session'            => 'NORMAL',
            'duration'           => 'DAY',
            'orderStrategyType'  => 'SINGLE',
            'orderLegCollection' => [
                'instruction' => 'SELL',
                'quantity'    => $quantity,
                'instrument'  => [
                    'symbol'    => $symbol,
                    'assetType' => 'EQUITY',
                ],
            ],
        ];

        return json_encode( $payload );
    }


}