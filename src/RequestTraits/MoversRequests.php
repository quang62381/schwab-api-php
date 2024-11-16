<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

use Carbon\Carbon;


trait MoversRequests {

    use RequestTrait;

    const VALID_SYMBOLS = [
        '$DJI',
        '$COMPX',
        '$SPX',
        'NYSE',
        'NASDAQ',
        'OTCBB',
        'INDEX_ALL',
        'EQUITY_ALL',
        'OPTION_ALL',
        'OPTION_PUT',
        'OPTION_CALL',
    ];

    const VALID_SORTS = [
        'VOLUME',
        'TRADES',
        'PERCENT_CHANGE_UP',
        'PERCENT_CHANGE_DOWN',
    ];

    const VALID_FREQUENCIES = [ 0, 1, 5, 10, 30, 60 ];



    public function movers( string $symbolId, string $sort = NULL, int $frequency = 0 ): array {
        $suffix = '/marketdata/v1/movers/' . $symbolId;

        $queryParameters                = [];
        $queryParameters[ 'symbol_id' ] = strtoupper( $symbolId );

        if ( $sort ):
            $queryParameters[ 'sort' ] = strtoupper( $sort );
        endif;

        if ( $frequency ):
            $queryParameters[ 'frequency' ] = $frequency;
        endif;

        if ( !in_array( $symbolId, self::VALID_SYMBOLS ) ):
            throw new \Exception( "You entered a symbol of '$symbolId' but the only valid values are " . implode( ', ', self::VALID_SYMBOLS ) );
        endif;

        if ( !in_array( $sort, self::VALID_SORTS ) ):
            throw new \Exception( "You entered a sort of '$sort' but the only valid values are " . implode( ', ', self::VALID_SORTS ) );
        endif;

        if ( !in_array( $frequency, self::VALID_FREQUENCIES ) ):
            throw new \Exception( "You entered a frequency of '$frequency' but the only valid values are " . implode( ', ', self::VALID_FREQUENCIES ) );
        endif;

        $suffix .= '?' . http_build_query( $queryParameters );

        $response = $this->_request( $suffix );
        return $this->json( $response );
    }
}