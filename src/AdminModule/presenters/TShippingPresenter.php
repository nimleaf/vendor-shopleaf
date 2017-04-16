<?php

namespace Nimleaf\Shopleaf\AdminModule\Presenters;

use App\Model\Doc\Shipping;
use App\AdminModule\Form\ShippingCreateFormFactory;
use App\AdminModule\Form\ShippingEditFormFactory;

trait TShippingPresenter {

	/** @var Shipping */
	protected $shipping;

	public function handleDelete($id) {
		$shipping = Shipping::load($this->em, $id);
		if ($shipping) {
			$shipping->delete($this->em);
			$this->flashMessage(_("Způsob dopravy byl smazán."));
		}
	}

	public function handleDown($id) {
		$this->shipping->down($this->em);
		$this->redirect('this');
	}

	public function handleUp($id) {
		$this->shipping->up($this->em);
		$this->redirect('this');
	}

	public function actionDefault($id) {
		if ($id !== NULL) {
			$this->shipping = Shipping::load($this->em, $id);
		}
	}

	public function renderDefault() {
		$this->template->shippings = Shipping::getAll($this->em, 'sort');
	}

	public function actionEdit($id) {
		$this->shipping = Shipping::load($this->em, $id);
		$this->template->shipping = $this->shipping;
		if (!$this->shipping) {
			$this->flashMessage(_("Způsob dopravy nenalezen."), 'error');
			$this->redirect('default');
		}
	}

	protected function createComponentCreateForm() {
		$f = new ShippingCreateFormFactory($this->em);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Způsob dopravy byl vytvořen."));
			$this->redirect('default');
		};
		return $form;
	}

	protected function createComponentEditForm() {
		$f = new ShippingEditFormFactory($this->em, $this->shipping);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Způsob dopravy byl upraven."));
			$this->redirect('default');
		};
		return $form;
	}

}
