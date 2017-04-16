<?php

namespace NimExampleShopleaf\App\AdminModule\Presenters;

use Nimleaf\Sandbox\AdminModule\Presenters\UserPresenter as NimleafUserPresenter;
use Nimleaf\Shopleaf\AdminModule\Presenters as Shopleaf;

class UserPresenter extends NimleafUserPresenter {

	use Shopleaf\TUserPresenter;
}
