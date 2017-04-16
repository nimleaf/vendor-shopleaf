<?php

namespace Nimleaf\Shopleaf\AdminModule\Presenters;

use App\AdminModule\Form\ProductCategoryCreateFormFactory;
use App\AdminModule\Form\ProductCategoryEditFormFactory;
use App\Model\Doc\ProductCategory;

trait TProductCategoryPresenter {

	/** @var ProductCategory */
	protected $category;

	public function handleDelete($id) {
		$category = ProductCategory::load($this->em, $id);
		if ($category) {
			$category->delete($this->em);
			$this->flashMessage(_("Kategorie byla smazána."));
			$this->redirect('this');
		}
	}

	public function handleDown($id) {
		$this->category->down($this->em);
		$this->redirect('this');
	}

	public function handleUp($id) {
		$this->category->up($this->em);
		$this->redirect('this');
	}

	public function actionDefault($id = NULL) {
		if ($id !== NULL) {
			$this->category = ProductCategory::load($this->em, $id);
		}
	}

	public function renderDefault() {
		$this->template->categories = ProductCategory::getAll($this->em, 'sort', 'ASC');
	}

	public function actionEdit($id) {
		$this->category = ProductCategory::load($this->em, $id);
	}

	protected function createComponentCreateForm() {
		$f = new ProductCategoryCreateFormFactory($this->em);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Kategorie byla vytvořena."));
			$this->redirect('default');
		};
		return $form;
	}

	protected function createComponentEditForm() {
		$f = new ProductCategoryEditFormFactory($this->em, $this->category);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Kategorie byla upravena."));
			$this->redirect('default');
		};
		return $form;
	}

}
