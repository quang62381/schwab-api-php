<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

use Carbon\Carbon;


trait MarketHoursRequests {

    use RequestTrait;

    const MARKETS = [
        'equity',
        'option',
        'bond',
        'future',
        'forex',
    ];


    /**
     * @param array               $markets
     * @param \Carbon\Carbon|NULL $date
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function markets( array $markets = self::MARKETS, Carbon $date = NULL ): array {
        $suffix = '/marketdata/v1/markets';

        $markets = array_map( 'strtolower', $markets );

        $queryParameters              = [];
        $queryParameters[ 'markets' ] = $markets;

        if ( $date ):
            $queryParameters[ 'date' ] = $date->toDateString();
        endif;

        $this->_throwExceptionIfInvalidParameters( $markets );

        $suffix .= '?' . http_build_query( $queryParameters );

        $response = $this->_request( $suffix );
        return $this->json( $response );
    }


    /**
     * @param string              $marketId Ex: equity
     * @param \Carbon\Carbon|NULL $date
     *
     * @return array
     * "equity" => array:1 [▼
     *      "equity" => array:4 [▼
     *      "date" => "2024-11-16"
     *      "marketType" => "EQUITY"
     *      "product" => "equity"
     *      "isOpen" => false
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function marketsById( string $marketId, Carbon $date = NULL ): array {
        $marketId = strtolower( $marketId );
        $suffix   = '/marketdata/v1/markets/' . $marketId;

        $queryParameters = [];

        if ( $date ):
            $queryParameters[ 'date' ] = $date->toDateString();
        endif;

        $this->_throwExceptionIfInvalidParameters( [ $marketId ] );

        $suffix .= '?' . http_build_query( $queryParameters );

        $response = $this->_request( $suffix );
        return $this->json( $response );
    }


    public function getNextOpenDateForMarket( string $marketId, string $timezone = 'America/New_York' ): Carbon {

        $maxAttempts = 10;
        $attempt     = 0;
        $date        = Carbon::today( $timezone );
        $isOpen      = FALSE;
        while ( FALSE == $isOpen ):
            $attempt++;
            /**
             * Array (
             * [equity] => Array (
             * [equity] => Array (
             * [date] => 2024-11-10
             * [marketType] => EQUITY
             * [product] => equity
             * [isOpen] =>
             * )
             * )
             * )
             */
            $marketData = $this->marketsById( $marketId, $date );

            $isOpen = $marketData[ $marketId ][ $marketId ][ 'isOpen' ];

            if ( $attempt >= $maxAttempts ):
                throw new \Exception( "Check your code. You should have found a date where the market was open." );
            endif;

            $date = $date->copy()->addDay();
        endwhile;

        return $date;
    }


    /**
     * @param array $markets
     *
     * @return void
     * @throws \Exception
     */
    protected function _throwExceptionIfInvalidParameters( array $markets ): void {
        foreach ( $markets as $market ) :
            if ( !in_array( $market, self::MARKETS ) ) :
                throw new \Exception( "Make sure the markets you are querying for are valid values." );
            endif;
        endforeach;
    }
}