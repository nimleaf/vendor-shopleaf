<?php

namespace NimExampleShopleaf\App\Model\Doc;

use Doctrine\ORM\Mapping as ORM;
use Nimleaf\Sandbox\Model\Doc\Settings as NimleafSettings;
use Nimleaf\Shopleaf\Model\Doc as Shopleaf;

/**
 * @ORM\Entity
 */
class Settings extends NimleafSettings {

	use Shopleaf\TSettings;
}
