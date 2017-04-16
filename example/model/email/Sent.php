<?php

namespace NimExampleShopleaf\App\Model\Email;

use App\Model\Doc\Order;
use Doctrine\ORM\EntityManager;
use Nette\Mail\IMailer;

class Sent extends Base {

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
		return __DIR__ . '/templates/sent.latte';
	}
	
	protected function template() {
		$template = parent::template();
		$template->order = $this->order;
		
		return $template;
	}

	public function sendMail() {
		$this->send();
	}

}
