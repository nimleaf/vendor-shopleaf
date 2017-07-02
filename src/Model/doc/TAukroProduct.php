<?php

namespace Nimleaf\Shopleaf\Model\Doc;

trait TAukroProduct {

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string ID vystavené aukce
	 */
	public $auctionId;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string ID vystavené aukce - lokální
	 */
	public $localId;
	
	public function __toString() {
		return $this->auctionId ? $this->auctionId : '';
	}
	
	public function getAuctionPreviewUrl() {
		return "http://aukro.cz/show_item.php?item=" . $this->auctionId;
	}

}
