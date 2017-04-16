<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Banner;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;

trait TBannerEditFormFactory {

	/** @var Banner */
	protected $banner;

	public function __construct(EntityManager $em, Banner $banner) {
		parent::__construct($em);
		$this->banner = $banner;
	}

	protected function getDefaults() {
		$defaults = [
			'title' => $this->banner->title,
		];

		return $defaults;
	}

	public function formSucceeded(Form $form, $values) {
		$banner = $this->banner;
		$this->applyValues($banner, $values);
	}

}
