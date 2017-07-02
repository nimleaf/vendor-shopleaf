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
}
