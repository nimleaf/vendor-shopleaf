<?php

namespace Nimleaf\Shopleaf\FrontModule\Presenters;

use App\Model\Doc\Product;

trait THomepagePresenter {

	public function renderDefault() {
		$this->template->products = Product::getAllProducts($this->em, TRUE, 'dateCreated', 4);
	}
}
