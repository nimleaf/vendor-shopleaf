<?php

namespace Nimleaf\Shopleaf\Model\Doc;

use App\Model\Doc\Address;
use App\Model\Doc\Contact;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Nimleaf\Editorial\Model\Doc as Editorial;

trait TUser {

	use Editorial\TUser;
	
	/**
	 * @ORM\OneToOne(targetEntity="Contact", cascade={"persist", "remove"})
	 * @var Contact kontaktní údaje
	 */
	protected $contact;

	/**
	 * @ORM\OneToOne(targetEntity="Address", cascade={"persist", "remove"})
	 * @var Address adresa
	 */
	protected $address;

	/**
	 * @param EntityManager $em
	 * @param string $email
	 * @throws DuplicateUserException
	 */
	public function __construct(EntityManager $em, $email, $password, $role) {
		parent::__construct($em, $email, $password, $role);
		
		$contact = new Contact;
		$contact->email = $email;
		$contact->save($em);
		
		$address = new Address;
		$address->save($em);
		
		$this->setContact($contact);
		$this->setAddress($address);
	}

	public function getAddress() {
		return $this->address;
	}

	public function getContact() {
		return $this->contact;
	}

	public function setAddress(Address $address) {
		$this->address = $address;
	}

	public function setContact(Contact $contact) {
		$this->contact = $contact;
	}

}
