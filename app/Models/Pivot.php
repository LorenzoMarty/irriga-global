<?php

class Pivot
{
    public $id;
    public $description;
    public $flowRate;
    public $minApplicationDepth;
    public $userId;

    public function __construct($id, $description, $flowRate, $minApplicationDepth, $userId)
    {
        $this->id = $id;
        $this->description = $description;
        $this->flowRate = $flowRate;
        $this->minApplicationDepth = $minApplicationDepth;
        $this->userId = $userId;
    }
}
