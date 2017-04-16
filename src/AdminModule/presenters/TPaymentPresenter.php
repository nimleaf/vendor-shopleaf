<?php

namespace Nimleaf\Shopleaf\AdminModule\Presenters;

use App\Model\Doc\Payment;
use App\AdminModule\Form\PaymentCreateFormFactory;
use App\AdminModule\Form\PaymentEditFormFactory;

trait TPaymentPresenter {

	/** @var Payment */
	protected $payment;

	public function handleDelete($id) {
		$payment = Payment::load($this->em, $id);
		if ($payment) {
			$payment->delete($this->em);
			$this->flashMessage(_("Možnost platby byla smazána."));
		}
	}
	
	public function handleDown($id) {
		$this->payment->down($this->em);
		$this->redirect('this');
	}

	public function handleUp($id) {
		$this->payment->up($this->em);
		$this->redirect('this');
	}

	public function actionDefault($id) {
		if ($id !== NULL) {
			$this->payment = Payment::load($this->em, $id);
		}
	}

	public function renderDefault() {
		$this->template->payments = Payment::getAll($this->em);
	}

	public function actionEdit($id) {
		$this->payment = Payment::load($this->em, $id);
		if (!$this->payment) {
			$this->flashMessage(_("Možnost platby nenalezena."), 'error');
			$this->redirect('default');
		}
	}

	protected function createComponentCreateForm() {
		$f = new PaymentCreateFormFactory($this->em);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Možnost platby byla vytvořena."));
			$this->redirect('default');
		};
		return $form;
	}

	protected function createComponentEditForm() {
		$f = new PaymentEditFormFactory($this->em, $this->payment);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Možnost platby byla upravena."));
			$this->redirect('default');
		};
		return $form;
	}

}
