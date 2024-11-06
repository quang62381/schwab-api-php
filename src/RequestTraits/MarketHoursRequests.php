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

        return $this->_request( $suffix );
    }


    /**
     * @param string              $marketId
     * @param \Carbon\Carbon|NULL $date
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function marketsById( string $marketId, Carbon $date = NULL ): array {
        $marketId = strtolower( $marketId );
        $suffix   = '/marketdata/v1/markets/' . $marketId;

        $queryParameters              = [];

        if ( $date ):
            $queryParameters[ 'date' ] = $date->toDateString();
        endif;

        $this->_throwExceptionIfInvalidParameters( [$marketId] );

        $suffix .= '?' . http_build_query( $queryParameters );

        return $this->_request( $suffix );
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