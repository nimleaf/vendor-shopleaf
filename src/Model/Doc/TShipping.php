<?php

namespace Nimleaf\Shopleaf\Model\Doc;

trait TShipping {

	public static $PATH_ICON_FOLDER = '/www/icon/shipping/';
	public static $PATH_ICON = '/icon/shipping/';

	/**
	 * @ORM\ManyToMany(targetEntity="Payment")
	 * @var Payment povolené možnosti platby
	 */
	protected $paymentOption = [];

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string $deliveryTime doba dodání
	 */
	public $deliveryTime;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string $description popis
	 */
	public $description;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string $icon ikona
	 */
	public $icon;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string $name název
	 */
	public $name;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string $name Url pro sledování zásilky (%%%%% jako číslo zásilky)
	 */
	public $trackingCodeUrl;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 * @var float $price cena
	 */
	public $price;

	public function __construct() {
		parent::__construct();
	}

	public function &getPaymentOption() {
		return $this->paymentOption;
	}

	public function getIcon() {
		return self::$PATH_ICON . $this->icon;
	}

	/**
	 * Možnosti platby pro toto poštovné
	 * @return ['id' => Payment]
	 */
	public function getPaymentOptionArray() {
		$all = $this->paymentOption;
		$items = [];
		foreach ($all as $item) {
			$items[$item->id] = $item;
		}
		return $items;
	}

	public function getPaymentOptionById($paymentId) {
		foreach ($this->paymentOption as $payment) {
			if ($payment->id === $paymentId) {
				return $payment;
			}
		}
	}

	public function resetPaymentOption() {
		$this->paymentOption = [];
	}

}
