<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\ProductTag;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TProductTagCreateFormFactory {

	public function __construct(EntityManager $em) {
		parent::__construct($em);
	}

	protected function applyValues(ProductTag $productTag, $values) {
		$productTag->name = $values->name;
		$productTag->save($this->em);
	}

	public function create() {
		$form = parent::create();

		$form->addText('name', _("název"))
				->setRequired();
		
		$form->addSubmit('send', _("Uložit"));

		$form->setDefaults($this->getDefaults());

		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		$productTag = new ProductTag;
		$this->applyValues($productTag, $values);
	}

}
