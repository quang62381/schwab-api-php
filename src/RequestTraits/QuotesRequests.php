<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

/**
 * @url https://developer.schwab.com/products/trader-api--individual/details/specifications/Market%20Data%20Production
 */
trait QuotesRequests {

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
        $suffix          = '/marketdata/v1/quotes';
        $queryParameters = [];
        if ( $symbols ):
            $queryParameters[ 'symbols' ] = implode( ',', $symbols );
        endif;

        if ( $fields ):
            $queryParameters[ 'fields' ] = implode( ',', $fields );
        endif;

        $queryParameters[ 'indicative' ] = $indicative;

        $suffix .= '?' . http_build_query( $queryParameters );

        $response = $this->_request( $suffix );
        return $this->json( $response );
    }


    /**
     * @param string $symbol Symbol of instrument. Ex: TSLA
     * @param array  $fields Request for subset of data by passing coma separated list of root nodes, possible root nodes are quote, fundamental, extended, reference, regular. Don't send this attribute for full response.
     *
     * @return array
     * ["TBLA" => array:7 [▼
     * "assetMainType" => "EQUITY"
     * "assetSubType" => "COE"
     * "quoteType" => "NBBO"
     * "realtime" => true
     * "ssid" => 73833092
     * "symbol" => "TBLA"
     * "quote" => array:28 [
     **** "52WeekHigh" => 5.0
     *** "52WeekLow" => 2.87
     *** "askMICId" => "ARCX"
     *** "askPrice" => 3.6
     *** "askSize" => 51
     *** "askTime" => 1732220677330
     *** "bidMICId" => "XNAS"
     *** "bidPrice" => 3.59
     *** "bidSize" => 24
     *** "bidTime" => 1732220675465
     *** "closePrice" => 3.3
     *** "highPrice" => 3.67
     *** "lastMICId" => "XADF"
     *** "lastPrice" => 3.59
     *** "lastSize" => 400
     *** "lowPrice" => 3.48
     *** "mark" => 3.59
     *** "markChange" => 0.29
     *** "markPercentChange" => 8.78787879
     *** "netChange" => 0.29
     *** "netPercentChange" => 8.78787879
     *** "openPrice" => 3.5
     *** "postMarketChange" => 0.0
     *** "postMarketPercentChange" => 0.0
     *** "quoteTime" => 1732220677330
     *** "securityStatus" => "Normal"
     *** "totalVolume" => 2373190
     *** "tradeTime" => 1732220641538
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function quotesBySymbol( string $symbol,
                                    array  $fields = [] ): array {
        $symbol          = strtoupper( $symbol );
        $suffix          = '/marketdata/v1/' . $symbol . '/quotes';
        $queryParameters = [];

        if ( $fields ):
            $queryParameters[ 'fields' ] = implode( ',', $fields );
        endif;

        $suffix .= '?' . http_build_query( $queryParameters );

        $response = $this->_request( $suffix );
        return $this->json( $response );
    }
}