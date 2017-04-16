<?php

namespace Nimleaf\Shopleaf\FrontModule\Presenters;

use App\Model\Doc\ProductTag;
use App\Model\Doc\Settings;
use App\FrontModule\Control\Pagination;

trait TProductTagPresenter {

	/** @var ProductTag */
	protected $tag;
	
	/** @persistent */
	protected $page = 1;
	
	/** @var int */
	protected $total;

	public function actionDefault($id) {
		$this->tag = ProductTag::load($this->em, $id);
		if (!$this->tag) {
			$this->redirect('Homepage:');
		}
		$this->menuTab = 'tag';
		$this->menuItemId = $this->tag->id;
	}

	public function renderDefault($id, $page = 1, $total = NULL) {
		$settings = Settings::getSettings($this->em);
		$onlyAvaible = TRUE;
		$order = 'sort';
		$by = NULL;
		$limit = $settings->shop->productsOnPage;
		$offset = ($page * $limit) - $limit;
		
		$products = $this->tag->getProducts($this->em, $onlyAvaible, $order, $by, $limit, $offset);
		
		$this->page = $page;
		if ($total == NULL) {
			$this->total = count($this->tag->getProducts($this->em, $onlyAvaible));
		} else {
			$this->total = $total;
		}

		$this->template->tag = $this->tag;
		$this->template->products = $products;
	}
	
	protected function createComponentPagination($name) {
		$settings = Settings::getSettings($this->em);
		$limit = $settings->shop->productsOnPage;
		return new Pagination($this->em, $this, $name, $limit, $this->page, $this->total);
	}

}
