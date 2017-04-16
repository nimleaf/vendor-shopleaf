<?php

namespace NimExampleShopleaf\App\Model\Doc;

use Doctrine\ORM\Mapping as ORM;
use Nimleaf\Shopleaf\Model\Doc as Shopleaf;

/**
 * @ORM\Entity
 * @ORM\Table(name="`order`")
 */
class Order extends Base {

	use Shopleaf\TOrder;
}
