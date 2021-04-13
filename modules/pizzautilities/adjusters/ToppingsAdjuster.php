<?php

namespace modules\pizzautilities\adjusters;

use craft;
use craft\base\Component;
use craft\commerce\base\AdjusterInterface;
use craft\commerce\elements\Order;
use craft\commerce\models\OrderAdjustment;
use craft\commerce\elements\Product;

class ToppingsAdjuster extends Component implements AdjusterInterface
{
    public function adjust(Order $order): array
    {
        $adjustments = [];

        foreach ($order->getLineItems() as $item) {
            $adjustment = new OrderAdjustment;
            $adjustment->type = 'discount';
            $adjustment->name = 'Extra toppings';
            $adjustment->description = '';
            // `sourceSnapshot` can contain information to explain the adjustment
            $adjustment->sourceSnapshot = [ 'data' => 'value' ];
            $adjustment->amount = 0;
            
            $size = strtolower($item->purchasable->title);
            
            if(array_key_exists('Extra Toppings', $item->options)){
                foreach($item->options['Extra Toppings'] as $option) {
                    // get the option price
                    $product = Product::find()
                        ->title($option)
                        ->one();
                    
                    if(array_key_exists($size, $product->prices[0])){
                        $toppingPrice = $product->prices[0][$size];
                        if($toppingPrice) {
                            $adjustment->amount += $toppingPrice;
                        }
                    }                   
                }
            }
            
            $adjustment->setOrder($order);
            $adjustment->setLineItem($item);

            $adjustments[] = $adjustment;
        }

        return $adjustments;
    }
}