<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Payment;
use App\Model\Doc\Shipping;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;
use Nette\Utils\Image;
use Nimleaf\Strings;

trait TShippingCreateFormFactory {

	public function __construct(EntityManager $em) {
		parent::__construct($em);
	}

	protected function applyValues(Shipping $shipping, $values) {
		$shipping->name = $values->name;
		$shipping->description = $values->description;
		$shipping->deliveryTime = $values->deliveryTime;
		$shipping->price = $values->price;
		$shipping->trackingCodeUrl = Strings::emptyToNull($values->trackingCodeUrl);
		
		$shipping->resetPaymentOption();

		foreach ($values->paymentOption as $paymentId) {
			$paymentOption = Payment::load($this->em, $paymentId);
			if ($paymentOption) {
				$shipping->paymentOption[] = $paymentOption;
			}
		}
		
		if ($values->icon->size > 0) {
			$image = Image::fromFile($values->icon);
			//zpracování obrázku
			$image->resize(40, 40, Image::FIT);
			$image->save(ROOT_DIR . Shipping::$PATH_ICON_FOLDER . $values->icon->name);
			
			$shipping->icon = $values->icon->name;
		}
		
		$shipping->save($this->em);
	}

	public function create() {
		$paymentOption = Payment::getAllToArray($this->em);

		$form = parent::create();

		$form->addGroup();
		
		$form->addUpload('icon', _("obrázek"))
				->addCondition(Form::FILLED)
				->addRule(Form::IMAGE, _("Nepovolený formát obrázku."));

		$form->addText('name', _("název"))
				->setRequired();
		
		$form->addText('trackingCodeUrl', _("URL pro sledování zásilky"))
				->setOption('description', _("namísto %%%%% bude doplněno číslo zásilky"))
				->setAttribute('placeholder', "https://www.postaonline.cz/trackandtrace/-/zasilka/cislo?parcelNumbers=%%%%%");
		
		$form->addText('description', _("popis"));
		
		$form->addText('deliveryTime', _("doba dodání"));

		$form->addText('price', _("cena"))
				->setRequired();

		$form->addGroup(_("povolené možnosti platby"));

		$form->addCheckboxList('paymentOption', NULL, $paymentOption);

		$form->addSubmit('send', _("Uložit"));

		$form->setDefaults($this->getDefaults());

		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		$shipping = new Shipping;
		$this->applyValues($shipping, $values);
	}

}
