<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\ProductTag;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TProductTagEditFormFactory {

	/** @var ProductTag */
	protected $productTag;

	public function __construct(EntityManager $em, ProductTag $productTag) {
		parent::__construct($em);
		$this->productTag = $productTag;
	}

	protected function getDefaults() {
		$productTag = $this->productTag;
		$defaults = [
			'name' => $productTag->name,
		];

		return $defaults;
	}

	public function formSucceeded(Form $form, $values) {
		$productTag = $this->productTag;
		$this->applyValues($productTag, $values);
	}

}
