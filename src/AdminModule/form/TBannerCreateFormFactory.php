<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Banner;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;
use Nette\Utils\Image;

trait TBannerCreateFormFactory {

	public function __construct(EntityManager $em) {
		parent::__construct($em);
	}

	protected function applyValues(Banner $banner, $values) {
		$banner->title = $values->title;

		if ($values->image->size > 0) {
			$banner->name = $values->image->name;

			$image = Image::fromFile($values->image);
			$image->save(ROOT_DIR . '/www' . Banner::$PATH . $values->image->name);
		}

		$banner->save($this->em);
	}

	public function create() {
		$form = parent::create();

		$form->addText('title', _("titulek"))
				->setRequired();

		$form->addUpload('image', _("obrázek"))
				->addCondition(Form::FILLED)
				->addRule(Form::IMAGE, _("Nepovolený formát obrázku."));

		$form->addSubmit('send', _("Uložit"));

		$form->setDefaults($this->getDefaults());

		return $form;
	}

	public function formSucceeded(Form $form, $values) {
		$banner = new Banner;
		$this->applyValues($banner, $values);
	}

}
