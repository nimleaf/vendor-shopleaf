<?php

namespace Nimleaf\Shopleaf\Model\Doc;

use App\Model\Doc\AukroProduct;
use App\Model\Doc\Media;
use App\Model\Doc\Order;
use App\Model\Doc\ProductCategory;
use App\Model\Doc\ProductCorrection;
use App\Model\Doc\ProductPurchase;
use App\Model\Doc\ProductSold;
use App\Model\Doc\ProductTag;
use App\Model\Doc\User;
use Doctrine\ORM\EntityManager;

trait TProduct {

	public static $IMAGE_EXTENSION = '.jpg';
	public static $IMAGE_EXTENSION_MINI = '-mini.jpg';
	public static $IMAGE_EXTENSION_THUMB = '-thumb.jpg';
	public static $IMAGE_EXTENSION_NORMAL = '-normal.jpg';
	public static $IMAGE_PATH = '/www/img/product/';
	public static $IMAGE_SHOW_PATH = '/img/product/';

	/**
	 * @ORM\OneToOne(targetEntity="AukroProduct", cascade={"persist", "remove"})
	 * @var string aukro
	 */
	protected $aukro;

	/**
	 * @ORM\ManyToOne(targetEntity="ProductCategory")
	 * @var ProductCategory kategorie
	 */
	protected $category;

	/**
	 * @ORM\OneToMany(targetEntity="ProductCorrection", mappedBy="product", cascade={"persist", "remove"})
	 * @var ProductCorrection
	 */
	protected $correction = [];

	/**
	 * @ORM\OneToMany(targetEntity="ProductPurchase", mappedBy="product", cascade={"persist", "remove"})
	 * @var ProductPurchase nákup produktu
	 */
	protected $purchase = [];

	/**
	 * @ORM\OneToMany(targetEntity="ProductSold", mappedBy="product")
	 * @var ProductSold prodej produktu
	 */
	protected $sold = [];

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 * @var boolean je produkt aktivní
	 */
	public $active = TRUE;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 * @var float akční cena za kus
	 */
	public $actionPrice;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * @var int počet kusů k prodeji
	 */
	public $cachePiece;

	/**
	 * @ORM\Column(type="string", nullable=true, unique=true)
	 * @var string kód produktu
	 */
	public $code;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @var string popis
	 */
	public $description;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string obrázek
	 * @deprecated nově používat $images
	 */
	public $image;

	/**
	 * @ORM\ManyToOne(targetEntity="Media")
	 * @var hlavní obrázek
	 */
	public $images = [];

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string název
	 */
	public $name;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string poznámka
	 */
	public $note;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 * @var float cena za kus
	 */
	public $price;

	/**
	 * @ORM\ManyToMany(targetEntity="ProductTag")
	 * @var ProductTag
	 */
	public $tags = [];

	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @var string uživatel, který produkt vložil
	 */
	protected $user;

	/**
	 * @param EntityManager $em
	 * @param string $code
	 * @throws DuplicateProductException
	 */
	public function __construct(EntityManager $em, $code, User $user) {
		//validovat duplicitní kód
		try {
			self::validateDuplicate($em, $code);
		} catch (DuplicateProductException $ex) {
			throw $ex;
		}

		parent::__construct();
		$this->code = $code;
		$this->user = $user;
	}

	public function __toString() {
		return $this->name;
	}

	/**
	 * Zvaliduje kód produktu zda není duplicitní
	 * @param EntityManager $em
	 * @param string $code
	 * @throws DuplicateProductException
	 */
	public static function validateDuplicate(EntityManager $em, $code) {
		$object = $em->getRepository(get_called_class())->findOneBy(['code' => $code]);
		if ($object) {
			throw new DuplicateProductException("Produkt s tímto kódem existuje.");
		}
	}

	public static function getAllActions(EntityManager $em, $sort = NULL, $limit = NULL) {
		$all = self::getAvaible($em, $sort, $limit);
		$allActions = [];
		foreach ($all as $product) {
			if (isset($product->actionPrice)) {
				$allActions[] = $product;
			}
		}
		$actions = array_slice($allActions, 0, $limit);
		return $actions;
	}

	/**
	 * Počet produktů v kategorii
	 * @param EntityManager $em
	 * @param boolean $onlyAvaible pouze dostupné produkty
	 * @param string $sort řadit podle pole
	 * @param int $limit maximální počet záznamů
	 * @param int $offset start od x-tého záznamu
	 * @return Product []
	 */
	public static function getAllProducts(EntityManager $em, $onlyAvaible = FALSE, $sort = NULL, $limit = NULL, $offset = NULL) {
		$products = $em->createQueryBuilder()
						->select('a')->from('App\Model\Doc\Product', 'a');


		if ($onlyAvaible === TRUE) {
			$products->andWhere("a.active = '1'");
			$products->andWhere("a.cachePiece > '0'");
		}

		if ($sort !== NULL) {
			$products->orderBy('a.' . $sort, 'desc');
		}

		if ($limit !== NULL) {
			$products->setMaxResults($limit);
		}

		if ($offset !== NULL) {
			$products->setFirstResult($offset);
		}

		$query = $products->getQuery();
		return $query->getResult();
	}

	public static function getAvaible(EntityManager $em, $sort = NULL, $limit = NULL) {
		$all = self::getAll($em, $sort, $limit);
		$allAvaible = [];
		foreach ($all as $product) {
			if ($product->getPieces() > 0 && $product->active) {
				$allAvaible[] = $product;
			}
		}
		$products = array_slice($allAvaible, 0, $limit);
		return $products;
	}
	
	public function addImage(Media $image) {
		$this->images[] = $image;
	}

	public function addProductTag(ProductTag $tag) {
		$this->tags[] = $tag;
	}

	public function getAukro() {
		return $this->aukro ? $this->aukro : $this->aukro = new AukroProduct();
	}

	public function getFinalPrice() {
		return $this->actionPrice? : $this->price;
	}

	/**
	 * Vrátí url obrázku
	 * @param string $size (mini || thumb || normal || NULL == original size)
	 * @return type
	 */
	public function getImageUrl($size = NULL) {
		switch ($size) {
			case 'mini':
				return self::$IMAGE_SHOW_PATH . $this->image . self::$IMAGE_EXTENSION_MINI;
			case 'thumb':
				return self::$IMAGE_SHOW_PATH . $this->image . self::$IMAGE_EXTENSION_THUMB;
			case 'normal':
				return self::$IMAGE_SHOW_PATH . $this->image . self::$IMAGE_EXTENSION_NORMAL;
		}
		//original
		return self::$IMAGE_SHOW_PATH . $this->image . self::$IMAGE_EXTENSION;
	}

	/**
	 * Vrátí absolutní url obrázku
	 * @param string $size (mini || thumb || normal || NULL == original size)
	 * @return type
	 */
	public function getImageAbsoluteUrl(EntityManager $em, $size = NULL) {
		$settings = Settings::getSettings($em);
		return $settings->url . $this->getImageUrl($size);
	}

	public function getOrders(EntityManager $em) {
		$array = [];
		$all = Order::getAll($em);
		foreach ($all as $order) {
			if ($order->getProductSoldById($this->id)) {
				$array[] = $order;
			}
		}
		return $array;
	}

	/**
	 * Počet kusu na skladě
	 * @return type
	 */
	public function getPieces() {
		return $this->cachePiece;
	}

	public function haveTag($tag) {
		foreach ($this->tags as $thisTag) {
			if ($thisTag->id === $tag->id) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Přepočítá počet kusů při každé změně produktu
	 * @ORM\PreFlush
	 */
	public function hookPreFlush() {
		$this->cachePiece = $this->recountCachePiece();
	}

	/**
	 * Přepočítá dostupné kusy zboží a pokud se liší, vloží novou konstantu do databáze
	 * @return boolean TRUE pokud byl objekt přepsán
	 */
	public function recountCachePiece() {
		$pieces = 0;
		foreach ($this->purchase as $purchase) {
			$pieces += $purchase->piece;
		}
		foreach ($this->sold as $sold) {
			$pieces -= $sold->piece;
		}
		foreach ($this->correction as $correction) {
			$pieces += $correction->piece;
		}
		return $pieces;
	}

	public function setCategory(ProductCategory $category = NULL) {
		$this->category = $category;
	}

	public function setCorrection(ProductCorrection $correction) {
		$correction->product = $this;
		$this->correction[] = $correction;
	}

	public function setPurchase(ProductPurchase $purchase) {
		$purchase->product = $this;
		$this->purchase[] = $purchase;
	}

	public function setSold(ProductSold $sold) {
		$sold->product = $this;
		$this->sold[] = $sold;
	}

}
