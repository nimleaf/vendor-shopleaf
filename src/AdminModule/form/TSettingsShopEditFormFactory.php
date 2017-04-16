<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Settings;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TSettingsShopEditFormFactory {

	public function __construct(EntityManager $em) {
		parent::__construct($em);
	}

	protected function getDefaults() {
		$settings = Settings::getSettings($this->em);
		$settingsShop = $settings->shop;

		$defaults = [
			'apiHeureka' => $settingsShop->apiHeureka,
			'productsOnPage' => $settingsShop->productsOnPage,
		];

		if ($settingsShop->termsAndConditions !== NULL) {
			$defaults['termsAndConditions'] = $settingsShop->termsAndConditions->id;
		}

		return $defaults;
	}

	public function formSucceeded(Form $form, $values) {
		$settings = Settings::getSettings($this->em);
		$settingsShop = $settings->shop;
		$this->applyValues($settingsShop, $values);
	}
}
