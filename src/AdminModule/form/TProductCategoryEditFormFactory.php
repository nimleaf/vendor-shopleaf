<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\ProductCategory;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TProductCategoryEditFormFactory {

	/** @var Category */
	protected $category;

	public function __construct(EntityManager $em, ProductCategory $category) {
		parent::__construct($em);
		$this->category = $category;
	}

	protected function getDefaults() {
		$category = $this->category;
		$defaults = [
			'name' => $category->name,
			'aukroCategory' => $category->getAukro()->category,
		];

		return $defaults;
	}

	public function formSucceeded(Form $form, $values) {
		$category = $this->category;
		$this->applyValues($category, $values);
	}

}
