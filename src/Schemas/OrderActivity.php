<?php

namespace MichaelDrennen\SchwabAPI\Schemas;


class OrderActivity {


    /**
     * @var string
     * @example EXECUTION, ORDER_ACTION
     */
    protected string $activityType;
    /**
     * @var string
     * @example FILL
     */
    protected string $executionType;

    /**
     * @var float
     */
    protected float $quantity;

    /**
     * @var float
     */
    protected float $orderRemainingQuantity;

    /**
     * @var \MichaelDrennen\SchwabAPI\Schemas\ExecutionLeg[]
     */
    protected array $executionLegs;

    public function __construct() {

    }


}