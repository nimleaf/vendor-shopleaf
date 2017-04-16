<?php

namespace NimExampleShopleaf\App\Model\Email;

use App\Model\Doc\Order;
use App\Model\Doc\Settings;
use App\Model\Invoice\Order as InvoiceOrder;
use Doctrine\ORM\EntityManager;
use Nette\Mail\IMailer;

class Completed extends Base {

	protected $order;

	public function __construct(EntityManager $em, IMailer $mailer, Order $order, $name) {
		parent::__construct($em, $mailer);
		$this->order = $order;
		$this->name = $name;
	}

	protected function getEmailTo() {
		return $this->order->contact->email;
	}

	protected function getFile() {
		return __DIR__ . '/templates/completed.latte';
	}
	
	protected function template() {
		$template = parent::template();
		$template->order = $this->order;
		$template->pageSettings = Settings::getSettings($this->em);
		
		return $template;
	}

	public function sendMail() {
		$invoice = new InvoiceOrder($this->em, $this->order);
		$attachment = $invoice->pdfAttachment();
		$this->send($attachment);
	}

}
