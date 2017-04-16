<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\ProductSold;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\Application\UI\Presenter;

trait TProductSoldEditFormFactory {

	/** @var Presenter */
	protected $presenter;

	public function __construct(EntityManager $em, Presenter $presenter) {
		parent::__construct($em);
		$this->presenter = $presenter;
	}

	protected function applyValues(ProductSold $productSold, $values) {
		$productSold->setPiece($values->productPiece);
		$productSold->setPrice($values->productPrice);
		$productSold->save($this->em);
		$order = $productSold->getOrder();
		$order->shippingTrackingCode = $order->shippingTrackingCode;
		$order->save($this->em);
	}

	public function create() {
		return new Multiplier(function ($productSoldId) {
			$productSold = ProductSold::load($this->em, $productSoldId);

			$form = parent::create();

			$form->addHidden('productSoldId', $productSoldId);

			$form->addText('productCode', _("kód"))
					->setAttribute('class', "form-control")
					->setDisabled()
					->setValue($productSold->product->code)
					->setRequired();

			$form->addText('productPiece', _("počet kusů"))
					->setAttribute('class', "form-control")
					->setType('number')
					->setValue($productSold->piece)
					->setRequired();

			$form->addText('productPrice', _("cena"))
					->setAttribute('class', "form-control")
					->setType('number')
					->setValue($productSold->price)
					->setRequired();

			$form->addSubmit('productSend', _("Uložit"));

			return $form;
		});
	}

	public function formSucceeded(Form $form, $values) {
		$productSold = ProductSold::load($this->em, $values->productSoldId);
		$order = $productSold->getOrder();
		if ($values->productPiece == 0) { //vymazat zboží
			$this->presenter->handleDeleteProductSold($order->id, $productSold->id);
		} else {
			$this->applyValues($productSold, $values);
			$this->presenter->flashMessage(_("Zboží bylo upraveno."));
			$this->presenter->redirect('this');
		}
	}

}
