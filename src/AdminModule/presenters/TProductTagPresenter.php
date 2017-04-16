<?php

namespace Nimleaf\Shopleaf\AdminModule\Presenters;

use App\AdminModule\Form\ProductTagCreateFormFactory;
use App\AdminModule\Form\ProductTagEditFormFactory;
use App\Model\Doc\ProductTag;

trait TProductTagPresenter {

	/** @var ProductTag */
	protected $productTag;

	public function handleDelete($id) {
		$productTag = ProductTag::load($this->em, $id);
		if ($productTag) {
			$productTag->delete($this->em);
			$this->flashMessage(_("Tag byl smazán."));
			$this->redirect('this');
		}
	}
	
	public function actionDefault($id = NULL) {
		if ($id !== NULL) {
			$this->productTag = ProductTag::load($this->em, $id);
		}
	}

	public function renderDefault() {
		$this->template->productTags = ProductTag::getAll($this->em, 'name', 'asc');
	}
	
	public function actionEdit($id) {
		$this->productTag = ProductTag::load($this->em, $id);
	}

	protected function createComponentCreateForm() {
		$f = new ProductTagCreateFormFactory($this->em);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Tag byl vytvořen."));
			$this->redirect('default');
		};
		return $form;
	}

	protected function createComponentEditForm() {
		$f = new ProductTagEditFormFactory($this->em, $this->productTag);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Tag byl upraven."));
			$this->redirect('default');
		};
		return $form;
	}
}
