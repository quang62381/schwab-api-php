<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

use Carbon\Carbon;
use MichaelDrennen\SchwabAPI\Schemas\CreatePayloadEquity;


trait OrderRequests {

    use RequestTrait;

    const AWAITING_PARENT_ORDER   = 'AWAITING_PARENT_ORDER';
    const AWAITING_CONDITION      = 'AWAITING_CONDITION';
    const AWAITING_STOP_CONDITION = 'AWAITING_STOP_CONDITION';
    const AWAITING_MANUAL_REVIEW  = 'AWAITING_MANUAL_REVIEW';
    const ACCEPTED                = 'ACCEPTED';
    const AWAITING_UR_OUT         = 'AWAITING_UR_OUT';
    const PENDING_ACTIVATION      = 'PENDING_ACTIVATION';
    const QUEUED                  = 'QUEUED';
    const WORKING                 = 'WORKING';
    const REJECTED                = 'REJECTED';
    const PENDING_CANCEL          = 'PENDING_CANCEL';
    const CANCELED                = 'CANCELED';
    const PENDING_REPLACE         = 'PENDING_REPLACE';
    const REPLACED                = 'REPLACED';
    const FILLED                  = 'FILLED';
    const EXPIRED                 = 'EXPIRED';
    const NEW                     = 'NEW';
    const AWAITING_RELEASE_TIME   = 'AWAITING_RELEASE_TIME';
    const PENDING_ACKNOWLEDGEMENT = 'PENDING_ACKNOWLEDGEMENT';
    const PENDING_RECALL          = 'PENDING_RECALL';
    const UNKNOWN                 = 'UNKNOWN';


    const VALID_STATUSES = [
        'AWAITING_PARENT_ORDER',
        'AWAITING_CONDITION',
        'AWAITING_STOP_CONDITION',
        'AWAITING_MANUAL_REVIEW',
        'ACCEPTED',
        'AWAITING_UR_OUT',
        'PENDING_ACTIVATION',
        'QUEUED',
        'WORKING',
        'REJECTED',
        'PENDING_CANCEL',
        'CANCELED',
        'PENDING_REPLACE',
        'REPLACED',
        'FILLED',
        'EXPIRED',
        'NEW',
        'AWAITING_RELEASE_TIME',
        'PENDING_ACKNOWLEDGEMENT',
        'PENDING_RECALL',
        'UNKNOWN',
    ];


    /**
     * Get all ORDERS from all ACCOUNTS
     *
     * @param \Carbon\Carbon|NULL $fromTime
     * @param \Carbon\Carbon|NULL $toTime
     * @param int|NULL            $maxResults
     * @param string|NULL         $status
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function orders( Carbon $fromTime = NULL,
                            Carbon $toTime = NULL,
                            int    $maxResults = NULL,
                            string $status = NULL ): array {
        $suffix = '/trader/v1/orders';

        // This Exception is just to help Developers keep from getting confused or wasting time.
        if ( $fromTime xor $toTime ):
            throw new \Exception( "If you set fromTime, you are required to set toTime as well." );
        endif;

        $queryParameters = [];

        if ( $fromTime && $toTime ):
            $queryParameters[ 'fromEnteredTime' ] = $fromTime->toIso8601String();
            $queryParameters[ 'toEnteredTime' ]   = $toTime->toIso8601String();
        endif;

        if ( $status ):
            $status = strtoupper( $status );
            if ( !in_array( $status, self::VALID_STATUSES ) ):
                throw new \Exception( "Invalid status. Your input of '" . $status . "' is not in the array of validStatuses." );
            endif;

            $queryParameters[ 'status' ] = $status;
        endif;

        if ( $maxResults ):
            $queryParameters[ 'maxResults' ] = $maxResults;
        endif;

        if ( $queryParameters ):
            $suffix .= '?' . http_build_query( $queryParameters );
        endif;

        return $this->_request( $suffix );
    }


    /**
     * All orders for a specific account.
     * Orders retrieved can be filtered based on input parameters below.
     * Maximum date range is 1 year.
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
    public function ordersForAccount( string $hashValueOfAccountNumber,
                                      int    $maxResults = NULL,
                                      Carbon $fromTime = NULL,
                                      Carbon $toTime = NULL,
                                      string $status = NULL ): array {
        $suffix = '/trader/v1/accounts/' . $hashValueOfAccountNumber . '/orders';

        // This Exception is just to help Developers keep from getting confused or wasting time.
        if ( $fromTime xor $toTime ):
            throw new \Exception( "If you set fromTime, you are required to set toTime as well." );
        endif;

        $queryParameters = [];

        if ( $maxResults ):
            $queryParameters[ 'maxResults' ] = $maxResults;
        endif;

        if ( $fromTime && $toTime ):
            $queryParameters[ 'fromEnteredTime' ] = $fromTime->toIso8601String();
            $queryParameters[ 'toEnteredTime' ]   = $toTime->toIso8601String();
        endif;

        if ( $status ):
            $status = strtoupper( $status );
            if ( !in_array( $status, self::VALID_STATUSES ) ):
                throw new \Exception( "Invalid status. Your input of '" . $status . "' is not in the array of validStatuses." );
            endif;

            $queryParameters[ 'status' ] = $status;
        endif;

        if ( $queryParameters ):
            $suffix .= '?' . http_build_query( $queryParameters );
        endif;

        return $this->_request( $suffix );
    }


    /**
     * Get a specific order by its ID, for a specific account
     *
     * @param string $hashValueOfAccountNumber
     * @param int    $orderId
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function orderForAccount( string $hashValueOfAccountNumber,
                                     int    $orderId ): array {
        $suffix = '/trader/v1/accounts/' . $hashValueOfAccountNumber . '/orders/' . $orderId;
        return $this->_request( $suffix );
    }


    /**
     * @param string $hashValueOfAccountNumber
     * @param string $symbol
     * @param int    $quantity
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function placeBuyOrder( string $hashValueOfAccountNumber, string $symbol, int $quantity ): array {
        $payload = CreatePayloadEquity::createBuy( $symbol, $quantity );
        $suffix  = '/trader/v1/accounts/' . $hashValueOfAccountNumber . '/orders';

        return $this->_request( $suffix,
                                'POST',
                                [ 'body' => $payload ] );
    }


    /**
     * @param string $hashValueOfAccountNumber
     * @param string $symbol
     * @param int    $quantity
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function placeSellOrder( string $hashValueOfAccountNumber, string $symbol, int $quantity ): array {
        $payload = CreatePayloadEquity::createSell( $symbol, $quantity );
        $suffix  = '/trader/v1/accounts/' . $hashValueOfAccountNumber . '/orders';

        return $this->_request( $suffix,
                                'POST',
                                [ 'body' => $payload ] );
    }

}