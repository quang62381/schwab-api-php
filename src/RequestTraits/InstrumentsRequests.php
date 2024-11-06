<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

use Carbon\Carbon;


trait InstrumentsRequests {

    use RequestTrait;

    const VALID_PROJECTIONS = [
        'symbol-search',
        'symbol-regex',
        'desc-search',
        'desc-regex',
        'search',
        'fundamental',
    ];


    /**
     * Get Instruments details by using different projections.
     * Get more specific fundamental instrument data by using fundamental as the projection.
     *
     * @param string $symbol symbol of a security
     * @param string $projection search by
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function instruments( string $symbol, string $projection ): array {
        $suffix = '/trader/v1/instruments';

        $projection = strtolower( $projection );

        $queryParameters                 = [];
        $queryParameters[ 'symbol' ]     = $symbol;
        $queryParameters[ 'projection' ] = $projection;

        if ( !in_array( $projection, self::VALID_PROJECTIONS ) ):
            throw new \Exception( "Invalid projection. Select from " . implode( ', ', self::VALID_PROJECTIONS ) );
        endif;

        $suffix .= '?' . http_build_query( $queryParameters );
        return $this->_request( $suffix );
    }


    /**
     * Get basic instrument details by cusip
     * @param string $cusip
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function instrumentByCusip( string $cusip ): array {
        $cusip = strtoupper( $cusip );
        $suffix = '/trader/v1/instruments/' . $cusip;
        return $this->_request( $suffix );
    }



}