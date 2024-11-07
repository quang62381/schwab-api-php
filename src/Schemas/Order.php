<?php

namespace MichaelDrennen\SchwabAPI\Schemas;


class Order {

    /**
     * @var string
     * @example NORMAL, AM, PM, SEAMLESS
     */
    protected string $session;

    /**
     * @var string
     * @example DAY, GOOD_TILL_CANCEL, FILL_OR_KILL, IMMEDIATE_OR_CANCEL, END_OF_WEEK, END_OF_MONTH, NEXT_END_OF_MONTH, UNKNOWN
     */
    protected string $duration;

    /**
     * @var string Same as orderType, but does not have UNKNOWN since this type is not allowed as an input
     * @example MARKET, LIMIT, STOP, STOP_LIMIT, TRAILING_STOP, CABINET, NON_MARKETABLE, MARKET_ON_CLOSE, EXERCISE, TRAILING_STOP_LIMIT, NET_DEBIT, NET_CREDIT, NET_ZERO, LIMIT_ON_CLOSE
     */
    protected string $orderType;

    /**
     * @var string date-time
     */
    protected string $cancelTime;

    /**
     * @var string
     * @example NONE, COVERED, VERTICAL, BACK_RATIO, CALENDAR, DIAGONAL, STRADDLE, STRANGLE, COLLAR_SYNTHETIC, BUTTERFLY, CONDOR, IRON_CONDOR, VERTICAL_ROLL, COLLAR_WITH_STOCK, DOUBLE_DIAGONAL, UNBALANCED_BUTTERFLY, UNBALANCED_CONDOR, UNBALANCED_IRON_CONDOR, UNBALANCED_VERTICAL_ROLL, MUTUAL_FUND_SWAP, CUSTOM
     */
    protected string $complexOrderStrategyType;

    /**
     * @var float
     */
    protected float $quantity;

    /**
     * @var float
     */
    protected float $filledQuantity;

    /**
     * @var float
     */
    protected float $remainingQuantity;

    /**
     * @var string
     */
    protected string $destinationLinkName;

    /**
     * @var string date-time
     */
    protected string $releaseTime;

    /**
     * @var float
     */
    protected float $stopPrice;

    /**
     * @var string
     * @example MANUAL, BASE, TRIGGER, LAST, BID, ASK, ASK_BID, MARK, AVERAGE
     */
    protected string $stopPriceLinkBasis;

    /**
     * @var string
     * @example VALUE, PERCENT, TICK
     */
    protected string $stopPriceLinkType;

    /**
     * @var float
     */
    protected float $stopPriceOffset;

    /**
     * @var string
     * @example STANDARD, BID, ASK, LAST, MARK
     */
    protected string $stopType;

    /**
     * @var string
     * @example MANUAL, BASE, TRIGGER, LAST, BID, ASK, ASK_BID, MARK, AVERAGE
     */
    protected string $priceLinkBasis;

    /**
     * @var string
     * @example VALUE, PERCENT, TICK
     */
    protected string $priceLinkType;

    /**
     * @var float
     */
    protected float $price;

    /**
     * @var string
     * @example FIFO, LIFO, HIGH_COST, LOW_COST, AVERAGE_COST, SPECIFIC_LOT, LOSS_HARVESTER
     */
    protected string $taxLotMethod;


    /**
     * @var \MichaelDrennen\SchwabAPI\Schemas\OrderLegCollection[]
     */
    protected array $orderLegCollection;


    /**
     * @var float
     */
    protected float $activationPrice;

    /**
     * @var string
     * @example ALL_OR_NONE, DO_NOT_REDUCE, ALL_OR_NONE_DO_NOT_REDUCE
     */
    protected string $specialInstruction;

    /**
     * @var string
     * @example SINGLE, CANCEL, RECALL, PAIR, FLATTEN, TWO_DAY_SWAP, BLAST_ALL, OCO, TRIGGER
     */
    protected string $orderStrategyType;

    /**
     * @var int
     */
    protected int $orderId;

    /**
     * @var bool
     */
    protected bool $cancelable;

    /**
     * @var bool
     */
    protected bool $editable;

    /**
     * @var string
     * @example AWAITING_PARENT_ORDER, AWAITING_CONDITION, AWAITING_STOP_CONDITION, AWAITING_MANUAL_REVIEW, ACCEPTED, AWAITING_UR_OUT, PENDING_ACTIVATION, QUEUED, WORKING, REJECTED, PENDING_CANCEL, CANCELED, PENDING_REPLACE, REPLACED, FILLED, EXPIRED, NEW, AWAITING_RELEASE_TIME, PENDING_ACKNOWLEDGEMENT, PENDING_RECALL, UNKNOWN
     */
    protected string $status;

    /**
     * @var string date-time
     */
    protected string $enteredTime;

    /**
     * @var string date-time
     */
    protected string $closeTime;

    /**
     * @var string
     */
    protected string $tag;

    /**
     * @var int
     */
    protected int $accountNumber;


    /**
     * @var \MichaelDrennen\SchwabAPI\Schemas\OrderActivity[]
     */
    protected array $orderActivityCollection;

    /**
     * @var \MichaelDrennen\SchwabAPI\Schemas\Order[]
     */
    protected array $replacingOrderCollection;

    /**
     * @var \MichaelDrennen\SchwabAPI\Schemas\Order[]
     */
    protected array $childOrderStrategies;

    /**
     * @var string
     */
    protected string $statusDescription;


    public function __construct(){

    }


}