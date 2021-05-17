<?php

namespace Modules\Payment\Gateways;

use Illuminate\Http\Request;
use Modules\Order\Entities\Order;
use Modules\Payment\GatewayInterface;
use Modules\Payment\Responses\NullResponse;

class Epayco implements GatewayInterface
{
    public $label;
    public $description;

    public function __construct()
    {
        $this->label = setting('epayco_label');
        $this->description = setting('epayco_description');

        \Stripe\Stripe::setApiKey(setting('epayco_secret_key'));
    }

    public function purchase(Order $order, Request $request)
    {
        $intent = PaymentIntent::create([
            'amount' => $order->total->subunit(),
            'currency' => setting('default_currency'),
        ]);

        return new EpaycoResponse($order, $intent);
    }

    public function complete(Order $order)
    {
        return new EpaycoResponse($order, new PaymentIntent(request('paymentIntent')));
    }
}
