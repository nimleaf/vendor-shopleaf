<?php

namespace Nimleaf\Shopleaf\Model\Doc;

use Doctrine\ORM\EntityManager;
use App\Model\Doc\Settings;

trait TBanner {
	
	public static $PATH = '/img/banner/';

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string název suoboru
	 */
	public $name;
	
	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string titulet obrázku
	 */
	public $title;

	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Prověří zda mazaný baner není výchozí
	 * @param EntityManager $em
	 */
	public function canBeDeleted(EntityManager $em) {
		//výchozí baner
		$settings = Settings::getSettings($em);
		if ($settings->mainBanner && $settings->mainBanner->id === $this->id) {
			return FALSE;
		}
		return parent::canBeDeleted($em);
	}

	
	public function getPath() {
		return self::$PATH . $this->name;
	}
}
