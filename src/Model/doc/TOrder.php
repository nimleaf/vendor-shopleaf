<?php

namespace Nimleaf\Shopleaf\Model\Doc;

use App\Model\Doc\Address;
use App\Model\Doc\Contact;
use App\Model\Doc\OrderStatusHistory;
use App\Model\Doc\Payment;
use App\Model\Doc\Product;
use App\Model\Doc\ProductSold;
use App\Model\Doc\Settings;
use App\Model\Doc\Shipping;
use App\Model\Doc\Voucher;
use App\Model\Doc\VoucherSold;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

trait TOrder {

	public static $STATUS = [
		NULL => "---",
		'new-order' => "nová objednávka",
		'waiting-for-payment' => "čeká na platbu",
		'paid' => "zaplaceno",
		'sent' => "odesláno",
		'sent-cod' => "odesláno na dobírku",
		'completed' => "dokončeno",
	];

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string komentář zákazníka k objednávce
	 */
	protected $comment;

	/**
	 * @ORM\OneToOne(targetEntity="Contact", cascade={"persist", "remove"})
	 * @var Contact kontaktní údaje
	 */
	protected $contact;

	/**
	 * @ORM\OneToOne(targetEntity="Address", cascade={"persist", "remove"})
	 * @var Address doručovací adresa
	 */
	protected $deliveryAddress;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string IP adresa
	 */
	protected $ip;

	/**
	 * @ORM\ManyToOne(targetEntity="Payment")
	 * @var string možnost platby
	 */
	protected $payment;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string možnost platby - název
	 */
	protected $paymentName;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string možnost platby - cena
	 */
	protected $paymentPrice;

	/**
	 * @ORM\OneToMany(targetEntity="ProductSold", mappedBy="order", cascade={"persist", "remove"})
	 * @var ProductSold
	 */
	protected $productSold = [];

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string aktuální status objednávky
	 */
	protected $status;

	/**
	 * @ORM\OneToMany(targetEntity="OrderStatusHistory", mappedBy="order", cascade={"persist", "remove"})
	 * @var OrderStatusHistory
	 */
	protected $statusHistory = [];

	/**
	 * @ORM\ManyToOne(targetEntity="Shipping")
	 * @var string možnost dopravy
	 */
	protected $shipping;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string možnost dopravy - ikona
	 */
	protected $shippingIcon;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string možnost dopravy - název
	 */
	protected $shippingName;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string možnost dopravy - cena
	 */
	protected $shippingPrice;

	/**
	 * @ORM\OneToMany(targetEntity="VoucherSold", mappedBy="order", cascade={"persist", "remove"})
	 * @var VoucherSold
	 */
	protected $voucherSold = [];

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string doprava - sledovací kód
	 */
	public $shippingTrackingCode;

	public function __construct($comment, Contact $contact, Address $deliveryAddress, Payment $payment, Shipping $shipping) {
		parent::__construct();
		$this->comment = $comment;
		$this->contact = $contact;
		$this->deliveryAddress = $deliveryAddress;
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->payment = $payment;
		$this->paymentName = $payment->name;
		$this->paymentPrice = $payment->price;
		$this->shipping = $shipping;
		$this->shippingIcon = $shipping->getIcon();
		$this->shippingName = $shipping->name;
		$this->shippingPrice = $shipping->price;

		$this->setStatus();
	}

	public function __toString() {
		return '';
	}

	/**
	 * Přidá produkt k objednávce
	 * @param Product $product
	 * @param type $piece
	 * @return ProductSold
	 */
	public function addProductSold(Product $product, $piece) {
		$productSold = new ProductSold($this, $product, $piece);
		$this->productSold[] = $productSold;
		return $productSold;
	}

	public function addVoucherSold(Voucher $voucher) {
		$voucherSold = new VoucherSold($this, $voucher);
		$this->voucherSold[] = $voucherSold;
		return $voucherSold;
	}

	/**
	 * Vygeneruje 'code' pro QR kód
	 * @param EntityManager $em
	 * @return string
	 */
	public function generatePaymentQrCode(EntityManager $em) {
		$settingsInvoice = Settings::getSettings($em)->invoice;
		$msg = $this->deliveryAddress->name . ' ' . $this->deliveryAddress->surname;
		return $settingsInvoice->generatePaymentQrCode($this->getFinalPrice(), $this->code, $msg);
	}

	public function getContact() {
		return $this->contact;
	}

	public function getDeliveryAddress() {
		return $this->deliveryAddress;
	}

	public function getFinalPiece() {
		$piece = 0;
		foreach ($this->productSold as $product) {
			$piece += $product->piece;
		}
		return $piece;
	}

	public function getFinalPrice() {
		$price = $this->getProductsPrice();
		$percentageDiscount = 0;

		foreach ($this->voucherSold as $voucherSold) {
			if ($voucherSold->discountType == 'amount') {
				$price -= $voucherSold->discount;
			} else { //percentage
				$percentageDiscount = $voucherSold->discount > $percentageDiscount ? $voucherSold->discount : $percentageDiscount; //procenta se nesčítají
			}
		}
		$price *= ((100 - $percentageDiscount) / 100);

		$price += $this->shippingPrice;
		$price += $this->paymentPrice;
		return $price;
	}

	public function getProductsPrice() {
		$price = 0;
		foreach ($this->productSold as $productSold) {
			$price += $productSold->price;
		}
		
		return $price;
	}
	
	public function getProductSold() {
		return $this->productSold;
	}

	/**
	 * Najde prodej produktu podle ID
	 * @param string $id
	 * @return ProductSold
	 */
	public function getProductSoldById($id) {
		foreach ($this->productSold as $productSold) {
			if ($productSold->getProduct()->id === $id) {
				return $productSold;
			}
		}
	}

	public function getShipping() {
		return $this->shipping;
	}

	public function getStatusHistory() {
		return $this->statusHistory;
	}

	public function getTrackingCodeCompleteUrl() {
		if ($this->shippingTrackingCode == NULL || $this->shipping->trackingCodeUrl == NULL) {
			return NULL;
		}
		return str_replace("%%%%%", $this->shippingTrackingCode, $this->shipping->trackingCodeUrl);
	}

	public function getTranslatedStatus() {
		return self::$STATUS[$this->status];
	}
	
	public function getVoucherSold() {
		return $this->voucherSold;
	}

	/**
	 * Nastaví status objednávce
	 * @param string $status
	 * @todo existence statusu
	 */
	public function setStatus($status = NULL) {
		$newStatus = $status == NULL ? 'new-order' : $status;
		$this->status = $newStatus;

		$orderStatusHistory = new OrderStatusHistory($this, $newStatus);
		$this->statusHistory[] = $orderStatusHistory;
	}

	/**
	 * Odebere produkt z objednávky
	 * @param ProductSold $productSold
	 */
	public function unsetProductSold(ProductSold $productSold) {
		foreach ($this->productSold as $key => $value) {
			if ($productSold == $value) {
				unset($this->productSold[$key]);
			}
		}
	}

	/**
	 * Odebere voucher z objednávky
	 * @param VoucherSold $voucherSold
	 */
	public function unsetVoucherSold(VoucherSold $voucherSold) {
		foreach ($this->voucherSold as $key => $value) {
			if ($voucherSold == $value) {
				unset($this->voucherSold[$key]);
			}
		}
	}

}
