<?php

class Irrigation
{
    public $id;
    public $pivotId;
    public $applicationAmount;
    public $irrigationDate;
    public $userId;

    public function __construct($id, $pivotId, $applicationAmount, $irrigationDate, $userId)
    {
        $this->id = $id;
        $this->pivotId = $pivotId;
        $this->applicationAmount = $applicationAmount;
        $this->irrigationDate = $irrigationDate;
        $this->userId = $userId;
    }
}
