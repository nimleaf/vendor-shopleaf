<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\ProductPurchase;
use App\Model\Doc\Product;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TProductPurchaseCreateFormFactory {

	protected $product;

	public function __construct(EntityManager $em, Product $product) {
		parent::__construct($em);
		$this->product = $product;
	}
	
	protected function applyValues(ProductPurchase $purchase, $values) {
		$purchase->piece = $values->piece;
		$purchase->price = $values->price;
		$purchase->product = $this->product;
		
		$this->product->save($this->em);
	}

	public function create() {
		$form = parent::create();

		$form->addText('piece', _("počet kusů"))
				->setRequired()
				->setType('number');

		$form->addText('price', _("cena za kus"))
				->setRequired();

		$form->setDefaults($this->getDefaults());
		
		$form->addSubmit('send', _("Uložit"));

		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		$purchase = new ProductPurchase;
		$this->product->setPurchase($purchase);
		
		$this->applyValues($purchase, $values);
	}

}
