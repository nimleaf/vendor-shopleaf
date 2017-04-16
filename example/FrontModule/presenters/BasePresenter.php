<?php

namespace NimExampleShopleaf\App\FrontModule\Presenters;

use Nimleaf\Sandbox\FrontModule\Presenters\BasePresenter as NimleafBasePresenter;
use Nimleaf\Shopleaf\FrontModule\Presenters as Shopleaf;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends NimleafBasePresenter {

	use Shopleaf\TBasePresenter;
}
