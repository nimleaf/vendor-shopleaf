<?php

namespace Nimleaf\Shopleaf\Vendor\Aukro;

use Doctrine\ORM\EntityManager;

class MyAukro extends Aukro {

	public function __construct(EntityManager $em) {
		parent::__construct($em);
	}

	public function doGetStatesInfo($countryCode, $stateCode = NULL) {
		$call = parent::doGetStatesInfo($countryCode);

		if ($stateCode) {
			$items = (array) $call;
			foreach ($items as $item) {
				$c = (array) $item;
				if ($c['state-id'] === $stateCode) {
					return $c;
				}
			}
		}

		return $call;
	}

}
