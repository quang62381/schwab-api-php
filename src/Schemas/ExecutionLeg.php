<?php

namespace MichaelDrennen\SchwabAPI\Schemas;


class ExecutionLeg {


    /**
     * @var int
     */
    protected int $legId;

    /**
     * @var float
     */
    protected float $price;

    /**
     * @var float
     */
    protected float $quantity;

    /**
     * @var float
     */
    protected float $mismarkedQuantity;

    /**
     * @var int
     */
    protected int $instrumentId;

    /**
     * @var string date-time
     */
    protected string $time;


    public function __construct() {

    }


}