<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Product;
use App\Model\Doc\ProductPurchase;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TProductPurchaseEditFormFactory {

	/** @var ProductPurchase */
	protected $purchase;

	public function __construct(EntityManager $em, Product $product, ProductPurchase $purchase) {
		parent::__construct($em, $product);
		$this->purchase = $purchase;
	}

	protected function getDefaults() {
		$purchase = $this->purchase;

		$defaults = [
			'piece' => $purchase->piece,
			'price' => $purchase->price,
		];

		return $defaults;
	}

	public function formSucceeded(Form $form, $values) {
		$purchase = $this->purchase;
		$this->applyValues($purchase, $values);
	}

}
