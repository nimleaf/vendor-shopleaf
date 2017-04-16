<?php

namespace Nimleaf\Shopleaf\Model\Doc;

trait TProductPurchase {

	/**
	 * @ORM\manyToOne(targetEntity="Product")
	 * @var Product
	 */
	public $product;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * @var int počet kusů
	 */
	public $piece;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 * @var float cena za kus
	 */
	public $price;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string zdroj
	 */
	public $source;

	public function __construct() {
		parent::__construct();
	}
	
	public function __toString() {
		return $this->id;
	}
	
	/**
	 * Přepočítá počet kusů produktu při vymazání nákupu
	 * @ORM\PreRemove
	 */
	public function hookPreRemove() {
		$this->product->hookPreFlush();
	}

}
