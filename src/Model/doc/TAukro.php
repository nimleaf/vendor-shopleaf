<?php

namespace Nimleaf\Shopleaf\Model\Doc;

use Doctrine\ORM\EntityManager;

trait TAukro {

	public static $AUTO_REFRESH_AUCTION = [
		0 => "Bez pokračování",
		1 => "Pokračovat s pevným seznamem produktů",
		2 => "Pokračovat pouze s neprodanými produkty",
	];

	public static $COUNTRY = [
		56 => "Česká republika",
	];
	public static $DAYS = [
		0 => "3 dny",
		1 => "5 dní",
		2 => "7 dní",
		3 => "10 dní",
		4 => "14 dní",
		5 => "30 dní",
	];
	public static $PAYMENT_FORM = [
		1 => "Zaplaťte předem (banka)",
		2 => "-",
		4 => "Podrobnosti v popisu",
		8 => "Faktura s DPH",
	];
	public static $SHIPPING_FORM = [
		0 => "Prodávající je povinen nést náklady na dopravu",
		1 => "Kupující hradí náklady na dopravu",
	];

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string API Aukro
	 */
	public $apiKey;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string uživatelské jméno
	 */
	public $username;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string heslo
	 */
	public $password;

	/////////////////////

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string automatická obnova aukce
	 */
	public $autoRefreshAuction;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string počet dní
	 */
	public $days;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string forma platby
	 */
	public $paymentForm;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string forma dopravy
	 */
	public $shippingForm;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string země
	 */
	public $country;
	 
	/**
	 * Nastavení je pouze jednou
	 * @param EntityManager $em
	 * @return Aukro
	 */
	public static function getAukro(EntityManager $em) {
		return self::getAll($em, NULL, 1);
	}

	/**
	 * Vždy FALSE, nastavení nelze smazat
	 * @param EntityManager $em
	 * @return FALSE
	 */
	public function canBeDeleted(EntityManager $em) {
		return FALSE;
	}

	public function getCountryName() {
		return self::$COUNTRY[$this->country];
	}
}
