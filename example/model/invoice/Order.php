<?php

namespace NimExampleShopleaf\App\Model\Invoice;

use App\Model\Doc\Order as odmOrder;
use App\Model\Doc\Settings;
use Doctrine\ORM\EntityManager;
use Latte\Engine;
use mPDF;
use Nette\Environment;
use Nette\Templating\FileTemplate;

class Order {

	/** @var EntityManager */
	protected $em;
	
	/** @var odmOrder */
	protected $order;

	public function __construct(EntityManager $em, odmOrder $order) {
		$this->em = $em;
		$this->order = $order;
	}

	public function pdf() {
		$order = $this->order;
		$name = [
			date_format($order->dateCreated, 'Y-m-d'),
			$order->id,
			$order->deliveryAddress->name,
			$order->deliveryAddress->surname,
		];
		
		$pdf = new mPDF;
		$pdf->WriteHTML((string)  $this->template());
		$pdf->Output(implode('-', $name).'.pdf', 'I');
	}
	
	public function pdfAttachment() {
		$pdf = new mPDF;
		$pdf->WriteHTML((string)  $this->template());
		return $pdf->Output('', 'S');
	}

		public function template() {
		$template = new FileTemplate(__DIR__ . '/templates/order.latte');
		$template->registerFilter(new Engine());

		$template->basePath = Environment::getHttpRequest()->getUrl()->getBasePath();
		$template->em = $this->em;
		$template->pageSettings = Settings::getSettings($this->em);
		$template->order = $this->order;
	
		return $template;
	}

}
