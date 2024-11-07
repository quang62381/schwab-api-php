<?php

namespace MichaelDrennen\SchwabAPI\Schemas;


class OrderLegCollection {

    /**
     * @var string
     * @example EQUITY, OPTION, INDEX, MUTUAL_FUND, CASH_EQUIVALENT, FIXED_INCOME, CURRENCY, COLLECTIVE_INVESTMENT
     */
    protected string $orderLegType;

    /**
     * @var int
     */
    protected int $legId;


    /**
     * @var \MichaelDrennen\SchwabAPI\Schemas\AbstractAccountsInstrument
     */
    protected AbstractAccountsInstrument $instrument;

    /**
     * @var string
     * @example BUY, SELL, BUY_TO_COVER, SELL_SHORT, BUY_TO_OPEN, BUY_TO_CLOSE, SELL_TO_OPEN, SELL_TO_CLOSE, EXCHANGE, SELL_SHORT_EXEMPT
     */
    protected string $instruction;

    /**
     * @var string
     * @example OPENING, CLOSING, AUTOMATIC
     */
    protected string $positionEffect;

    /**
     * @var float
     */
    protected float $quantity;

    /**
     * @var string
     * @example ALL_SHARES, DOLLARS, SHARES
     */
    protected string $quantityType;

    /**
     * @var string
     * @example REINVEST, PAYOUT
     */
    protected string $divCapGains;

    /**
     * @var string
     */
    protected string $toSymbol;


    public function __construct() {

    }


}