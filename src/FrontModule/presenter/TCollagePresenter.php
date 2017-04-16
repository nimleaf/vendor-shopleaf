<?php

namespace Nimleaf\Shopleaf\FrontModule\Presenters;

use App\Model\Doc\Tag;
use \Imagick;
use Latte\Engine;
use mPDF;
use Nette\Templating\FileTemplate;

trait TCollagePresenter {

	public function renderDefault($id, $case) {
		$products = $this->getProducts($id, $case);
		$this->template->products = $products;
	}

	public function renderPdf($id, $case) {
		$products = $this->getProducts($id, $case);

		$pdf = new mPDF;
		$pdf->WriteHTML((string) $this->getPdfTemplate($products));
		$pdf->Output($case, 'I');
	}

	//todo imagick not foundS
	public function renderJpg($id, $case) {
		$pdf = $this->link('pdf', ['id' => $id, 'case' => $case]);
		$im = new Imagick($pdf);
		$im->setImageFormat("jpg");
		$img_name = time() . '.jpg';
		$im->setSize(800, 600);
		$im->writeImage($img_name);
		$im->clear();
		$im->destroy();
	}

	protected function getPdfTemplate($products) {
		$template = new FileTemplate(__DIR__ . '\..\templates\Collage\pdf.latte');
		$template->registerFilter(new Engine());

		$template->products = $products;

		return $template;
	}

	protected function getProducts($id, $case) {
		$products = [];
		switch ($case) {
			case 'tag':
				$tag = Tag::load($this->em, $id);
				if ($tag) {
					$products = $tag->getProducts($this->em, TRUE);
				}
				break;
		}
		return $products;
	}

}
