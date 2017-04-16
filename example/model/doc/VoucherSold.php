<?php

namespace NimExampleShopleaf\App\Model\Doc;

use Doctrine\ORM\Mapping as ORM;
use Nimleaf\Shopleaf\Model\Doc as Shopleaf;

/**
 * Aktivovaný voucher
 * @ORM\Entity
 */
class VoucherSold extends Base {

	use Shopleaf\TVoucherSold;
}
