<?php

namespace NimExampleShopleaf\App\Model\Email;

use App\Exception\EmailBadTypeException;
use App\Model\Doc\Order;
use Doctrine\ORM\EntityManager;
use Nette\Mail\IMailer;

class EmailSender {

	public static $EMAIL_NAME = [
		'new-order' => 'Přijatá objednávka',
		'waiting-for-payment' => "Platební údaje",
		'paid' => "Obdrželi jsme vaši platbu",
		'sent' => "Objednávka byla odeslána",
		'sent-cod' => "Objednávka byla odeslána na dobírku",
		'completed' => "Faktura k Vaší objednávce",
	];

	/** @var EntityManager */
	protected $em;

	/** @var string */
	protected $emailName;

	/** @var IMailer */
	protected $mailer;
	
	/** @var Order */
	protected $order;

	/**
	 * @param EntityManager $em
	 * @param Order $order
	 * @param string $emailName
	 */
	public function __construct(EntityManager $em, IMailer $mailer, Order $order, $emailName) {
		$this->em = $em;
		$this->mailer = $mailer;
		$this->order = $order;
		$this->emailName = $emailName;

		$this->validateType();
	}

	protected function validateType() {
		if (!key_exists($this->emailName, self::$EMAIL_NAME)) {
			throw new EmailBadTypeException;
		}
	}

	protected function getEmail() {
		switch ($this->emailName) {
			case 'new-order':
				$email = new NewOrder($this->em, $this->mailer, $this->order, self::$EMAIL_NAME[$this->emailName]);
				break;
			case 'waiting-for-payment':
				$email = new WaitingForPayment($this->em, $this->mailer, $this->order, self::$EMAIL_NAME[$this->emailName]);
				break;
			case 'paid':
				$email = new Paid($this->em, $this->mailer, $this->order, self::$EMAIL_NAME[$this->emailName]);
				break;
			case 'sent':
				$email = new Sent($this->em, $this->mailer, $this->order, self::$EMAIL_NAME[$this->emailName]);
				break;
			case 'sent-cod':
				$email = new SentCod($this->em, $this->mailer, $this->order, self::$EMAIL_NAME[$this->emailName]);
				break;
			case 'completed':
				$email = new Completed($this->em, $this->mailer, $this->order, self::$EMAIL_NAME[$this->emailName]);
				break;
		}

		return $email;
	}

	public function send() {
		$email = $this->getEmail();

		$email->sendMail();
	}

}
