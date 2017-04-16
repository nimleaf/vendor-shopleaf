<?php

namespace Nimleaf\Shopleaf\Model\Doc;

use Doctrine\ORM\Mapping as ORM;
use App\Model\Doc\Order;
use App\Model\Doc\Voucher;

trait TVoucherSold {

	/**
	 * @ORM\ManyToOne(targetEntity="Order")
	 * @var Order objednávka
	 */
	protected $order;

	/**
	 * @ORM\ManyToOne(targetEntity="Voucher")
	 * @var Voucher aktivovaný voucher
	 */
	protected $voucher;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 * @var int sleva
	 */
	protected $discount;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var sting typ slevy
	 */
	protected $discountType;

	public function __construct(Order $order, Voucher $voucher) {
		parent::__construct();
		$this->order = $order;
		$this->voucher = $voucher;
		$this->discount = $voucher->discount;
		$this->discountType = $voucher->discountType;
	}

	public function __toString() {
		return $this->voucher->__toString();
	}

	public function getOrder() {
		return $this->order;
	}

	public function getTranslatedDiscount() {
		return '- ' . $this->discount . ' ' . Voucher::$DISCOUNT_TYPE[$this->discountType];
	}

	public function getVoucher() {
		return $this->voucher;
	}

}
