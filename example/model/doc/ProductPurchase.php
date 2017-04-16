<?php

namespace NimExampleShopleaf\App\Model\Doc;

use Doctrine\ORM\Mapping as ORM;
use Nimleaf\Shopleaf\Model\Doc as Shopleaf;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ProductPurchase extends Base {

	use Shopleaf\TProductPurchase;
}
