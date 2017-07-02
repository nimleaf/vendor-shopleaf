<?php

namespace Nimleaf\Shopleaf\FrontModule\Form;

use App\Model\Doc\Address;
use App\Model\Doc\Contact;
use App\Model\Doc\Order;
use App\Model\Doc\Payment;
use App\Model\Doc\Product;
use App\Model\Doc\Shipping;
use App\Model\Doc\User;
use App\Model\Doc\Voucher;
use App\Model\Doc\VoucherExeption;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Nimleaf\Strings;

trait TOrderFormFactory {

	/** @var EntityManager */
	protected $em;

	/** @var User */
	protected $user;

	/** @var string */
	protected $orderId;

	/** @var string */
	protected $termsAndConditionLink;

	/** @var array */
	protected $basket;

	/** @var float cena produktů v objednávce */
	protected $productsPrice = 0;

	/** @var array */
	protected $vouchers;

	public function __construct(EntityManager $em, &$orderId, $termsAndConditionLink, $basket, $vouchers, User $user = NULL) {
		$this->em = $em;
		$this->orderId = &$orderId;
		$this->termsAndConditionLink = $termsAndConditionLink;
		$this->basket = $basket;
		$this->user = $user;
		$this->vouchers = $vouchers;
	}

	protected function getDefaults() {
		if (!$this->user) {
			return [];
		}
		$user = $this->user;
		$defaults = [
			'email' => $user->contact->email,
			'phone' => $user->contact->phone,
			'name' => $user->address->name,
			'surname' => $user->address->surname,
			'street' => $user->address->street,
			'town' => $user->address->town,
			'zip' => $user->address->zip,
		];

		return $defaults;
	}

	public function create() {

		$paymentOptions = $this->getPaymentOptions();
		$shippingOptions = $this->getShippingOptions();

		$paymentFirst = array_values(Payment::getAllToArray($this->em, 'sort'))[0];
		$shippingFirst = array_values(Shipping::getAllToArray($this->em, 'sort'))[0];

		$termsAndCondition = Html::el('a', _("obchodními podmínkami"))->addAttributes(['target' => "_blank"]);
		$termsAndCondition->href = $this->termsAndConditionLink;
		$agree = Html::el()->setHtml(_("souhlasím s ") . $termsAndCondition);

		$form = parent::create();
		$form->getElementPrototype()->class[] = "order-form";

		$form->addGroup()
				->setOption('container', Html::el('fieldset')->class("fieldset-50"));

		$form->addRadioList('shipping', _("možnosti dopravy"), $shippingOptions)
				->setRequired(_("Vyberte dopravu."))
				->setAttribute('class', 'option-input')
				->setDefaultValue($shippingFirst->id)
				->getSeparatorPrototype()->setName('');

		$form->addRadioList('payment', _("možnosti platby"), $paymentOptions)
				->setRequired(_("Vyberte platbu."))
				->setAttribute('class', 'option-input')
				->setDefaultValue($paymentFirst->id)
				->getSeparatorPrototype()->setName('');

		$form->addGroup()
				->setOption('description', Html::el('h2')->setText(_("dodací údaje")));

		$form->addText('h', "")
				->setAttribute('class', 'h');

		$form->addGroup(_("adresa"))
				->setOption('container', Html::el('fieldset')->class("col-md-6 mt20"));

		$form->addText('name', _("jméno"))
				->setRequired(_("Vložte jméno."));

		$form->addText('surname', _("příjmení"))
				->setRequired(_("Vložte příjmení."));

		$form->addText('street', _("ulice"))
				->setRequired(_("Vložte ulici."));

		$form->addText('town', _("město"))
				->setRequired(_("Vložte město."));

		$form->addText('zip', _("PSČ"))
				->setRequired(_("Vložte platné PSČ."))
				->addRule(Form::PATTERN, _("Vložte platné PSČ."), '([0-9]\s*){5}');

		$form->addGroup(_("souhrn"))
				->setOption('container', Html::el('fieldset')->class("col-md-6 mt20"));

		$form->addText('email', _("e-mail"))
				->setRequired(_("Vložte platný e-mail."))
				->addRule(Form::EMAIL, _("Vložte platný e-mail."));

		$form->addText('phone', _("telefon"))
				->setRequired(_("Vložte telefonní číslo."));

		$form->addTextArea('comment', _("komentář"))
				->setAttribute('class', "form-control");

		$form->addCheckbox('agree', $agree)
				->addRule(Form::EQUAL, _("Pro odeslání objednávky je nutný souhlas s obchodními podmínkami."), TRUE);

		$form->addGroup()
				->setOption('container', Html::el('fieldset')->class("col-md-12 mt20"));

		$form->addSubmit('send', _("závazně objednat"));

		$form->setDefaults($this->getDefaults());

		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		$comment = Strings::emptyToNull($values->comment);
		$contact = $this->setContact($values);
		$deliveryAddress = $this->setDeliveryAddress($values);

		$payment = Payment::load($this->em, $values->payment);
		$shipping = Shipping::load($this->em, $values->shipping);

		$order = new Order($comment, $contact, $deliveryAddress, $payment, $shipping);
		$this->setProducts($order);
		try {
			$this->setVouchers($order);
		} catch (VoucherExeption $ex) {
			$form->addError($ex->getMessage());
		}

		$order->save($this->em);

		$this->recountCachePiece();
		$this->orderId = $order->id;
	}

	protected function getPaymentOptions() {
		$payment = Payment::getAllToArray($this->em, 'sort');
		$paymentOptions = [];

		foreach ($payment as $option) {
			$title = Html::el('span')->class('title')->setHtml($option->name . '<br/>');
			$description = Html::el('span')->class('description')->setHtml($option->description . '<br/>');
			$price = Html::el('span')->class('price')->setHtml(_("cena") . ' <strong>' . $option->price . ' ' . _('Kč') . '</strong><br/>');

			$paymentOptions[$option->id] = Html::el('div')->class('option')->setHtml($title . $description . $price);
		}

		return $paymentOptions;
	}

	protected function getShippingOptions() {
		$shipping = Shipping::getAllToArray($this->em, 'sort');
		$shippingOptions = [];

		foreach ($shipping as $option) {
			if ($option->icon !== NULL) {
				$logo = Html::el('img')->src(Shipping::$PATH_ICON . $option->icon);
			} else {
				$logo = '';
			}
			$title = Html::el('span')->class('title')->setHtml($option->name . '<br/>');
			$description = Html::el('span')->class('description')->setHtml($option->description . '<br/>');
			$deliveryTime = Html::el('span')->class('delivery-time')->setHtml(_("doba dodání") . ' <strong>' . $option->deliveryTime . '</strong><br/>');
			$price = Html::el('span')->class('price')->setHtml(_("cena") . ' <strong>' . $option->price . ' ' . _('Kč') . '</strong><br/>');

			$firstEl = Html::el('div')->class('logo')->setHtml($logo);
			$secondEl = Html::el('div')->class('text')->setHtml($title . $description . $deliveryTime . $price);

			$shippingOptions[$option->id] = Html::el('div')->class('option')->setHtml($firstEl . $secondEl);
		}

		return $shippingOptions;
	}

	protected function recountCachePiece() {
		foreach ($this->basket as $productId => $piece) {
			$product = Product::load($this->em, $productId);
			if ($product) {
				$product->cachePiece = $product->recountCachePiece();
				$product->save($this->em);
			}
		}
	}

	protected function setContact($values) {
		$contact = new Contact;
		$contact->phone = $values->phone;
		$contact->email = $values->email;

		return $contact;
	}

	protected function setDeliveryAddress($values) {
		$deliveryAddress = new Address;
		$deliveryAddress->name = $values->name;
		$deliveryAddress->surname = $values->surname;
		$deliveryAddress->street = $values->street;
		$deliveryAddress->town = $values->town;
		$deliveryAddress->zip = $values->zip;

		return $deliveryAddress;
	}

	protected function setProducts(Order &$order) {
		foreach ($this->basket as $productId => $piece) {
			$product = Product::load($this->em, $productId);
			if ($product) {
				$this->productsPrice += $product->getFinalPrice();
				$order->addProductSold($product, $piece);
			}
		}
	}

	protected function setVouchers(Order &$order) {
		foreach ($this->vouchers as $voucherId => $voucher) {
			$voucher = Voucher::load($this->em, $voucherId);
			$order->addVoucherSold($voucher);
		}
	}

}
