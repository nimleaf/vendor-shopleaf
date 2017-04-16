<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Shipping;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TShippingEditFormFactory {

	/** @var Shipping */
	protected $shipping;

	public function __construct(EntityManager $em, Shipping $shipping) {
		parent::__construct($em);
		$this->shipping = $shipping;
	}

	protected function getDefaults() {
		$shipping = $this->shipping;
		$defaults = [
			'name' => $shipping->name,
			'description' => $shipping->description,
			'deliveryTime' => $shipping->deliveryTime,
			'price' => $shipping->price,
			'paymentOption' => array_keys($shipping->getPaymentOptionArray()),
			'trackingCodeUrl' => $shipping->trackingCodeUrl,
		];

		return $defaults;
	}

	public function formSucceeded(Form $form, $values) {
		$shipping = $this->shipping;
		$this->applyValues($shipping, $values);
	}

}
