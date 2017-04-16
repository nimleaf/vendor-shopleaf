<?php

namespace Nimleaf\Shopleaf\FrontModule\Presenters;

use App\Model\Doc\ProductCategory;
use App\Model\Doc\Settings;
use App\FrontModule\Control\Pagination;

trait TProductCategoryPresenter {

	/** @var ProductCategory */
	protected $category;
	
	/** @persistent */
	protected $page = 1;
	
	/** @var int */
	protected $total;

	public function actionDefault($id, $p = 0) {
		$this->category = ProductCategory::load($this->em, $id);
		if (!$this->category) {
			$this->redirect('Homepage:');
		}
		$this->menuTab = 'category';
		$this->menuItemId = $this->category->id;
	}

	public function renderDefault($id, $page = 1, $total = NULL) {
		$settings = Settings::getSettings($this->em);
		$onlyAvaible = TRUE;
		$order = 'sort';
		$by = NULL;
		$limit = $settings->shop->productsOnPage;
		$offset = ($page * $limit) - $limit;
		
		$products = $this->category->getProducts($this->em, $onlyAvaible, $order, $by, $limit, $offset);
		
		$this->page = $page;
		if ($total == NULL) {
			$this->total = count($this->category->getProducts($this->em, $onlyAvaible));
		} else {
			$this->total = $total;
		}

		$this->template->category = $this->category;
		$this->template->products = $products;
	}
	
	protected function createComponentPagination($name) {
		$settings = Settings::getSettings($this->em);
		$limit = $settings->shop->productsOnPage;
		return new Pagination($this->em, $this, $name, $limit, $this->page, $this->total);
	}

}
