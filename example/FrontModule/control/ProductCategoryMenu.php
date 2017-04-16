<?php

namespace NimExampleShopleaf\App\FrontModule\Control;

use Nimleaf\Shopleaf\FrontModule\Control as Shopleaf;

class ProductCategoryMenu extends Base {

	use Shopleaf\TProductCategoryMenu;

	protected $templateFileName = __DIR__ . '/templates/productCategoryMenu.latte';

}
