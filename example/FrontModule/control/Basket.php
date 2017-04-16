<?php

namespace NimExampleShopleaf\App\FrontModule\Control;

use Nimleaf\Shopleaf\FrontModule\Control as Shopleaf;

class Basket extends Base {

	use Shopleaf\TBasket;
	
	protected $templateFileName = __DIR__ . '/templates/basket.latte';
}
