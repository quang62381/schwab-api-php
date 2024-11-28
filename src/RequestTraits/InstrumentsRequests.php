<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

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
     * @param string $symbol     symbol of a security
     * @param string $projection Search by. Available values : symbol-search, symbol-regex, desc-search, desc-regex, search, fundamental
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function instruments( string $symbol, string $projection ): array {
        $suffix = '/marketdata/v1/instruments';

        $projection = strtolower( $projection );

        $queryParameters                 = [];
        $queryParameters[ 'symbol' ]     = $symbol;
        $queryParameters[ 'projection' ] = $projection;

        if ( !in_array( $projection, self::VALID_PROJECTIONS ) ):
            throw new \Exception( "Invalid projection. Select from " . implode( ', ', self::VALID_PROJECTIONS ) );
        endif;

        $suffix   .= '?' . http_build_query( $queryParameters );
        $response = $this->_request( $suffix );
        return $this->json( $response );
    }


    /**
     * Get basic instrument details by cusip
     *
     * @param string $cusip
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function instrumentByCusip( string $cusip ): array {
        $cusip    = strtoupper( $cusip );
        $suffix   = '/marketdata/v1/instruments/' . $cusip;
        $response = $this->_request( $suffix );
        return $this->json( $response );
    }


    /**
     * @param string $ticker
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCusipFromTicker( string $ticker ): string {
        $instrument = $this->instruments( $ticker, 'symbol-search' );
        if ( empty( $instrument ) ):
            throw new \Exception( "Unable to find the CUSIP for ticker " . $ticker );
        endif;

        return $instrument[ 'instruments' ][ 0 ][ 'cusip' ];
    }


    public function getInstrumentFromTicker( string $ticker ): array {
        $cusip = $this->getCusipFromTicker( $ticker );
        $instrument = $this->instrumentByCusip($cusip);
        dd( $instrument );
    }

}