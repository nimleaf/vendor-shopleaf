<?php

namespace Nimleaf\Shopleaf\AdminModule\Presenters;

use App\AdminModule\Form\OrderProductSoldAddFormFactory;
use App\AdminModule\Form\ProductSoldEditFormFactory;
use App\AdminModule\Form\OrderShippingTrackingCodeFormFactory;
use App\AdminModule\Form\OrderStatusFormFactory;
use App\AdminModule\Form\OrderVoucherSoldAddFormFactory;
use App\Model\Doc\Order;
use App\Model\Doc\Product;
use App\Model\Doc\ProductSold;
use App\Model\Doc\VoucherSold;
use App\Model\Email\EmailSender;
use App\Model\Invoice\Order as InvoiceOrder;
use Exception;

trait TOrderPresenter {

	/** @var Order */
	protected $order;

	/** @persistent form step */
	public $formStep = 1;

	/** @persistent id produktu */
	public $productSoldProductId;

	public function handleDelete($id = NULL) {
		$order = Order::load($this->em, $id);
		if ($order) {
			$order->delete($this->em);
			$this->flashMessage(_("Objednávka byla smazána."));
		}
		$this->redirect('this');
	}

	public function handleDeleteProductSold($id, $productSoldId) {
		$productSold = ProductSold::load($this->em, $productSoldId);
		if ($productSold) {
			$this->order->unsetProductSold($productSold);
			$this->order->save($this->em);
			$productSold->delete($this->em);
			$this->flashMessage(_("Zboží bylo odebráno."));
		}
		$this->redirect('this', $id, NULL); //$productSoldId = NULL
	}
	
	public function handleDeleteVoucherSold($id, $voucherSoldId) {
		$voucherSold = VoucherSold::load($this->em, $voucherSoldId);
		if ($voucherSold) {
			$this->order->unsetVoucherSold($voucherSold);
			$this->order->save($this->em);
			$voucherSold->delete($this->em);
			$this->flashMessage(_("Kupón byl odebrán."));
		}
		$this->redirect('this');
	}

	public function handleInvoice() {
		$invoice = new InvoiceOrder($this->em, $this->order);
		$invoice->pdf();
	}

	public function renderDefault($order = NULL, $by = NULL) {
		$this->template->orders = Order::getAll($this->em, $order, $by);
		$this->template->order = $order;
		$this->template->by = $by;
	}

	public function actionView($id, $productSoldId = NULL) {
		$this->order = Order::load($this->em, $id);
		if (!$this->order) {
			$this->flashMessage(_("Objednávka nenalezena."), 'error');
			$this->redirect('default');
		}
	}

	public function renderView() {
		$this->template->order = $this->order;
		$this->template->formStep = $this->formStep;
		$this->template->formStepProductSoldProduct = Product::load($this->em, $this->productSoldProductId);
	}

	protected function createComponentOrderProductSoldAddForm() {
		$f = new OrderProductSoldAddFormFactory($this->em, $this->order, $this->formStep, $this->productSoldProductId);
		if ($this->formStep == 1) {
			$form = $f->createStep1();
			$form->onSuccess[] = function () {
				$this->redirect('this');
			};
		} else if ($this->formStep == 2) {
			$form = $f->createStep2();
			$form->onSuccess[] = function () {
				$this->flashMessage(_("Zboží bylo přidáno k objednávce."));
				$this->redirect('this');
			};
		}
		$form->onError[] = function ($form) {
			foreach ($form->errors as $error) {
				$this->flashMessage(_($error), 'error');
			}
			$this->redirect('this');
		};
		return $form;
	}

	protected function createComponentProductSoldEditForm() {
		$f = new ProductSoldEditFormFactory($this->em, $this->presenter);
		$form = $f->create();
		//onSuccess je ve formuláři (multiplier)
		return $form;
	}

	protected function createComponentShippingTrackingCodeForm() {
		$f = new OrderShippingTrackingCodeFormFactory($this->em, $this->order);
		$form = $f->create();
		$form->onSuccess[] = function () {
			$this->flashMessage(_("Sledovací kód byl změněn."));
			$this->redirect('this');
		};
		return $form;
	}

	protected function createComponentStatusForm() {
		$f = new OrderStatusFormFactory($this->em, $this->order);
		$form = $f->create();
		$form->onSuccess[] = function ($form, $values) {
			$this->flashMessage(_("Status byl změněn."));

			//odeslat e-mail
			if ($values->sendEmail === TRUE) {
				try {
					$email = new EmailSender($this->em, $this->mailer, $this->order, $this->order->status);
					$email->send();
				} catch (Exception $ex) {
					$this->flashMessage(_("E-mail nebyl odeslán, chyba: ") . $ex->getMessage(), 'error');
				}
			}

			$this->redirect('this');
		};
		return $form;
	}
	
	
	protected function createComponentOrderVoucherSoldAddForm() {
		$f = new OrderVoucherSoldAddFormFactory($this->em, $this->order);
		$form = $f->create();
		$form->onSuccess[] = function () {
			$this->flashMessage(_("Kupón byl přidaný k objednávce."));
			$this->redirect('this');
		};
		$form->onError[] = function ($form) {
			foreach ($form->errors as $error) {
				$this->flashMessage(_($error), 'error');
			}
			$this->redirect('this');
		};
		return $form;
	}

}
