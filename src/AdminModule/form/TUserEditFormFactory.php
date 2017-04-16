<?php

namespace Nimleaf\Shopleaf\AdminModule\Form;

trait TUserEditFormFactory {

	protected function getDefaults() {
		$user = $this->user;
		
		$defaults = [
			'name' => $user->address->name,
			'surname' => $user->address->surname,
			'street' => $user->address->street,
			'town' => $user->address->town,
			'zip' => $user->address->zip,
			'email' => $user->contact->email,
			'phone' => $user->contact->phone,
			'role' => $user->role,
		];

		return $defaults;
	}

}
