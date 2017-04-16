<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Product;
use App\Model\Doc\ProductCorrection;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TProductCorrectionCreateFormFactory {

	protected $product;

	public function __construct(EntityManager $em, Product $product) {
		parent::__construct($em);
		$this->product = $product;
	}

	protected function applyValues(ProductCorrection $correction, $values) {
		$correction->piece = $values->piece;
		$correction->note = $values->note;

		$this->product->save($this->em);
	}

	public function create() {
		$form = parent::create();

		$form->addText('piece', _("počet kusů"))
				->setRequired()
				->setType('number');

		$form->addText('note', _("poznámka"))
				->setRequired();

		$form->setDefaults($this->getDefaults());

		$form->addSubmit('send', _("Uložit"));

		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		$correction = new ProductCorrection;
		$this->product->setCorrection($correction);

		$this->applyValues($correction, $values);
	}

}
