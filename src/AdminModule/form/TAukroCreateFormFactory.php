<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Aukro;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TAukroCreateFormFactory {

	public function __construct(EntityManager $em) {
		parent::__construct($em);
	}

	protected function applyValues(Aukro $aukro, $values) {
		$aukro->apiKey = $values->apiKey;
		$aukro->username = $values->username;
		$aukro->password = $values->password;
		$aukro->paymentForm = $values->paymentForm;
		$aukro->shippingForm = $values->shippingForm;
		$aukro->autoRefreshAuction = $values->autoRefreshAuction;
		$aukro->days = $values->days;
		$aukro->country = $values->country;

		$aukro->save($this->em);
	}

	public function create() {		
		$form = parent::create();

		$form->addGroup(_("přihlašovací údaje"));

		$form->addText('apiKey', _("klíč API Aukro"))
				->setRequired();
		
		$form->addText('username', _("uživatelské jméno"))
				->setRequired();
		
		$form->addPassword('password', _("heslo"))
				->setRequired();
		
		$form->addGroup(_("nastavení prodeje"));

		$form->addSelect('days', _("počet dní trvání aukce"), Aukro::$DAYS)
				->setRequired();
		
		$form->addSelect('paymentForm', _("forma platby"), Aukro::$PAYMENT_FORM)
				->setRequired();
		
		$form->addSelect('shippingForm', _("forma dopravy"), Aukro::$SHIPPING_FORM)
				->setRequired();
		
		$form->addSelect('autoRefreshAuction', _("automatická obnova aukce"), Aukro::$AUTO_REFRESH_AUCTION)
				->setRequired();

		$form->addGroup(_("nastavení adresy"));
		
		$form->addSelect('country', _("stát"), Aukro::$COUNTRY)
				->setRequired();


		$form->addGroup();

		$form->addSubmit('send', _("Uložit"));

		$form->setDefaults($this->getDefaults());

		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		//do nothing
	}

}
