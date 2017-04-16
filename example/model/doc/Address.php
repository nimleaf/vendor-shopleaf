<?php

namespace NimExampleShopleaf\App\Model\Doc;

use Doctrine\ORM\Mapping as ORM;
use Nimleaf\Shopleaf\Model\Doc as Shopleaf;

/**
 * @ORM\Entity
 */
class Address extends Base {

	use Shopleaf\TAddress;
}
