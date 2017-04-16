<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Voucher;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TVoucherEditFormFactory {

	protected $voucher;

	public function __construct(EntityManager $em, Voucher $voucher) {
		$this->voucher = $voucher;
		parent::__construct($em);
	}

	protected function getDefaults() {
		$defaults = [
			'name' => $this->voucher->name,
			'code' => $this->voucher->code,
			'maximumUsage' => $this->voucher->maximumUsage,
			'dateFrom' => $this->voucher->dateFrom->format('d.m.Y'),
			'dateTo' => $this->voucher->dateTo->format('d.m.Y'),
			'discount' => $this->voucher->discount,
			'discountType' => $this->voucher->discountType,
			'minimalPrice' => $this->voucher->minimalPrice,
		];

		return $defaults;
	}

	public function formSucceeded(Form $form, $values) {
		$voucher = $this->voucher;
		$this->applyValues($voucher, $values);
	}

}
