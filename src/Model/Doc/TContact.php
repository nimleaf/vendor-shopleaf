<?php

namespace Nimleaf\Shopleaf\Model\Doc;

trait TContact {

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string telefon
	 */
	public $phone;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string e-mail
	 */
	public $email;

	public function __construct() {
		parent::__construct();
	}

	public function __toString() {
		return $this->email;
	}

}
