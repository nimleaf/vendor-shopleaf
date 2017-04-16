<?php

namespace NimExampleShopleaf\App\Model\Doc;

use Doctrine\ORM\Mapping as ORM;
use Nimleaf\Shopleaf\Model\Doc as Shopleaf;

/**
 * Prodej produktu
 * @ORM\Entity
 */
class ProductSold extends Base {

	use Shopleaf\TProductSold;
}
