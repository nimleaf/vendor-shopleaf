<?php

namespace Nimleaf\Shopleaf\FrontModule\Form;

use App\Model\Doc\User;
use App\Model\Doc\Voucher;
use App\Model\Doc\VoucherExeption;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TVoucherAddFormFactory {

	/** @var EntityManager */
	protected $em;

	/** @var User */
	protected $user;

	/** @var string */
	protected $orderPrice;

	/** @var array */
	protected $vouchers;

	public function __construct(EntityManager $em, &$vouchers, $orderPrice, User $user = NULL) {
		$this->em = $em;
		$this->orderPrice = $orderPrice;
		$this->vouchers = &$vouchers;
		$this->user = $user;
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
		if ($voucher) {
			try {
				$voucher->validateVoucher($this->orderPrice, $this->vouchers);
				$voucherId = $voucher->id;
				$this->vouchers->$voucherId = $voucher;
			} catch (VoucherExeption $ex) {
				$form->addError($ex->getMessage());
			}
		} else {
			$form->addError("Kupón nelze vložit, neplatný kód.");
		}
	}

}
