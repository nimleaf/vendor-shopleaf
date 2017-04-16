<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Order;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;
use Nimleaf\String;

trait TOrderShippingTrackingCodeFormFactory {
	
	/** @var Order */
	private $order;

	public function __construct(EntityManager $em, Order $order) {
		parent::__construct($em);
		$this->order = $order;
	}
	

	protected function applyValues(Order $order, $values) {
		$order->shippingTrackingCode = String::emptyToNull($values->shippingTrackingCode);
		$order->save($this->em);
	}

	protected function getDefaults() {
		$defaults = [
			'shippingTrackingCode' => $this->order->shippingTrackingCode,
		];

		return $defaults;
	}

	public function create() {
		$status = Order::$STATUS;

		$form = parent::create();

		$form->addText('shippingTrackingCode', _("sledovací kód"), $status)
				->setAttribute('class', "form-control");

		$form->addSubmit('send', _("Uložit"));

		$form->setDefaults($this->getDefaults());

		return $form;
	}
	
	public function formSucceeded(Form $form, $values) {
		$order = $this->order;
		$this->applyValues($order, $values);
	}

}
