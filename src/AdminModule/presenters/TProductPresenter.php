<?php

namespace Nimleaf\Shopleaf\AdminModule\Presenters;

use App\AdminModule\Form\ProductCorrectionCreateFormFactory;
use App\AdminModule\Form\ProductCorrectionEditFormFactory;
use App\AdminModule\Form\ProductCreateFormFactory;
use App\AdminModule\Form\ProductEditFormFactory;
use App\AdminModule\Form\ProductPurchaseCreateFormFactory;
use App\AdminModule\Form\ProductPurchaseEditFormFactory;
use App\Model\Doc\Product;
use App\Model\Doc\ProductCorrection;
use App\Model\Doc\ProductPurchase;
use Nimleaf\Aukro\MyAukro;
use \SoapFault;

trait TProductPresenter {

	/** @var ProductCorrection */
	protected $correction;

	/** @var Product */
	protected $product;

	/** @var ProductPurchase */
	protected $purchase;

	/** @persistent */
	public $productId;

	public function handleActive($id) {
		$product = Product::load($this->em, $id);
		if ($product) {
			$product->active = TRUE;
			$product->save($this->em);
		}
		$this->redirect('this');
	}

	public function handleAukroCheckAdd($id) {
		$product = Product::load($this->em, $id);
		if ($product) {
			try {
				$aukro = new MyAukro($this->em);
				$newAuction = $aukro->doCheckNewAuctionExt($this->product);
				$this->flashMessage(_(
								"Prezentace na aukro.cz je připravena.<br>"
								. "Budou účtovány následující poplatky:<br><br>")
						. _($newAuction['item-price-desc'] . "<br><br>")
						. _("Celkem:") . " " . $newAuction['item-price']);
			} catch (SoapFault $ex) {
				$this->flashMessage(_("Prezentace na aukro.cz se nezdařila.<br>" . $ex->getMessage()), 'warning');
			}
		}
		$this->redirect('this');
	}

	public function handleCorrectionDelete($correctionId) {
		$correction = ProductCorrection::load($this->em, $correctionId);
		if ($correction) {
			$correction->delete($this->em);
			$this->flashMessage(_("Korekce byla smazána."));
		}
		$this->redirect('this');
	}

	public function handleDelete($id) {
		$product = Product::load($this->em, $id);
		if ($product) {
			$product->delete($this->em);
			$this->flashMessage(_("Produkt byl smazán."));
		}
		$this->redirect('this');
	}

	public function handleDown($id) {
		$this->product->down($this->em);
		$this->redirect('this');
	}

	public function handlePurchaseDelete($purchaseId) {
		$purchase = ProductPurchase::load($this->em, $purchaseId);
		if ($purchase) {
			$purchase->delete($this->em);
			$this->flashMessage(_("Nákup byl smazán."));
		}
		$this->redirect('this');
	}

	public function handleUp($id) {
		$this->product->up($this->em);
		$this->redirect('this');
	}

	public function actionCorrectionCreate($id) {
		$this->productId = NULL;
		$this->product = Product::load($this->em, $id);
		if (!$this->product) {
			$this->flashMessage(_("Produkt nenalezen."), 'error');
			$this->redirect('default');
		}
	}

	public function actionCorrectionEdit($id, $correctionId) {
		$this->product = Product::load($this->em, $id);
		if (!$this->product) {
			$this->flashMessage(_("Produkt nenalezen."), 'error');
			$this->redirect('default');
		}
		$this->correction = ProductCorrection::load($this->em, $correctionId);
		if (!$this->correction) {
			$this->flashMessage(_("Korekce nenalezena."), 'error');
			$this->redirect('edit', $this->product->id);
		}
	}

	public function actionDefault($id, $order = NULL, $by = NULL) {
		if ($id !== NULL) {
			$this->product = Product::load($this->em, $id);
		}
	}

	public function renderDefault($id, $order = NULL, $by = NULL) {
		$products = Product::getAll($this->em, $order, $by);
		$this->template->products = $products;
		$this->template->order = $order;
		$this->template->by = $by;
	}

	public function actionEdit($id) {
		$this->product = Product::load($this->em, $id);
		if (!$this->product) {
			$this->flashMessage(_("Produkt nenalezen."), 'error');
			$this->redirect('default');
		}
		if ($this->product->getAukro()->localId) {
			$aukro = new MyAukro($this->em);
			$verify = $aukro->doVerifyItem($this->product->getAukro()->localId);
			//todo
		}
	}

	public function renderEdit() {
		$this->template->product = $this->product;
	}

	public function actionPurchaseCreate($id) {
		$this->productId = NULL;
		$this->product = Product::load($this->em, $id);
		if (!$this->product) {
			$this->flashMessage(_("Produkt nenalezen."), 'error');
			$this->redirect('default');
		}
	}

	public function actionPurchaseEdit($id, $purchaseId) {
		$this->product = Product::load($this->em, $id);
		if (!$this->product) {
			$this->flashMessage(_("Produkt nenalezen."), 'error');
			$this->redirect('default');
		}
		$this->purchase = ProductPurchase::load($this->em, $purchaseId);
		if (!$this->purchase) {
			$this->flashMessage(_("Nákup nenalezen."), 'error');
			$this->redirect('edit', $this->product->id);
		}
	}

	protected function createComponentCorrectionCreateForm() {
		$f = new ProductCorrectionCreateFormFactory($this->em, $this->product);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Korekce byla přidána."));
			$this->redirect('edit', $this->product->id);
		};
		return $form;
	}

	protected function createComponentCorrectionEditForm() {
		$f = new ProductCorrectionEditFormFactory($this->em, $this->product, $this->correction);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Korekce byla upravena."));
			$this->redirect('edit', $this->product->id);
		};
		return $form;
	}

	protected function createComponentCreateForm() {
		$f = new ProductCreateFormFactory($this->em, $this->getLoggedUser(), $this->productId);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Produkt byl vytvořen."));
			$this->redirect('purchaseCreate', $this->productId);
		};
		return $form;
	}

	protected function createComponentEditForm() {
		$f = new ProductEditFormFactory($this->em, $this->getLoggedUser(), $this->product);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Produkt byl upraven."));
			$this->redirect('default');
		};
		return $form;
	}

	protected function createComponentPurchaseCreateForm() {
		$f = new ProductPurchaseCreateFormFactory($this->em, $this->product);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Nákup byl přidán."));
			$this->redirect('edit', $this->product->id);
		};
		return $form;
	}

	protected function createComponentPurchaseEditForm() {
		$f = new ProductPurchaseEditFormFactory($this->em, $this->product, $this->purchase);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Nákup byl upraven."));
			$this->redirect('edit', $this->product->id);
		};
		return $form;
	}

}
