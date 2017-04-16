<?php

namespace Nimleaf\Shopleaf\AdminModule\Presenters;

use App\AdminModule\Form\SettingsShopEditFormFactory;

trait TSettingsShopPresenter {

		protected function createComponentEditForm() {
		$f = new SettingsShopEditFormFactory($this->em);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Nastavení bylo upraveno."));
			$this->redirect('default');
		};
		return $form;
	}

}
