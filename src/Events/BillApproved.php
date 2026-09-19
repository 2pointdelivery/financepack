<?php

namespace FinancePack\Events;

use FinancePack\Models\Bill;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillApproved
{
    use Dispatchable, SerializesModels;

    public Bill $bill;

    public function __construct(Bill $bill)
    {
        $this->bill = $bill;
    }
}
