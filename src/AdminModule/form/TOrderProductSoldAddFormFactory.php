<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Order;
use App\Model\Doc\Product;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;
use \Exception;

/**
 * Přidá produkt do vytvořené objednávky
 * 2 krokový formulář
 */
trait TOrderProductSoldAddFormFactory {

	/** @var form step */
	protected $formStep;

	/** @var Order */
	protected $order;

	/** @var product id */
	protected $productId;

	public function __construct(EntityManager $em, Order $order, &$formStep, &$productId) {
		parent::__construct($em);
		$this->formStep = &$formStep;
		$this->order = $order;
		$this->productId = &$productId;
	}

	protected function getDefaults() {
		$product = Product::load($this->em, $this->productId);
		$defaults = [
			'piece' => 1,
			'price' => $product->getFinalPrice(),
		];

		return $defaults;
	}

	protected function applyValues(Order $order, $values) {
		if ($this->formStep == 1) {
			$product = Product::loadOneBy($this->em, ['code' => $values->code]);
			
			if ($product) {
				$this->productId = $product->id;
				$this->formStep = 2;
			} else {
				throw new Exception(_("Produkt s tímto kódem neexistuje."));
			}
		} else { // step 2
			$product = Product::load($this->em, $this->productId);
			$piece = $values->piece;
			if ($product && $piece > 0) {
				$productSold = $order->addProductSold($product, $piece);
				$productSold->setPrice($values->price);
				$productSold->save($this->em);
			} else {
				throw new Exception(_("Produkt nelze vložit. Chybný počet kusů."));
			}
			$order->save($this->em);
			$this->formStep = 1;
			$this->productId = NULL;
		}
	}

	public function createStep1() {
		$form = parent::create();

		$form->addText('code', _("kód"))
				->setAttribute('class', "form-control")
				->setRequired();

		$form->addSubmit('send', _("Uložit"));

		return $form;
	}

	public function createStep2() {
		$form = parent::create();

		$form->addText('piece', _("počet kusů"))
				->setAttribute('class', "form-control")
				->setType('number')
				->setRequired();

		$form->addText('price', _("cena"))
				->setAttribute('class', "form-control")
				->setType('number')
				->setRequired();

		$form->addSubmit('send', _("Uložit"));

		$form->setDefaults($this->getDefaults());

		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		$order = $this->order;
		try {
			$this->applyValues($order, $values);
		} catch (Exception $ex) {
			$form->addError($ex->getMessage());
		}
	}

}
