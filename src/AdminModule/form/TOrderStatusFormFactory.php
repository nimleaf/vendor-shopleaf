<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Order;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TOrderStatusFormFactory {
	
	/** @var Order */
	private $order;

	public function __construct(EntityManager $em, Order $order) {
		parent::__construct($em);
		$this->order = $order;
	}
	

	protected function applyValues(Order $order, $values) {
		$order->setStatus($values->status);
		$order->save($this->em);
	}

	protected function getDefaults() {
		$defaults = [
			'sendEmail' => TRUE,
			'status' => $this->order->status,
		];

		return $defaults;
	}

	public function create() {
		$status = Order::$STATUS;

		$form = parent::create();

		$form->addSelect('status', _("status"), $status)
				->setAttribute('class', "form-control");
		
		$form->addCheckbox('sendEmail', _("odeslat e-mail"));

		$form->addSubmit('send', _("Uložit"));

		$form->setDefaults($this->getDefaults());

		return $form;
	}
	
	public function formSucceeded(Form $form, $values) {
		$order = $this->order;
		$this->applyValues($order, $values);
	}

}
