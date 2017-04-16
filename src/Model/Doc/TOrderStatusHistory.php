<?php

namespace Nimleaf\Shopleaf\Model\Doc;

use App\Model\Doc\Order;
use Doctrine\ORM\Mapping as ORM;

trait TOrderStatusHistory {

	/**
	 * @ORM\ManyToOne(targetEntity="Order")
	 * @var Order objednávka
	 */
	protected $order;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @var string status objednávky
	 */
	protected $status;

	public function __construct(Order $order, $status) {
		parent::__construct();
		$this->order = $order;
		$this->status = $status;
	}

	public function __toString() {
		return Order::$STATUS[$this->status];
	}

	public function getStatus() {
		return $this->status;
	}

}
