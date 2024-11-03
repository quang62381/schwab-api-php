<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

use Carbon\Carbon;


/**
 * @url https://developer.schwab.com/products/trader-api--individual/details/specifications/Market%20Data%20Production
 */
trait QuoteRequests {

    use RequestTrait;


    /**
     * @param array $symbols    Comma separated list of symbol(s) to look up a quote
     * @param array $fields     Request for subset of data by passing coma separated list of root nodes, possible root nodes are quote, fundamental, extended, reference, regular. Don't send this attribute for full response.
     * @param bool  $indicative Include indicative symbol quotes for all ETF symbols in request. If ETF symbol ABC is in request and indicative=true API will return quotes for ABC and its corresponding indicative quote for $ABC.IV
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function quotes( array $symbols = [],
                            array $fields = [],
                            bool  $indicative = FALSE ): array {
        $suffix          = '/quotes';
        $queryParameters = [];
        if ( $symbols ):
            $queryParameters[ 'symbols' ] = implode( ',', $symbols );
        endif;

        if ( $fields ):
            $queryParameters[ 'fields' ] = implode( ',', $fields );
        endif;

        $queryParameters[ 'indicative' ] = $indicative;

        $suffix .= '?' . http_build_query( $queryParameters );

        return $this->_request( $suffix );
    }


    /**
     * @param string $symbol Symbol of instrument. Ex: TSLA
     * @param array  $fields Request for subset of data by passing coma separated list of root nodes, possible root nodes are quote, fundamental, extended, reference, regular. Don't send this attribute for full response.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function quotesBySymbol( string $symbol,
                                    array  $fields = [] ): array {
        $symbol          = strtoupper( $symbol );
        $suffix          = $symbol . '/quotes';
        $queryParameters = [];

        if ( $fields ):
            $queryParameters[ 'fields' ] = implode( ',', $fields );
        endif;

        $suffix .= '?' . http_build_query( $queryParameters );

        return $this->_request( $suffix );
    }


}