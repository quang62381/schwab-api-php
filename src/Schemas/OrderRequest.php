<?php

namespace MichaelDrennen\SchwabAPI\Schemas;


class OrderRequest {

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
     * @var int
     */
    protected int $accountNumber;


    /**
     * @var \MichaelDrennen\SchwabAPI\Schemas\OrderActivity[]
     */
    protected array $orderActivityCollection;

    /**
     * @var \MichaelDrennen\SchwabAPI\Schemas\OrderRequest[]
     */
    protected array $replacingOrderCollection;

    /**
     * @var \MichaelDrennen\SchwabAPI\Schemas\OrderRequest[]
     */
    protected array $childOrderStrategies;

    /**
     * @var string
     */
    protected string $statusDescription;


    public function __construct( string $session = NULL,
                                 string $duration = NULL,
                                 string $orderType = NULL,
                                 string $cancelTime = NULL,
                                 string $complexOrderStrategyType = NULL,
                                 float  $quantity = NULL,
                                 float  $filledQuantity = NULL,
                                 float  $remainingQuantity = NULL,
                                 string $destinationLinkName = NULL,
                                 string $releaseTime = NULL,
                                 float  $stopPrice = NULL,
                                 string $stopPriceLinkBasis = NULL,
                                 string $stopPriceLinkType = NULL,
                                 float  $stopPriceOffset = NULL,
                                 string $stopType = NULL,
                                 string $priceLinkBasis = NULL,
                                 string $priceLinkType = NULL,
                                 float  $price = NULL,
                                 string $taxLotMethod = NULL,
                                 array  $orderLegCollection = NULL,
                                 float  $activationPrice = NULL,
                                 string $specialInstruction = NULL,
                                 string $orderStrategyType = NULL,
                                 int    $orderId = NULL,
                                 bool   $cancelable = NULL,
                                 bool   $editable = NULL,
                                 string $status = NULL,
                                 string $enteredTime = NULL,
                                 string $closeTime = NULL,
                                 int    $accountNumber = NULL,
                                 array  $orderActivityCollection = NULL,
                                 array  $replacingOrderCollection = NULL,
                                 array  $childOrderStrategies = NULL,
                                 string $statusDescription = NULL ) {
        $this->session                  = $session;
        $this->duration                 = $duration;
        $this->orderType                = $orderType;
        $this->cancelTime               = $cancelTime;
        $this->complexOrderStrategyType = $complexOrderStrategyType;
        $this->quantity                 = $quantity;
        $this->filledQuantity           = $filledQuantity;
        $this->remainingQuantity        = $remainingQuantity;
        $this->destinationLinkName      = $destinationLinkName;
        $this->releaseTime              = $releaseTime;
        $this->stopPrice                = $stopPrice;
        $this->stopPriceLinkBasis       = $stopPriceLinkBasis;
        $this->stopPriceLinkType        = $stopPriceLinkType;
        $this->stopPriceOffset          = $stopPriceOffset;
        $this->stopType                 = $stopType;
        $this->priceLinkBasis           = $priceLinkBasis;
        $this->priceLinkType            = $priceLinkType;
        $this->price                    = $price;
        $this->taxLotMethod             = $taxLotMethod;
        $this->orderLegCollection       = $orderLegCollection;
        $this->activationPrice          = $activationPrice;
        $this->specialInstruction       = $specialInstruction;
        $this->orderStrategyType        = $orderStrategyType;
        $this->orderId                  = $orderId;
        $this->cancelable               = $cancelable;
        $this->editable                 = $editable;
        $this->status                   = $status;
        $this->enteredTime              = $enteredTime;
        $this->closeTime                = $closeTime;
        $this->accountNumber            = $accountNumber;
        $this->orderActivityCollection  = $orderActivityCollection;
        $this->replacingOrderCollection = $replacingOrderCollection;
        $this->childOrderStrategies     = $childOrderStrategies;
        $this->statusDescription        = $statusDescription;
    }


}