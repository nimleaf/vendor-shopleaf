<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Order;
use App\Model\Doc\Voucher;
use App\Model\Doc\VoucherExeption;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TOrderVoucherSoldAddFormFactory {

	/** @var Order */
	protected $order;

	public function __construct(EntityManager $em, Order $order) {
		parent::__construct($em);
		$this->order = $order;
	}

	protected function getDefaults() {
		$defaults = [
		];

		return $defaults;
	}

	public function create() {
		$form = parent::create();

		$form->addText('code', _("kód"))
				->setRequired();

		$form->addSubmit('send', _("vložit"));

		$form->setDefaults($this->getDefaults());

		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		$voucher = Voucher::loadOneBy($this->em, ['code' => $values->code]);
		$orderPrice = $this->order->getProductsPrice();
		if ($voucher) {
			try {
				$voucher->validateVoucher($orderPrice, $this->order->voucherSold);
				$this->order->addVoucherSold($voucher);
				$this->order->save($this->em);
			} catch (VoucherExeption $ex) {
				$form->addError($ex->getMessage());
			}
		} else {
			$form->addError("Kupón nelze vložit, neplatný kód.");
		}
	}

}
