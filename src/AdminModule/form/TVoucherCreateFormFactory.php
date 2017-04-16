<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Voucher;
use App\Model\Doc\DuplicateVoucherException;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TVoucherCreateFormFactory {

	public function __construct(EntityManager $em) {
		parent::__construct($em);
	}

	protected function applyValues(Voucher $voucher, $values) {
		$voucher->name = $values->name;
		$voucher->maximumUsage = $values->maximumUsage;
		$voucher->dateFrom = new \DateTime($values->dateFrom);
		$voucher->dateTo = new \DateTime($values->dateTo);
		$voucher->discount = $values->discount;
		$voucher->discountType = $values->discountType;
		$voucher->minimalPrice = $values->minimalPrice;
		$voucher->save($this->em);
	}

	public function create() {
		$discountType = Voucher::$DISCOUNT_TYPE;

		$form = parent::create();

		$form->addText('name', _("název"))
				->setRequired();

		$form->addText('code', _("kód"))
				->setRequired();

		$form->addText('maximumUsage', _("počet použití"))
				->setType('number')
				->setAttribute('class', "form-control")
				->setAttribute('placeholder', _("0 = neomezeně"))
				->setRequired();

		$form->addText('dateFrom', _("platné od"))
				->setAttribute('class', "date form-control");

		$form->addText('dateTo', _("platné do"))
				->setAttribute('class', "date form-control");

		$form->addText('discount', _("sleva"))
				->setType('number')
				->setAttribute('class', "form-control");

		$form->addSelect('discountType', _("typ slevy"), $discountType)
				->setAttribute('class', "form-control");

		$form->addText('minimalPrice', _("minimální cena objednávky"))
				->setType('number')
				->setAttribute('class', "form-control");

		$form->addSubmit('send', _("Uložit"));

		$form->setDefaults($this->getDefaults());
		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		try {
			$voucher = new Voucher($this->em, $values->code);
			$this->applyValues($voucher, $values);
		} catch (DuplicateVoucherException $ex) {
			$form->addError($ex->getMessage());
		}
	}

}
