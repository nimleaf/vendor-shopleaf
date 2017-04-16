<?php

namespace NimExampleShopleaf\App\Model\Doc;

use Doctrine\ORM\Mapping as ORM;
use Nimleaf\Sandbox\Model\Doc\Base as NimleafBase;
use Nimleaf\Shopleaf\Model\Doc as Shopleaf;

/**
 * Základní model
 * @ORM\MappedSuperclass 
 */
abstract class Base extends NimleafBase {

	use Shopleaf\TBase;
}
