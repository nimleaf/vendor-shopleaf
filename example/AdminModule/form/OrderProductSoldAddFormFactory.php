<?php

namespace NimExampleShopleaf\App\AdminModule\Form;

use Nimleaf\Shopleaf\AdminModule\Form as Shopleaf;

/**
 * Přidá produkt do vytvořené objednávky
 * 2 krokový formulář
 */
class OrderProductSoldAddFormFactory extends BaseFormFactory {

	use Shopleaf\TOrderProductSoldAddFormFactory;
}
