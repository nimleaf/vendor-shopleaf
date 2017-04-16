<?php

namespace NimExampleShopleaf\App\AdminModule\Form;

use Nimleaf\Sandbox\AdminModule\Form\BaseFormFactory as NimleafBaseFormFactory;
use Nimleaf\Shopleaf\AdminModule\Form as Shopleaf;

abstract class BaseFormFactory extends NimleafBaseFormFactory {

	use Shopleaf\TBaseFormFactory;
}
