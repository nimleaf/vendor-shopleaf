<?php

namespace Nimleaf\Shopleaf\Model\Doc;

trait TProductCorrection {

	/**
	 * @ORM\manyToOne(targetEntity="Product")
	 * @var Product
	 */
	public $product;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string poznámka
	 */
	public $note;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * @var int počet kusů
	 */
	public $piece;

	public function __construct() {
		parent::__construct();
	}

	public function __toString() {
		return $this->id;
	}

	/**
	 * Přepočítá počet kusů produktu při vymazání korekce
	 * @ORM\PreRemove
	 */
	public function hookPreRemove() {
		$this->product->hookPreFlush();
	}

}
