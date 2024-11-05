<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

use Carbon\Carbon;


trait PriceHistoryRequests {

    use RequestTrait;



    /**
     * Valid period types are the keys. Valid periods are the array values.
     */
    const PERIOD_TYPES = [
        'day'   => [ 1, 2, 3, 4, 5, 10 ],
        'month' => [ 1, 2, 3, 6 ],
        'year'  => [ 1, 2, 3, 5, 10, 15, 20 ],
        'ytd'   => [ 1 ],
    ];

    const DEFAULT_PERIODS = [
        'day'   => 10,
        'month' => 1,
        'year'  => 1,
        'ytd'   => 1,
    ];


    /**
     * Valid frequency types are they keys. Valid frequencies are the array values.
     */
    const FREQUENCY_TYPES = [
        'day'   => [ 'minute' ],
        'month' => [ 'daily', 'weekly' ],
        'year'  => [ 'daily', 'weekly', 'monthly' ],
        'ytd'   => [ 'daily', 'weekly' ],
    ];

    const DEFAULT_FREQUENCIES = [
        'day'   => 'minute',
        'month' => 'weekly',
        'year'  => 'monthly',
        'ytd'   => 'weekly',
    ];


    /**
     * Get historical Open, High, Low, Close, and Volume for a given frequency (i.e. aggregation).
     * Frequency available is dependent on periodType selected.
     * Schwab requires a datetime format in EPOCH milliseconds.
     * To stay consistent, I use Carbon objects for all of my Date/Time parameters.
     *
     * @param string              $symbol                The Equity symbol used to look up price history. Ex: AAPL
     * @param string|NULL         $periodType            The chart period being requested. See valid values above.
     * @param int|NULL            $period                The number of chart period types. See notes below.
     * @param string|NULL         $frequencyType         The time frequencyType. See notes below.
     * @param int|NULL            $frequency             The time frequency duration. See notes below.
     * @param \Carbon\Carbon|NULL $startDate             If not specified startDate will be (endDate - period) excluding weekends and holidays.
     * @param \Carbon\Carbon|NULL $endDate               If not specified, the endDate will default to the market close of previous business day.
     * @param bool                $needExtendedHoursData Need extended hours data
     * @param bool                $needPreviousClose     Need previous close price/date
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     *
     * PARAMETER NOTES
     * $this->period
     * If the periodType is
     * • day - valid values are 1, 2, 3, 4, 5, 10
     * • month - valid values are 1, 2, 3, 6
     * • year - valid values are 1, 2, 3, 5, 10, 15, 20
     * • ytd - valid values are 1
     *
     * If the period is not specified and the periodType is
     * • day - default period is 10.
     * • month - default period is 1.
     * • year - default period is 1.
     * • ytd - default period is 1.
     *
     * $this->frequencyType
     * If the periodType is
     * • day - valid value is minute
     * • month - valid values are daily, weekly
     * • year - valid values are daily, weekly, monthly
     * • ytd - valid values are daily, weekly
     *
     * If frequencyType is not specified, default value depends on the periodType
     * • day - defaulted to minute.
     * • month - defaulted to weekly.
     * • year - defaulted to monthly.
     * • ytd - defaulted to weekly.
     *
     * $this->>frequency
     * If the frequencyType is
     * • minute - valid values are 1, 5, 10, 15, 30
     * • daily - valid value is 1
     * • weekly - valid value is 1
     * • monthly - valid value is 1
     *
     * If frequency is not specified, default value is 1
     */
    public function priceHistory( string $symbol,
                                  string $periodType = NULL,
                                  int    $period = NULL,
                                  string $frequencyType = NULL,
                                  int    $frequency = NULL,
                                  Carbon $startDate = NULL,
                                  Carbon $endDate = NULL,
                                  bool   $needExtendedHoursData = FALSE,
                                  bool   $needPreviousClose = FALSE
    ): array {
        $suffix                                     =  '/marketdata/v1/pricehistory';
        $queryParameters                            = [];
        $queryParameters[ 'symbol' ]                = $symbol;
        $queryParameters[ 'needExtendedHoursData' ] = $needExtendedHoursData;
        $queryParameters[ 'needPreviousClose' ]     = $needPreviousClose;

        // Parameter Validation
        $this->throwExceptionIfInvalidParameters( $periodType,
                                                  $period,
                                                  $frequencyType,
                                                  $frequency,
                                                  $startDate,
                                                  $endDate );


        if ( $periodType ):
            $queryParameters[ 'periodType' ] = $periodType;
            $queryParameters[ 'period' ]     = $period ?? self::DEFAULT_PERIODS[ $periodType ];
        endif;

        if ( $frequencyType ):
            $queryParameters[ 'frequencyType' ] = $frequencyType;
            $queryParameters[ 'frequency' ]     = $frequency ?? self::DEFAULT_FREQUENCIES[ $frequencyType ];
        endif;


        if ( $startDate ):
            $queryParameters[ 'startDate' ] = $startDate->getTimestampMs();
        endif;

        if ( $endDate ):
            $queryParameters[ 'endDate' ] = $endDate->getTimestampMs();
        endif;


        $suffix .= '?' . http_build_query( $queryParameters );

        //https://api.schwabapi.com/trader/v1/pricehistory?symbol=AAPL&needExtendedHoursData=0&needPreviousClose=0
        return $this->_request( $suffix );
    }


    /**
     * @param string|NULL         $periodType
     * @param int|NULL            $period
     * @param string|NULL         $frequencyType
     * @param int|NULL            $frequency
     * @param \Carbon\Carbon|NULL $startDate
     * @param \Carbon\Carbon|NULL $endDate
     *
     * @return void
     * @throws \Exception
     */
    protected function throwExceptionIfInvalidParameters( string &$periodType = NULL,
                                                          int    $period = NULL,
                                                          string &$frequencyType = NULL,
                                                          int    $frequency = NULL,
                                                          Carbon $startDate = NULL,
                                                          Carbon $endDate = NULL ): void {
        if ( $periodType ):
            $periodType = strtolower( $periodType );
            if ( !array_key_exists( $periodType, self::PERIOD_TYPES ) ):
                throw new \Exception( "The periodType you passed in '" . $periodType . "', was not in the list of valid PERIOD_TYPES: " . implode( ', ', self::PERIOD_TYPES ) );
            endif;

            if ( $period && !in_array( $period, self::PERIOD_TYPES[ $periodType ] ) ):
                throw new \Exception( "You entered a periodType of '" . $periodType . "' and a period of " . $period . ". Valid period values for " . $periodType . " are " . implode( ', ', self::PERIOD_TYPES[ $periodType ] ) );
            endif;
        endif;

        if ( $frequencyType ):
            $frequencyType = strtolower( $frequencyType );
            if ( !array_key_exists( $frequencyType, self::FREQUENCY_TYPES ) ):
                throw new \Exception( "The frequencyType you passed in '" . $frequencyType . "', was not in the list of valid FREQUENCY_TYPES: " . implode( ', ', self::FREQUENCY_TYPES ) );
            endif;

            if ( $frequency && !in_array( $frequency, self::FREQUENCY_TYPES[ $frequencyType ] ) ):
                throw new \Exception( "You entered a frequencyType of '" . $frequencyType . "' and a frequency of " . $frequency . ". Valid frequency values for " . $frequencyType . " are " . implode( ', ', self::FREQUENCY_TYPES[ $frequencyType ] ) );
            endif;
        endif;

        if ( $startDate && $endDate && $startDate->gt( $endDate ) ):
            throw new \Exception( "You passed in startDate and endDate, but startDate was greater than endDate: " . $startDate->toDateString() . '>' . $endDate->toDateString() );
        endif;


    }

}