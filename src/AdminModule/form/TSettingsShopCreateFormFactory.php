<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Article;
use App\Model\Doc\SettingsShop;

trait TSettingsShopCreateFormFactory {

	protected function applyValues(SettingsShop $settingsShop, $values) {
		
		$termsAndConditions = Article::load($this->em, $values->termsAndConditions);
		$settingsShop->setTermsAndConditions($termsAndConditions);

		$settingsShop->apiHeureka = $values->apiHeureka;
		$settingsShop->productsOnPage = $values->productsOnPage;

		$settingsShop->save($this->em);
	}

	public function create() {
		$termsAndConditions = Article::getAllToArray($this->em);

		$form = parent::create();

		$form->addGroup(_("nastavení obchodu"));

		$form->addText('apiHeureka', _("API Heureka"));
		
		$form->addSelect('termsAndConditions', _("obchodní podmínky"), $termsAndConditions)
				->setAttribute('class', "form-control");
		
		$form->addText('productsOnPage', _("Počet produktů na stránku"))
				->setType('number')
				->setRequired()
				->setAttribute('class', "form-control");

		$form->addGroup();

		$form->addSubmit('send', _("Uložit"));

		$form->setDefaults($this->getDefaults());

		return $form;
	}


}
