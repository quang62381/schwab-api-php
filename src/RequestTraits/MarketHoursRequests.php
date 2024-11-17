<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

use App\Console\Kernel;
use Carbon\Carbon;


trait MarketHoursRequests {

    use RequestTrait;

    // This is the 2nd tier array index when the market is OPEN
    const EQ = 'EQ';         // equity -> EQ

    // marketId values: Ex: equity
    const equity = 'equity'; // EQ
    const option = 'option'; // EQO, IND
    const bond   = 'bond';   // BON
    const future = 'future'; // EHF,HO,MHG,QC,YM,MHO,QG,QH,QI,QM,QO,MYM,NKD,ZB,SMC,ZC,ZF,J7,BTC,...
    const forex  = 'forex';  // forex... This must have been closed on the testing date.

    const MARKETS = [
        self::equity,
        self::option,
        self::bond,
        self::future,
        self::forex,
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
        $queryParameters[ 'markets' ] = implode( ',', $markets );

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


    /**
     * The reason I have a subMarketId here...
     *
     * @param string      $marketId    equity
     * @param string|NULL $subMarketId EQ
     * @param string      $timezone
     *
     * @return \Carbon\Carbon
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getNextOpenDateForMarket( string $marketId,
                                              string $subMarketId = NULL,
                                              string $timezone = 'America/New_York' ): Carbon {

        $maxAttempts = 10;
        $attempt     = 0;
        $date        = Carbon::today( $timezone );
        $isOpen      = FALSE;
        while ( FALSE == $isOpen ):

            if ( $attempt >= $maxAttempts ):
                throw new \Exception( "Check your code. You should have found a date where the market was open." );
            endif;

            /**
             * This is what gets returned from marketsById, if the market is CLOSED.
             * Array (
             *      [equity] => Array (
             *          [equity] => Array (
             *          [date] => 2024-11-10
             *          [marketType] => EQUITY
             *          [product] => equity
             *          [isOpen] =>
             */
            $marketData = $this->marketsById( $marketId, $date );


            /**
             * This means the market is closed.
             * Ex:
             * $marketData['equity']['equity'] => [array of data telling you its closed basically]
             * So continue and check the NEXT day
             */
            if ( isset( $marketData[ $marketId ][ $marketId ] ) ):
                $date = $date->copy()->addDay();
                $attempt++;
                continue;


            /**
             * Else, the developer fat-fingerd the SPECIFIC market they want to get the next open-day for.
             * // So throw an exception, and let the developer read the notes to see what they should actually ask for.
             */
            elseif ( !isset( $marketData[ $marketId ][ $subMarketId ] ) ):
                throw new \Exception( "You were looking for " . $marketId . ':' . $subMarketId . " and that doesn't exist." );
            endif;

            $isOpen = (bool)$marketData[ $marketId ][ $subMarketId ][ 'isOpen' ];
        endwhile;

        return Carbon::parse( $date, $timezone );
    }


    /**
     * @param string $marketId Ex: equity
     * @param string $subMarketId Ex: EQ
     * @param string $timezone Ex: America/New_York
     *
     * @return array Ex:
     * Array(
     *      [preMarket] => Array(
     *          [0] => Array (
     *              [start] => 2024-11-18T07:00:00-05:00
     *              [end] => 2024-11-18T09:30:00-05:00
     *      [regularMarket] => Array(
     *          [0] => Array(
     *              [start] => 2024-11-18T09:30:00-05:00
     *              [end] => 2024-11-18T16:00:00-05:00
     *      [postMarket] => Array(
     *          [0] => Array(
     *              [start] => 2024-11-18T16:00:00-05:00
     *              [end] => 2024-11-18T20:00:00-05:00
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getNextSessionTimes( string $marketId, string $subMarketId, string $timezone = 'America/New_York' ): array {
        $carbonDate = $this->getNextOpenDateForMarket( $marketId, $subMarketId, $timezone );
        $marketData = $this->marketsById( $marketId, $carbonDate );
        return $marketData[ $marketId ][ $subMarketId ][ 'sessionHours' ];
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