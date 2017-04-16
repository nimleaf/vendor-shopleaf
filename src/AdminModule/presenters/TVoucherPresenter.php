<?php

namespace Nimleaf\Shopleaf\AdminModule\Presenters;

use App\Model\Doc\Voucher;
use App\AdminModule\Form\VoucherCreateFormFactory;
use App\AdminModule\Form\VoucherEditFormFactory;


trait TVoucherPresenter {

	/** @var Voucher */
	protected $voucher;

	public function handleDelete($id) {
		$voucher = Voucher::load($this->em, $id);
		if ($voucher) {
			$voucher->delete($this->em);
			$this->flashMessage(_("Kupón byl smazán."));
		}
	}
	
	public function renderDefault($order = NULL, $by = NULL) {
		$this->template->vouchers = Voucher::getAll($this->em, $order, $by);
		$this->template->order = $order;
		$this->template->by = $by;
	}

	public function actionEdit($id) {
		$this->voucher = Voucher::load($this->em, $id);
		if (!$this->voucher) {
			$this->flashMessage(_("Kupón nenalezen."), 'error');
			$this->redirect('default');
		}
	}

	public function renderEdit() {
		$this->template->voucher = $this->voucher;
	}

	protected function createComponentCreateForm() {
		$f = new VoucherCreateFormFactory($this->em);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Kupón byl vytvořen."));
			$this->redirect('default');
		};
		$form->onError[] = function ($form) {
			foreach ($form->getErrors() as $error) {
				$this->flashMessage(_($error), 'error');
			}
			$this->redirect('this');
		};
		return $form;
	}

	protected function createComponentEditForm() {
		$f = new VoucherEditFormFactory($this->em, $this->voucher);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Kupón byl upraven."));
			$this->redirect('default');
		};
		$form->onError[] = function ($form) {
			foreach ($form->getErrors() as $error) {
				$this->flashMessage(_($error), 'error');
			}
			$this->redirect('this');
		};
		return $form;
	}

}
