<?php

namespace Nimleaf\Shopleaf\FrontModule\Control;

use App\Model\Doc\ProductCategory as ORMCategory;
use App\Model\Doc\ProductTag as ORMTag;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Presenter;

trait TProductMenu {

	/** @var string */
	protected $currentItemId;

	/** @var string */
	protected $tab;

	public function __construct(EntityManager $em, Presenter $presenter, $name, $tab = NULL, $currentItemId = NULL) {
		parent::__construct($em, $presenter, $name);

		$this->currentItemId = $currentItemId;

		if ($tab == NULL || $tab == 'category') {
			$this->tab = 'category';
		} else {
			$this->tab = 'tag';
		}
	}

	public function render($args = NULL) {
		$all = ORMCategory::getAll($this->em, 'sort', 'asc');
		$categories = [];
		foreach ($all as $category) {
			if ($category->getProducts($this->em, TRUE)) {
				$categories[] = $category;
			}
		}

		$all2 = ORMTag::getAll($this->em, 'name', 'asc');
		$tags = [];
		foreach ($all2 as $tag) {
			if ($tag->getProducts($this->em, TRUE)) {
				$tags[] = $tag;
			}
		}

		$this->template->setFile($this->templateFileName);
		$this->template->categories = $categories;
		$this->template->tags = $tags;
		$this->template->currentItemId = $this->currentItemId;
		$this->template->menuTab = $this->tab;
		$this->template->render();
	}

}
