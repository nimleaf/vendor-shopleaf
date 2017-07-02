<?php

namespace Nimleaf\Shopleaf\Model\Doc;

use Doctrine\ORM\EntityManager;
use App\Model\Doc\Product;

trait TProductTag {

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string název skupiny
	 */
	public $name;

	public function __construct() {
		parent::__construct();
	}

	public function __toString() {
		return $this->name;
	}

	public static function getAllNames(EntityManager $em) {
		$tags = self::getAll($em);
		$names = [];
		foreach ($tags as $tag) {
			$names[$tag->id] = $tag->name;
		}
		return $names;
	}

	public static function getByName(EntityManager $em, $name) {
		$tags = self::getAll($em);
		foreach ($tags as $tag) {
			if ($tag->name === $name) {
				return $tag;
			}
		}
		return NULL; //tag nenalezen
	}

	public static function getByNameOrCreateNew(EntityManager $em, $name) {
		$tag = self::getByName($em, $name);
		if ($tag === NULL) {
			$tag = new Tag();
			$tag->name = $name;
			$tag->save($em);
		}
		return $tag;
	}

	/**
	 * Prověří zda nějaký produkt namá přiřazený tento tag
	 * @param EntityManager $em
	 */
	public function canBeDeleted(EntityManager $em) {
		if (count($this->getProducts($em)) > 0) {
			return FALSE;
		}
		return parent::canBeDeleted($em);
	}

	/**
	 * Počet produktů v kategorii
	 * @param EntityManager $em
	 * @param boolean $onlyAvaible pouze dostupné produkty
	 * @param string $order řadit podle pole
	 * @param int $limit maximální počet záznamů
	 * @param int $offset start od x-tého záznamu
	 * @return Product []
	 */
	public function getProducts(EntityManager $em, $onlyAvaible = FALSE, $order = NULL, $by = NULL, $limit = NULL, $offset = NULL) {
		$products = $em->createQueryBuilder()
				->select('a')->from('App\Model\Doc\Product', 'a')
				->innerJoin('a.tags', 't', 'WITH', "t.id = $this->id");

		if ($onlyAvaible === TRUE) {
			$products->andWhere("a.active = '1'");
			$products->andWhere("a.cachePiece > '0'");
		}

		if ($order !== NULL) {
			if ($by == (NULL || 'DESC')) {
				$by = 'DESC';
			} else {
				$by = 'ASC';
			}
			$products = $products->orderBy('a.' . $order, $by);
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

}
