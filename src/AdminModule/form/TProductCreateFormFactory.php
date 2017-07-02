<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\DuplicateProductException;
use App\Model\Doc\Product;
use App\Model\Doc\ProductCategory;
use App\Model\Doc\ProductTag;
use App\Model\Doc\User;
use Doctrine\ORM\EntityManager;
use Nimleaf\Strings;
use Nette\Application\UI\Form;

trait TProductCreateFormFactory {

	private $productId;

	/** @var User */
	protected $user;

	public function __construct(EntityManager $em, User $user, &$productId = NULL) {
		parent::__construct($em);
		$this->productId = &$productId;
		$this->user = $user;
	}

	protected function applyValues(Product $product, $values) {
		$product->name = $values->name;
		$product->description = $values->description;
		$product->price = $values->price;
		$product->actionPrice = Strings::emptyToNull($values->actionPrice);
		$product->note = Strings::emptyToNull($values->note);
		$product->active = $values->active;

		$product->tags = [];
		foreach ($values->tags as $id) {
			$tag = ProductTag::load($this->em, $id);
			$product->addProductTag($tag);
		}

		$product = $product->save($this->em);

		if ($values->image->size > 0) {
			$image = new Media();
			$image->save($this->em);
			$product->addImage($image);

			$image->setImage($values->image);
			$image->setUser($this->user);
			$image->name = "produkt id " . $product->id;
		}

		$category = ProductCategory::load($this->em, $values->category);
		$product->setCategory($category);

		$product->save($this->em);
	}

	public function create() {
		$category = [NULL => ""] + ProductCategory::getAllToArray($this->em);

		$form = parent::create();

		$form->addText('name', _("název"))
				->setRequired();

		$form->addTextArea('description', _("popis"))
				->setAttribute('class', 'wysiwyg');

		$form->addText('code', _("kód"))
				->setRequired();

		$form->addUpload('image', _("náhled"));

		$form->addText('price', _("cena"))
				->setRequired();

		$form->addText('actionPrice', _("akční cena"));

		$form->addSelect('category', _("kategorie"), $category)
				->setAttribute('class', "form-control");

		$form->addMultiSelect('tags', _("tagy"), ProductTag::getAllNames($this->em))
				->setAttribute('class', "form-control select2 basic-multiple");
		//->setAttribute('id', "select2-tags");
		//todo nový tag se neukládá

		$form->addTextArea('note', _("poznámka"))
				->setAttribute('class', "form-control");

		$form->addCheckbox('active', _("aktivní (ihned zobrazit)"))
				->setDefaultValue(TRUE);

		$form->addSubmit('send', _("Uložit"));

		$form->setDefaults($this->getDefaults());

		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		try {
			$product = new Product($this->em, $values->code, $this->user);
			$this->applyValues($product, $values);
			$this->productId = $product->id;
		} catch (DuplicateProductException $ex) {
			$form->addError(_($ex->getMessage()));
		}
	}

}
