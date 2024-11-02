<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

use Carbon\Carbon;


trait OrderRequests {

    use RequestTrait;

    /**
     * ORDERS
     */

    /**
     * All orders for a specific account. Orders retrieved can be filtered based on input parameters below. Maximum date range is 1 year.
     *
     * @param string              $hashValueOfAccountNumber
     * @param int|NULL            $maxResults The max number of orders to retrieve. Default is 3000.
     * @param \Carbon\Carbon|NULL $fromTime   Specifies that no orders entered before this time should be returned.
     * @param \Carbon\Carbon|NULL $toTime     Specifies that no orders entered after this time should be returned.
     * @param string|NULL         $status     Specifies that only orders of this status should be returned. Valid values can be found in the method body below.
     *
     * @return array
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function orders( string $hashValueOfAccountNumber,
                            int    $maxResults = NULL,
                            Carbon $fromTime = NULL,
                            Carbon $toTime = NULL,
                            string $status = NULL ): array {
        $suffix = '/accounts/' . $hashValueOfAccountNumber . '/orders';

        $validStatuses = [
            'AWAITING_PARENT_ORDER', 'AWAITING_CONDITION', 'AWAITING_STOP_CONDITION', 'AWAITING_MANUAL_REVIEW', 'ACCEPTED', 'AWAITING_UR_OUT', 'PENDING_ACTIVATION', 'QUEUED', 'WORKING', 'REJECTED', 'PENDING_CANCEL', 'CANCELED', 'PENDING_REPLACE', 'REPLACED', 'FILLED', 'EXPIRED', 'NEW', 'AWAITING_RELEASE_TIME', 'PENDING_ACKNOWLEDGEMENT', 'PENDING_RECALL', 'UNKNOWN',
        ];

        // This Exception is just to help Developers keep from getting confused or wasting time.
        if ( $fromTime xor $toTime ):
            throw new \Exception( "If you set fromTime, you are required to set toTime as well." );
        endif;

        $queryParameters = [];

        if ( $maxResults ):
            $queryParameters[ 'maxResults' ] = $maxResults;
        endif;

        if ( $fromTime && $toTime ):
            $queryParameters[ 'fromTime' ] = $fromTime->toIso8601String();
            $queryParameters[ 'toTime' ]   = $fromTime->toIso8601String();
        endif;

        if ( $status ):
            $status = strtoupper( $status );
            if ( !in_array( $status, $validStatuses ) ):
                throw new \Exception( "Invalid status. Your input of '" . $status . "' is not in the array of validStatuses." );
            endif;

            $queryParameters[ 'status' ] = $status;
        endif;

        if ( $queryParameters ):
            $suffix .= '?' . http_build_query( $queryParameters );
        endif;

        return $this->_request( $suffix );
    }

}