<?php

namespace NimExampleShopleaf\App\Model\Email;

use App\Model\Doc\Settings;
use Doctrine\ORM\EntityManager;
use Latte\Engine;
use Nette\Environment;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Templating\FileTemplate;

//todo imailer

abstract class Base {

	protected $name;

	/** @var EntityManager */
	protected $em;

	/** @var IMailer */
	protected $mailer;

	public function __construct(EntityManager $em, IMailer $mailer) {
		$this->em = $em;
		$this->mailer = $mailer;
	}

	abstract protected function getEmailTo();

	abstract protected function getFile();

	protected function getHtml() {
		$template = $this->template();
		return (string) $template;
	}

	protected function template() {
		$template = new FileTemplate($this->getFile());
		$template->registerFilter(new Engine());

		$template->basePath = Environment::getHttpRequest()->getUrl()->getBasePath();
		$template->pageSettings = Settings::getSettings($this->em);
		$template->name = $this->name;

		return $template;
	}

	public function send($attachment = NULL) {
		$settings = Settings::getSettings($this->em);

		$mail = new Message;
		$mail->setFrom($settings->email)
				->addTo($settings->email) //administrátor
				->addTo($this->getEmailTo()) //zákazník
				->setSubject($settings->name . ' :: ' . $this->name)
				->setHtmlBody($this->getHtml());
		
		if ($attachment !== NULL) {
			$mail->addAttachment('filename.pdf', $attachment);
		}

		$this->mailer->send($mail);
	}

}
