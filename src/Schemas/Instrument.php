<?php

namespace MichaelDrennen\SchwabAPI\Schemas;


class Instrument {


    /**
     * @var string
     */
    protected string $cusip;

    /**
     * @var string
     */
    protected string $symbol;

    /**
     * @var string
     */
    protected string $description;

    /**
     * @var string
     */
    protected string $exchange;

    /**
     * @var string
     * @example BOND, EQUITY, ETF, EXTENDED, FOREX, FUTURE, FUTURE_OPTION, FUNDAMENTAL, INDEX, INDICATOR, MUTUAL_FUND, OPTION, UNKNOWN
     */
    protected string $assetType;

    /**
     * @var string
     * @example BOND, EQUITY, ETF, EXTENDED, FOREX, FUTURE, FUTURE_OPTION, FUNDAMENTAL, INDEX, INDICATOR, MUTUAL_FUND, OPTION, UNKNOWN
     */
    protected string $type;

    public function __construct( string $cusip,
                                 string $symbol,
                                 string $description,
                                 string $exchange,
                                 string $assetType,
                                 string $type ) {
        $this->cusip       = $cusip;
        $this->symbol      = $symbol;
        $this->description = $description;
        $this->exchange    = $exchange;
        $this->assetType   = $assetType;
        $this->type        = $type;
    }


}