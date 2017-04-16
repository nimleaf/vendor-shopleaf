<?php

namespace Nimleaf\Shopleaf\Model\Doc;

use App\Model\Doc\AukroCategory;
use Doctrine\ORM\EntityManager;

trait TProductCategory {

	/**
	 * @ORM\OneToOne(targetEntity="AukroCategory", cascade={"persist", "remove"})
	 * @var AukroCategory aukro.cz kategorie
	 */
	protected $aukro;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string jméno
	 */
	public $name;

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Prověří zda není v této kategorii nějaký produkt
	 * @param EntityManager $em
	 */
	public function canBeDeleted(EntityManager $em) {
		if (count($this->getProducts($em)) > 0) {
			return FALSE;
		}
		return parent::canBeDeleted($em);
	}

	public function getAukro() {
		return $this->aukro ? $this->aukro : $this->aukro = new AukroCategory();
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
	public function getProducts(EntityManager $em, $onlyAvaible = FALSE, $order = NULL, $by = NULL, $limit = NULL, $offset = NULL) {
		$products = $em->createQueryBuilder()
				->select('a')->from('App\Model\Doc\Product', 'a')
				->where("a.category = $this->id");
				

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
