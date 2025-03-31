<?php
namespace App\View\Components;

use Carbon\Carbon;
use Illuminate\View\Component;

class Countdown extends Component
{
    public $endDate;
    public $nowDate;
    public $productId;

    public function __construct($endDate, $productId)
    {
        $this->endDate = $endDate;
        $this->nowDate = now();
        $this->productId = $productId;
    }

    public function render()
    {
        return view('components.countdown');
    }
}
