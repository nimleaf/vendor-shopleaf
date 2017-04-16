<?php

namespace NimExampleShopleaf\App\AdminModule\Presenters;

use Nimleaf\Sandbox\AdminModule\Presenters\BasePresenter as NimleafBasePresenter;
use Nimleaf\Shopleaf\AdminModule\Presenters as Shopleaf;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends NimleafBasePresenter {

	use Shopleaf\TBasePresenter;
}
