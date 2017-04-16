<?php

namespace Nimleaf\Shopleaf\FrontModule\Presenters;

use App\FrontModule\Control\Basket;
use App\FrontModule\Exception\PageNotFound;
use App\FrontModule\Form\OrderFormFactory;
use App\FrontModule\Form\VoucherAddFormFactory;
use App\Model\Doc\Order;
use App\Model\Doc\Product;
use App\Model\Doc\Settings;
use App\Model\Email\EmailSender;
use \Heureka;

trait TBasketPresenter {

	/** @persistent */
	public $orderId = NULL;

	public function handleDelete($id) {
		$basket = $this->session->getSection('basket');
		unset($basket->$id);
		$this->redirect('this');
	}

	public function handleDeleteVoucher($voucherId) {
		$vouchers = $this->session->getSection('vouchers');
		unset($vouchers->$voucherId);
		$this->redirect('this');
	}

	/**
	 * Přidá ($piece) kusů do košíku
	 * @param int $id ID prouktu
	 * @param int $piece
	 * @todo předělat na handle po nastavení multiple formu na výpise produktů
	 */
	public function actionAdd($id, $piece = 1) {
		$product = Product::load($this->em, $id);
		$basket = $this->session->getSection('basket');
		$want = $basket->$id + $piece;
		$avaible = $product->getPieces();
		if ($want > $avaible) {
			$this->flashMessage(_("Není možné přidat další kusy"), 'error');
		} else {
			$basket->$id += $piece;
			if ($basket->$id < 1) {
				unset($basket->$id);
			}
		}
		$this->redirect('default');
	}

	public function actionDefault() {
		if (!$this->session->hasSection('basket')) {
			$this->redirect('empty');
		}
	}

	public function renderDefault() {
		$products = [];
		foreach ($this->session->getSection('basket') as $productId => $productPiece) {
			$product = Product::load($this->em, $productId);
			if ($product) {
				$products[] = [
					'product' => $product,
					'piece' => $productPiece,
				];
			}
		}

		$vouchers = $this->session->getSection('vouchers');

		$basket = $this->presenter->session->getSection('basket');
		list($sumPiece, $sumPrice) = Basket::sumBasket($this->em, $basket, $vouchers);

		$this->template->products = $products;
		$this->template->sumPiece = $sumPiece;
		$this->template->sumPrice = $sumPrice;
		$this->template->vouchers = $vouchers;
	}

	public function actionFinal($id, $hash) {
		$order = Order::load($this->em, $id);
		if (!$order || $hash !== md5($order->getContact()->email)) {
			throw new PageNotFound;
		}

		$this->orderId = NULL;
		$this->session->getSection('basket')->remove();
		$this->session->getSection('vouchers')->remove();
	}

	public function renderFinal($id, $hash) {
		$this->template->order = Order::load($this->em, $id);
	}

	protected function createComponentOrderForm() {
		$termsAndConditionLink = $this->link('Article:view', Settings::getSettings($this->em)->shop->termsAndConditions->id);
		$basket = $this->session->getSection('basket');
		$vouchers = $this->session->getSection('vouchers');
		$f = new OrderFormFactory($this->em, $this->orderId, $termsAndConditionLink, $basket, $vouchers, $this->getLoggedUser());
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$order = Order::load($this->em, $this->orderId);
			$email = new EmailSender($this->em, $this->mailer, $order, 'new-order');
			$email->send();

			$this->automatic($order);

			$hash = md5($order->getContact()->email);
			$this->redirect('final', $this->orderId, $hash);
		};
		$form->onError[] = function($form) {
			foreach ($form->errors as $error) {
				$this->flashMessage(_($error), 'error');
				$this->redirect('this');
			}
		};
		return $form;
	}

	protected function createComponentVoucherAddForm() {
		$vouchers = $this->session->getSection('vouchers');
		$basket = $this->session->getSection('basket');
		list($sumPiece, $sumPrice) = Basket::sumProducts($this->em, $basket);
		$f = new VoucherAddFormFactory($this->em, $vouchers, $sumPrice, $this->getLoggedUser());
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Kupón byl přidán."), 'success');
			$this->redirect('this');
		};
		$form->onError[] = function($form) {
			foreach ($form->errors as $error) {
				$this->flashMessage(_($error), 'error');
				$this->redirect('this');
			}
		};
		return $form;
	}

	protected function automatic(Order $order) {
		//typ platby je platba předem
		if ($order->payment->paymentType == 'in-advance') {
			$order->setStatus('waiting-for-payment');
			$order->save($this->em);
			$email = new EmailSender($this->em, $this->mailer, $order, 'waiting-for-payment');
			$email->send();
		}

		//odeslání na heuréku
		$this->heureka($order);
	}

	/**
	 * Propojení se službou Heuréka - Ověřeno zákazníky
	 * @param Order $order
	 */
	protected function heureka(Order $order) {
		$options = [
			'service' => Heureka\ShopCertification::HEUREKA_CZ,
		];
		try {
			$heureka = new Heureka\ShopCertification(Settings::getSettings($this->em)->apiHeureka, $options);
			$heureka->setEmail($order->contact->email);
			$heureka->setOrderId($order->id);
			foreach ($order->productSold as $product) {
				$heureka->addProductItemId($product->code);
			}
			$heureka->logOrder();
		} catch (Heureka\ShopCertification\Exception $ex) {
			//do nothing
		}
	}

}
