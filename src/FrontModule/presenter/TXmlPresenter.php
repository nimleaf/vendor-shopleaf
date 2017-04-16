<?php

namespace Nimleaf\Shopleaf\FrontModule\Presenters;

use App\Model\Doc\Product;
use App\Model\Doc\Shipping;

trait TXmlPresenter {

	public function renderHeureka() {
		$this->template->products = Product::getAvaible($this->em);
		$this->template->productLink = $this->link('//Product:view');
		$this->template->imageLink = '/img/product/';
		$this->template->shipping = Shipping::getAll($this->em);
	}

}
