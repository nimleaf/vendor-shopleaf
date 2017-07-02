<?php

namespace Nimleaf\Shopleaf\Model\Doc;

use App\Model\Doc\Shipping;
use Doctrine\ORM\EntityManager;

trait TPayment {

	public static $PAYMENT_TYPE = [
		'upon-receipt' => "po převzetí",
		'in-advance' => "platba předem",
	];

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string $description popis
	 */
	public $description;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string $name název
	 */
	public $name;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string $paymentType typ platby
	 */
	public $paymentType;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 * @var float $price cena
	 */
	public $price;

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Prověří vazby zda nic nebrání smazání
	 * @param EntityManager $em
	 */
	public function canBeDeleted(EntityManager $em) {
		$shippings = Shipping::getAll($em);
		foreach ($shippings as $shipping) {
			if ($shipping->getPaymentOptionById($this->id)) {
				return FALSE;
			}
		}
		return parent::canBeDeleted($em);
	}

	public function getTranslatedPaymentType() {
		return self::$PAYMENT_TYPE[$this->paymentType];
	}

}
