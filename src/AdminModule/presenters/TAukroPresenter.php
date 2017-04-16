<?php

namespace Nimleaf\Shopleaf\AdminModule\Presenters;

use \SoapFault;
use App\AdminModule\Form\AukroEditFormFactory;
use Nimleaf\Shopleaf\Vendor\Aukro\MyAukro;

trait TAukroPresenter {

	public function renderDefault() {
		$aukro = new MyAukro($this->em);
		try {
			$aukro->doLoginEnc();
			$connect = TRUE;
		} catch (SoapFault $ex) {
			$connect = FALSE;
			$message = $ex->getMessage();
			$this->template->message = $message;
		}

		if ($connect === TRUE) {
			$myData = $aukro->doGetMyData();
			$userData = (array) $myData['user-data'];
			$myBilling = $aukro->doMyBilling();
			
			$this->template->userData = $userData;
			$this->template->myBilling = $myBilling;
		}

		$this->template->connect = $connect;
	}

	protected function createComponentEditForm() {
		$f = new AukroEditFormFactory($this->em);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Nastavení bylo upraveno."));
			$this->redirect('this');
		};
		return $form;
	}

}
