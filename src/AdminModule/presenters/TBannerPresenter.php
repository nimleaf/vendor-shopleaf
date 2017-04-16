<?php

namespace Nimleaf\Shopleaf\AdminModule\Presenters;

use App\AdminModule\Form\BannerCreateFormFactory;
use App\AdminModule\Form\BannerEditFormFactory;
use App\Model\Doc\Banner;
use App\Model\Doc\Settings;

trait TBannerPresenter {

	/** @var Banner */
	protected $banner;

	public function handleDelete($id) {
		$banner = Banner::load($this->em, $id);
		if ($banner) {
			$banner->delete($this->em);
			$this->flashMessage(_("Baner byl smazán."));
		}
		$this->redirect('this');
	}

	public function handleSetAsMainBanner($id) {
		$banner = Banner::load($this->em, $id);
		if ($banner) {
			$settings = Settings::getSettings($this->em);
			$settings->mainBanner = $banner;
			$settings->save($this->em);
			$this->flashMessage(_("Baner byl nastaven jako výchozí."));
		}
		$this->redirect('this');
	}

	public function handleUnsetMainBanner() {
		$settings = Settings::getSettings($this->em);
		$settings->mainBanner = NULL;
		$settings->save($this->em);
		$this->flashMessage(_("Zobrazení baneru bylo zrušeno."));

		$this->redirect('this');
	}

	public function renderDefault() {
		$this->template->banners = Banner::getAll($this->em);
	}

	public function actionEdit($id) {
		$this->banner = Banner::load($this->em, $id);
		if (!$this->banner) {
			$this->flashMessage(_("Baner nenalezen."), 'error');
			$this->redirect('default');
		}
	}

	public function renderEdit() {
		$this->template->banner = $this->banner;
	}

	protected function createComponentCreateForm() {
		$f = new BannerCreateFormFactory($this->em);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Baner byl vytvořen."));
			$this->redirect('default');
		};
		return $form;
	}

	protected function createComponentEditForm() {
		$f = new BannerEditFormFactory($this->em, $this->banner);
		$form = $f->create();
		$form->onSuccess[] = function ($form) {
			$this->flashMessage(_("Baner byl upraven."));
			$this->redirect('default');
		};
		return $form;
	}

}
