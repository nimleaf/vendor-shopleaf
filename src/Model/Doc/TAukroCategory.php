<?php

namespace Nimleaf\Shopleaf\Model\Doc;

trait TAukroCategory {

	//todo stahovat automaticky
	public static $CATEGORY = [
		90736 => "Móda a doplňky - Šperky a Hodinky - Bižuterie - Ostatní",
		90737 => "Móda a doplňky - Šperky a Hodinky - Bižuterie - Náhrdelníky",
		90738 => "Móda a doplňky - Šperky a Hodinky - Bižuterie - Řetízky",
		90739 => "Móda a doplňky - Šperky a Hodinky - Bižuterie - Náramky",
		90741 => "Móda a doplňky - Šperky a Hodinky - Bižuterie - Prsteny",
		90742 => "Móda a doplňky - Šperky a Hodinky - Bižuterie - Přívěsky",
		90744 => "Móda a doplňky - Šperky a Hodinky - Bižuterie - Soupravy",
		90795 => "Móda a doplňky - Šperky a Hodinky - Bižuterie - Náušnice - Ostatní",
		90796 => "Móda a doplňky - Šperky a Hodinky - Bižuterie - Náušnice - Klipsy",
		90797 => "Móda a doplňky - Šperky a Hodinky - Bižuterie - Náušnice - Kruhy",
		90798 => "Móda a doplňky - Šperky a Hodinky - Bižuterie - Náušnice - Pecičky",
		90799 => "Móda a doplňky - Šperky a Hodinky - Bižuterie - Náušnice - Vysací",
	];

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string ID kategorie na Aukro.cz
	 */
	public $category;
	
	public function __construct() {
		parent::__construct();
	}

}
