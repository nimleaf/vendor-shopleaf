<?php

namespace Nimleaf\Shopleaf\Model\Doc;

use App\Model\Doc\Article;

trait TSettingsShop {

	/**
	 * @ORM\OneToOne(targetEntity="Article")
	 * @var Article
	 */
	protected $termsAndConditions;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string API Heureka
	 */
	public $apiHeureka;
	
	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var počet produktů na stránku
	 */
	public $productsOnPage;


	public function setTermsAndConditions(Article $termsAndConditions = NULL) {
		$this->termsAndConditions = $termsAndConditions;
	}

}
