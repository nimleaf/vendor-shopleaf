<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

use App\Model\Doc\Address;
use App\Model\Doc\User;
use Nette\Application\UI\Form;

trait TUserCreateFormFactory {

	protected function applyValues(User $user, $values) {
		$address = new Address;
		$user->setAddress($address);

		$user->email = $values->email;
		if (isset($values->password)) {
			$user->password = User::hashPassword($values->password);
		}
		$user->role = $values->role;
		$user->address->name = $values->name;
		$user->address->surname = $values->surname;
		$user->address->street = $values->street;
		$user->address->town = $values->town;
		$user->address->zip = $values->zip;
		$user->contact->phone = $values->phone;
		if ($this->createFormValidator()) {
			$user->source = User::$SOURCE['added-by-admin'];
		}
		
		$user->save($this->em);
	}

	public function create() {
		$role = User::$ROLE;
		if ($this->editFormValidator()) {
			$userSource = $this->user->source;
		} else {
			$userSource = '';
		}

		$form = parent::createForm();

		$form->addGroup();

		$form->addText('name', _("jméno"))
				->setRequired();
		$form->addText('surname', _("příjmení"))
				->setRequired();
		$form->addText('email', _("e-mail"))
				->setRequired();
		$form->addText('phone', _("telefon"));
		$form->addPassword('password', _("Heslo"))
				->setOption('description', _("Pokud svůj profil editujete a nechcete měnit heslo, ponechte toto pole prázdné."))
				->addCondition(Form::FILLED)
				->addRule(Form::MIN_LENGTH, _("Heslo musí obsahovat minimálně %d znaků."), 6)
				->addCondition(callback($this, "createFormValidator"))
				->addRule(Form::MIN_LENGTH, _("Heslo musí obsahovat minimálně %d znaků."), 6);
		$form->addPassword('password2', _("Heslo znovu"))
				->addConditionOn($form['password'], Form::VALID)
				->addRule(Form::EQUAL, _("Hesla se neshodují."), $form['password']);
		$form->addSelect('role', _("role"), $role)
				->setAttribute('class', "form-control");
		$form->addText('source', _("zdroj registrace"))
				->setDisabled()
				->setValue($userSource);

		$form->addGroup();
		$form->addText('street', _("ulice"));
		$form->addText('town', _("město"));
		$form->addText('zip', _("PSČ"));
		$form->addSubmit('send', _("Uložit"));

		$form->setDefaults($this->getDefaults());

		return $form;
	}

}
