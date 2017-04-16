<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Payment;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TPaymentCreateFormFactory {

	public function __construct(EntityManager $em) {
		parent::__construct($em);
	}

	protected function applyValues(Payment $payment, $values) {
		$payment->name = $values->name;
		$payment->description = $values->description;
		$payment->paymentType = $values->paymentType;
		$payment->price = $values->price;
		$payment->save($this->em);
	}

	public function create() {
		$paymentType = Payment::$PAYMENT_TYPE;
		
		$form = parent::create();

		$form->addText('name', _("název"))
				->setRequired();
		
		$form->addText('description', _("popis"));
		
		$form->addSelect('paymentType', _("typ platby"), $paymentType)
				->setRequired();

		$form->addText('price', _("cena"))
				->setRequired();

		$form->addSubmit('send', _("Uložit"));

		$form->setDefaults($this->getDefaults());

		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		$payment = new Payment;
		$this->applyValues($payment, $values);
	}

}
