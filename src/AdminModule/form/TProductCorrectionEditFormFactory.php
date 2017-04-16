<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Product;
use App\Model\Doc\ProductCorrection;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TProductCorrectionEditFormFactory {

	/** @var ProductCorrection */
	protected $correction;

	public function __construct(EntityManager $em, Product $product, ProductCorrection $correction) {
		parent::__construct($em, $product);
		$this->correction = $correction;
	}

	protected function getDefaults() {
		$correction = $this->correction;

		$defaults = [
			'piece' => $correction->piece,
			'note' => $correction->note,
		];

		return $defaults;
	}

	public function formSucceeded(Form $form, $values) {
		$correction = $this->correction;
		$this->applyValues($correction, $values);
	}

}
