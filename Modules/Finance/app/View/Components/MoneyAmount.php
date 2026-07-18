<?php

namespace Modules\Finance\View\Components;

use Illuminate\View\Component;
use Modules\Finance\Support\Money;

class MoneyAmount extends Component
{
    public string $formatted;

    public function __construct(
        public float|int|string $amount,
        public string $currency,
        public ?float $exchangeRate = null,
        public ?float $baseAmount = null,
        public bool $convert = true,
    ) {
        $amount = (float) $amount;

        $this->formatted = $this->convert
            ? Money::displayLedger($amount, $currency, $this->exchangeRate, $this->baseAmount)
            : Money::format($amount, $currency);
    }

    public function render()
    {
        return view('finance::components.money-amount');
    }
}
