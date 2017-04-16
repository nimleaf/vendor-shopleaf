<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Payment;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TPaymentEditFormFactory {

	/** @var Payment */
	protected $payment;

	public function __construct(EntityManager $em, Payment $payment) {
		parent::__construct($em);
		$this->payment = $payment;
	}

	protected function getDefaults() {
		$payment = $this->payment;
		$defaults = [
			'name' => $payment->name,
			'description' => $payment->description,
			'paymentType' => $payment->paymentType,
			'price' => $payment->price,
		];

		return $defaults;
	}

	public function formSucceeded(Form $form, $values) {
		$payment = $this->payment;
		$this->applyValues($payment, $values);
	}

}
