<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Product;
use App\Model\Doc\User;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TProductEditFormFactory {

	/** @var Product */
	protected $product;

	public function __construct(EntityManager $em, User $user, Product $product) {
		parent::__construct($em, $user);
		$this->product = $product;
	}

	protected function getDefaults() {
		$product = $this->product;

		$defaults = [
			'name' => html_entity_decode($product->name),
			'description' => $product->description,
			'code' => $product->code,
			'image' => $product->image,
			'price' => $product->price,
			'actionPrice' => $product->actionPrice,
			'note' => $product->note,
			'active' => $product->active,
		];

		$tags = [];
		foreach ($product->tags as $tag) {
			$tags[] = $tag->id;
		}
		$defaults['tags'] = $tags;

		if ($product->category !== NULL) {
			$defaults['category'] = $product->category->id;
		}

		return $defaults;
	}

	public function formSucceeded(Form $form, $values) {
		$product = $this->product;
		$this->applyValues($product, $values);
	}

}
