<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Aukro;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TAukroEditFormFactory {

	/** @var Aukro */
	protected $aukro;

	public function __construct(EntityManager $em) {
		parent::__construct($em);
		$this->aukro = Aukro::getAukro($em);
	}

	protected function getDefaults() {
		$aukro = $this->aukro;
		$defaults = [
			'apiKey' => $aukro->apiKey,
			'username' => $aukro->username,
			'password' => $aukro->password,
			'days' => $aukro->days,
			'shippingForm' => $aukro->shippingForm,
			'autoRefreshAuction' => $aukro->autoRefreshAuction,
			'country' => $aukro->country,
		];
		return $defaults;
	}

	public function formSucceeded(Form $form, $values) {
		$aukro = $this->aukro;
		$this->applyValues($aukro, $values);
	}

}
