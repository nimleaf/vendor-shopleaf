<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\ProductCategory;
use App\Model\Doc\AukroCategory;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TProductCategoryCreateFormFactory {

	public function __construct(EntityManager $em) {
		parent::__construct($em);
	}

	protected function applyValues(ProductCategory $category, $values) {
		$aukroCategory = $category->getAukro();
		$aukroCategory->category = $values->aukroCategory;
		$aukroCategory->save($this->em);
		
		$category->name = $values->name;
		$category->save($this->em);
	}

	public function create() {
		$form = parent::create();

		$form->addText('name', _("název"))
				->setRequired();
		
		$form->addSelect('aukroCategory', _("kategorie na aukro.cz"), AukroCategory::$CATEGORY);

		$form->addSubmit('send', _("Uložit"));

		$form->setDefaults($this->getDefaults());

		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		$category = new ProductCategory;
		$this->applyValues($category, $values);
	}

}
