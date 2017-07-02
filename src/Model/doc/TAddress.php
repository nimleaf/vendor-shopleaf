<?php

namespace Nimleaf\Shopleaf\Model\Doc;

use Doctrine\ORM\Mapping as ORM;

trait TAddress {

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string jméno
	 */
	public $name;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string příjmení
	 */
	public $surname;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string ulice
	 */
	public $street;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string town
	 */
	public $town;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string zip
	 */
	public $zip;

	public function __construct() {
		parent::__construct();
	}

	public function __toString() {
		return $this->name . ' ' . $this->surname;
	}

}
