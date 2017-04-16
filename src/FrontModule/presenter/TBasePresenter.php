<?php

namespace Nimleaf\Shopleaf\FrontModule\Presenters;

use App\FrontModule\Control\Basket;
use App\FrontModule\Control\ProductMenu;
use Nimleaf\Editorial\FrontModule\Presenters as Editorial;

trait TBasePresenter {
	
	use Editorial\TBasePresenter;
	
	/** @persistent */
	protected $menuTab = 'category';

	protected function createComponentBasket($name) {
		$basket = new Basket($this->em, $this, $name);
		return $basket;
	}
	
	protected function createComponentProductMenu($name) {
		return new ProductMenu($this->em, $this, $name, $this->menuTab, $this->menuItemId);
	}
	
}
