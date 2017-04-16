<?php

namespace Nimleaf\Shopleaf\FrontModule\Control;

use App\Model\Doc\Product;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Presenter;

trait TBasket {

	public function __construct(EntityManager $em, Presenter $presenter, $name) {
		parent::__construct($em, $presenter, $name);
	}

	/**
	 * @param EntityManager $em
	 * @param array $basket obsah košíku ['productId' => 'piece', ...]
	 * @return [summaryPiece, summaryPrice]
	 */
	public static function sumBasket(EntityManager $em, $basket, $vouchers) {
		list($productPiece, $productPrice) = self::sumProducts($em, $basket);
		list($voucherAmmount, $voucherPercentage) = self::sumVouchers($vouchers);

		$price = ($productPrice - $voucherAmmount) * ((100 - $voucherPercentage) / 100);

		return [$productPiece, $price];
	}

	/**
	 * @param EntityManager $em
	 * @param array $basket obsah košíku ['productId' => 'piece', ...]
	 * @return [summaryPiece, summaryPrice]
	 */
	public static function sumProducts(EntityManager $em, $basket) {
		$piece = 0;
		$price = 0;

		foreach ($basket as $productId => $productPiece) {
			$product = Product::load($em, $productId);
			if ($product) {
				$piece += $productPiece;
				$price += $productPiece * $product->getFinalPrice();
			}
		}
		return [$piece, $price];
	}

	/**
	 * @param array $vouchers obsah košíku ['voucherId' => Voucher]
	 * @return summaryPrice
	 */
	public static function sumVouchers($vouchers) {
		$ammount = 0;
		$percentage = 0;

		foreach ($vouchers as $voucher) {
			if ($voucher->discountType == 'amount') {
				$ammount += $voucher->discount;
			} else {
				$percentage = $voucher->discount > $percentage ? $voucher->discount : $percentage; //procenta se nesčítají
			}
		}
		return [$ammount, $percentage];
	}

	public function render($args = NULL) {
		$basket = $this->presenter->session->getSection('basket');
		$vouchers = $this->presenter->session->getSection('vouchers');
		list($sumPiece, $sumPrice) = self::sumBasket($this->em, $basket, $vouchers);

		$this->template->setFile($this->templateFileName);
		$this->template->sumPiece = $sumPiece;
		$this->template->sumPrice = $sumPrice;
		$this->template->render();
	}

}
