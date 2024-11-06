<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

use Carbon\Carbon;


trait OptionChainsRequests {

    use RequestTrait;

    const CONTRACT_TYPES = [ 'CALL', 'PUT', 'ALL' ];

    const STRATEGIES = [
        'SINGLE',
        'ANALYTICAL',
        'COVERED',
        'VERTICAL',
        'CALENDAR',
        'STRANGLE',
        'STRADDLE',
        'BUTTERFLY',
        'CONDOR',
        'DIAGONAL',
        'COLLAR',
        'ROLL',
    ];

    const RANGES = [ 'ITM', 'NTM', 'OTM' ];

    const MONTHS = [ 'ALL', 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC' ];

    const ENTITLEMENTS = [ 'NP', 'PN', 'PP' ];


    /**
     * 
     * Does not work yet.
     * @param string              $symbol
     * @param string              $contractType
     * @param int|NULL            $strikeCount
     * @param bool                $includeUnderlyingQuote
     * @param string              $strategy
     * @param float|NULL          $interval
     * @param float|NULL          $strike
     * @param string|NULL         $range
     * @param \Carbon\Carbon|NULL $fromDate
     * @param \Carbon\Carbon|NULL $toDate
     * @param float|NULL          $volatility
     * @param float|NULL          $underlyingPrice
     * @param float|NULL          $interestRate
     * @param int|NULL            $daysToExpiration
     * @param string|NULL         $expMonth
     * @param string|NULL         $optionType
     * @param string|NULL         $entitlement
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function chains( string $symbol,
                            string $contractType = self::CONTRACT_TYPES[ 'ALL' ],
                            int    $strikeCount = NULL,
                            bool   $includeUnderlyingQuote = NULL,
                            string $strategy = self::STRATEGIES[ 'SINGLE' ],
                            float  $interval = NULL,
                            float  $strike = NULL,
                            string $range = NULL,
                            Carbon $fromDate = NULL,
                            Carbon $toDate = NULL,
                            float  $volatility = NULL,
                            float  $underlyingPrice = NULL,
                            float  $interestRate = NULL,
                            int    $daysToExpiration = NULL,
                            string $expMonth = NULL,
                            string $optionType = NULL,
                            string $entitlement = NULL ): array {
        $suffix                                      = '/marketdata/v1/chains';
        $queryParameters                             = [];
        $queryParameters[ 'symbol' ]                 = $symbol;
        $queryParameters[ 'contractType' ]           = $contractType;
        $queryParameters[ 'includeUnderlyingQuote' ] = $includeUnderlyingQuote;
        $queryParameters[ 'strategy' ]               = $strategy;


        if ( $includeUnderlyingQuote ):
            $queryParameters[ 'includeUnderlyingQuote' ] = (string)$includeUnderlyingQuote;
        endif;
        
        
        if ( $interval ):
            $queryParameters[ 'interval' ] = $interval;
        endif;

        if ( $strike ):
            $queryParameters[ 'strike' ] = $strike;
        endif;

        if ( $range ):
            $queryParameters[ 'range' ] = $range;
        endif;

        if ( $fromDate ):
            $queryParameters[ 'fromDate' ] = $fromDate->toDateString();
        endif;

        if ( $toDate ):
            $queryParameters[ 'toDate' ] = $toDate->toDateString();
        endif;

        if ( $volatility ):
            $queryParameters[ 'volatility' ] = $volatility;
        endif;

        if ( $underlyingPrice ):
            $queryParameters[ 'underlyingPrice' ] = $underlyingPrice;
        endif;

        if ( $interestRate ):
            $queryParameters[ 'interestRate' ] = $interestRate;
        endif;

        if ( $daysToExpiration ):
            $queryParameters[ 'daysToExpiration' ] = $daysToExpiration;
        endif;

        if ( $expMonth ):
            $queryParameters[ 'expMonth' ] = $expMonth;
        endif;

        if ( $optionType ):
            $queryParameters[ 'optionType' ] = $optionType;
        endif;

        if ( $entitlement ):
            $queryParameters[ 'entitlement' ] = $entitlement;
        endif;


        if ( $contractType && !in_array( $contractType, self::CONTRACT_TYPES ) ):
            throw new \Exception( "Invalid contract type '{$contractType}'." );
        endif;

        if ( $strategy && !in_array( $strategy, self::STRATEGIES ) ):
            throw new \Exception( "Invalid strategy type '{$strategy}'." );
        endif;

        if ( $range && !in_array( $range, self::RANGES ) ):
            throw new \Exception( "Invalid range type '{$range}'." );
        endif;

        if ( $expMonth && !in_array( $expMonth, self::MONTHS ) ):
            throw new \Exception( "Invalid expMonth type '{$expMonth}'." );
        endif;

        if ( $entitlement && !in_array( $entitlement, self::ENTITLEMENTS ) ):
            throw new \Exception( "Invalid entitlement type '{$entitlement}'." );
        endif;

        if ( $queryParameters ):
            $suffix .= '?' . http_build_query( $queryParameters );
        endif;

        return $this->_request( $suffix );
    }


}