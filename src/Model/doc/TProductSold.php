<?php

namespace Nimleaf\Shopleaf\Model\Doc;

use App\Model\Doc\Order;
use App\Model\Doc\Product;

trait TProductSold {

	/**
	 * @ORM\ManyToOne(targetEntity="Order")
	 * @var Order objednávka
	 */
	protected $order;

	/**
	 * @ORM\ManyToOne(targetEntity="Product")
	 * @var Product prodaný produkt
	 */
	protected $product;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * @var int počet prodaných kusů
	 */
	protected $piece;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 * @var int cena za kus
	 */
	protected $price;

	public function __construct(Order $order, Product $product, $piece) {
		parent::__construct();
		$this->order = $order;
		$this->product = $product;
		$this->piece = $piece;
		$this->price = $product->getFinalPrice();
	}
	
	public function __toString() {
		return $this->product;
	}
	
	public function getOrder() {
		return $this->order;
	}
	
	public function getProduct() {
		return $this->product;
	}
	
	/**
	 * Přepočítá počet kusů produktu při vymazání prodeje
	 * @ORM\PreRemove
	 */
	public function hookPreRemove() {
		$this->product->recountCachePiece();
	}
	
	public function setPiece($piece) {
		$this->piece = $piece;
	}
	
	public function setPrice($price) {
		$this->price = $price;
	}

}
