<?php

namespace modules\pizzautilities;

use yii\base\Module;

use Craft;

use craft\commerce\elements\Order;
use craft\commerce\events\LineItemEvent;
use craft\commerce\services\OrderAdjustments;
use craft\events\RegisterComponentTypesEvent;

use yii\base\Event;


use modules\pizzautilities\adjusters\ToppingsAdjuster;

class PizzaUtilities extends Module
{

	public static $instance;

	public function init()
	{
		parent::init();
		self::$instance = $this;

		Craft::setAlias('@modules/pizzautilities', $this->getBasePath());

		$this->controllerNamespace = 'modules\pizzautilites\controllers';

		$this->setComponents([
			'pizza' => Pizza::class,
		]);
		
		
		
		Event::on(
			Order::class,
			Order::EVENT_AFTER_ADD_LINE_ITEM,
			function(LineItemEvent $event) {
				// @var LineItem $lineItem
				$lineItem = $event->lineItem;
				// @var bool $isNew
				$isNew = $event->isNew;
				// ...
			}
		);
		
		
		
		Event::on(
			OrderAdjustments::class,
			OrderAdjustments::EVENT_REGISTER_ORDER_ADJUSTERS,
			function(RegisterComponentTypesEvent $event) {
				$event->types[] = ToppingsAdjuster::class;
			}
		);
	}

}
