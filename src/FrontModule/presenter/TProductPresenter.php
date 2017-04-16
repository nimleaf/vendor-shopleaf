<?php

namespace Nimleaf\Shopleaf\FrontModule\Presenters;

use Nimleaf\Sandbox\Exception\PageNotFound;
use App\Model\Doc\Product;

trait TProductPresenter {
	
	/** @var Product */
	protected $product;

	public function actionView($id) {
		$this->product = Product::load($this->em, $id);
		if (!$this->product) {
			throw new PageNotFound;
		}
		$this->product->recountCachePiece();
		$this->product->save($this->em);
		$this->menuItemId = $this->product->category->id;
	}

	public function renderView() {
        $this->template->product = $this->product;
    }

}
