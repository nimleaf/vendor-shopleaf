<?php

namespace NimExampleShopleaf\App\Model\Doc;

use Doctrine\ORM\Mapping as ORM;
use Nimleaf\Shopleaf\Model\Doc as Shopleaf;

/**
 * Tagy produktů
 * @ORM\Entity
 */
class ProductTag extends Base {

	use Shopleaf\TProductTag;
}
