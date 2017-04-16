<?php

namespace Nimleaf\Shopleaf\Model\Doc;

use Nimleaf\Editorial\Model\Doc as Editorial;
use Nimleaf\Invoice\Model\Doc as Invoice;

trait TSettings {

	use Editorial\TSettings;

use Invoice\TSettings;

	/**
	 * @ORM\OneToOne(targetEntity="Aukro")
	 * @var Aukro
	 */
	protected $aukro;

	/**
	 * @ORM\OneToOne(targetEntity="SettingsShop")
	 * @var SettingsShop
	 */
	protected $shop;

	/**
	 * @ORM\OneToOne(targetEntity="Banner")
	 * @var Banner
	 */
	public $mainBanner;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string číslo bankovního účtu
	 */
	public $bankAccount;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @var string faktura - dodavatel
	 */
	public $contractor;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @var string faktura - poznámka pod údaji plátce
	 */
	public $contractorNote;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string faktura - DIČ
	 */
	public $dic;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string číslo bankovního účtu v IBAN formátu
	 */
	public $iban;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string faktura - IČ
	 */
	public $ic;

}
