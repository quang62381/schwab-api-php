<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;


trait AccountRequests {

    use RequestTrait;


    /**
     * @return array
     * print_r() of $data
     * Array ( [0] => Array ( [accountNumber] => 27834695236 [hashValue] => 397465203847059238764059762304 ) [1] => Array ( [accountNumber] => 08347502745 [hashValue] => A34502983740527304857203947580A535A67529CC ) )
     * The 'hashValue' is the 'encrypted' accountNumber that is used in all other REQUESTS to the Schwab API system.
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function accountNumbers(): array {
        $suffix   = '/trader/v1/accounts/accountNumbers';
        $response = $this->_request( $suffix );
        return $this->json( $response );
    }


    /**
     * All the linked account information for the user logged in.
     * The balances on these accounts are displayed by default, however
     * the positions on these accounts will be displayed based on the "positions" flag.
     *
     * @param bool $positions Setting to TRUE adds a query parameter to the endpoint that tells Schwab to return POSITION data as well.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function accounts( bool $positions = FALSE ): array {
        $suffix = '/trader/v1/accounts';
        if ( $positions ):
            $suffix .= '?fields=positions';
        endif;
        $response = $this->_request( $suffix );
        return $this->json( $response );
    }


    /**
     * @param string $hashValueOfAccountNumber This hash value is returned by the '/accounts/accountNumbers' endpoint. Ex: E49D5746FD010E582E364C28E9D6A763D972C3A0C0C90170878260D0A6C65453
     * @param array  $fields                   In the Schwab docs, the only 'field' mentioned is 'positions' like in the accounts() method above. However, they elude to there being more.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function account( string $hashValueOfAccountNumber, array $fields = [] ): array {
        $suffix = '/trader/v1/accounts/' . $hashValueOfAccountNumber;

        if ( $fields ):
            $suffix .= '?fields=' . implode( ',', $fields );
            //$suffix .= '?' . http_build_query( $fields );
        endif;

        $response = $this->_request( $suffix );
        return $this->json( $response );
    }


    public function getLongEquityPositions( string $hashValueOfAccountNumber ): array {
        $positions = [];
        $account   = $this->account( $hashValueOfAccountNumber, [ 'positions' ] );

        if ( !isset ( $account[ 'securitiesAccount' ][ 'positions' ] ) ) :
            return [];
        endif;


        /**
         * @var array $position
         */

        // array:15 [
        //    "shortQuantity" => 0.0
        //    "averagePrice" => 0.305
        //    "currentDayProfitLoss" => 0.0
        //    "currentDayProfitLossPercentage" => 0.0
        //    "longQuantity" => 2.0
        //    "settledLongQuantity" => 2.0
        //    "settledShortQuantity" => 0.0
        //    "instrument" => array:4
        //      "assetType" => "EQUITY"
        //      "cusip" => "205750300"
        //      "symbol" => "LODE"
        //      "netChange" => -0.0025
        //    ]
        //    "marketValue" => 0.7
        //    "maintenanceRequirement" => 0.7
        //    "averageLongPrice" => 0.3055
        //    "taxLotAverageLongPrice" => 0.305
        //    "longOpenProfitLoss" => 0.085
        //    "previousSessionLongQuantity" => 2.0
        //    "currentDayCost" => 0.0
        //  ]
        foreach ( $account[ 'securitiesAccount' ][ 'positions' ] as $position ) :
            if ( $position[ 'longQuantity' ] <= 0 ):
                continue;
            endif;

            if ( 'EQUITY' != $position[ 'instrument' ][ 'assetType' ] ):
                continue;
            endif;

            // For this method, I am only looking at simple EQUITY securities.
            // Where there will only be ONE instrument associated with this position.
            // So to make it simple for me, I flatten the instrumend array, so
            // I don't end up looking for a ticker in a nested array in my other code.
            $position[ 'assetType' ] = $position[ 'instrument' ][ 'assetType' ];
            $position[ 'cusip' ]     = $position[ 'instrument' ][ 'cusip' ];
            $position[ 'symbol' ]    = $position[ 'instrument' ][ 'symbol' ];
            $position[ 'netChange' ] = $position[ 'instrument' ][ 'netChange' ];
            unset( $position[ 'instrument' ] );
            $positions[] = $position;
        endforeach;

        return $positions;
    }
}