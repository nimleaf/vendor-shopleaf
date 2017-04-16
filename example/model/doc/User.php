<?php

namespace NimExampleShopleaf\App\Model\Doc;

use Doctrine\ORM\Mapping as ORM;
use Nimleaf\Sandbox\Model\Doc\User as NimleafUser;
use Nimleaf\Shopleaf\Model\Doc as Shopleaf;

/**
 * @ORM\Entity
 */
class User extends NimleafUser {
	
	use Shopleaf\TUser;

	public static $ROLE = [
		'superadmin' => "super admin",
		'admin' => "administrátor",
		'customer' => "host",
	];

}
