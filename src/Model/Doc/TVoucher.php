<?php

namespace Nimleaf\Shopleaf\Model\Doc;

use Doctrine\ORM\EntityManager;

trait TVoucher {

	public static $DISCOUNT_TYPE = [
		'percentage' => "%",
		'amount' => "Kč"
	];

	/**
	 * @ORM\OneToMany(targetEntity="VoucherSold", mappedBy="voucher")
	 * @var VoucherSold aktivované kupóny
	 */
	protected $sold = [];

	/**
	 * @ORM\Column(type="string", nullable=true, unique=true)
	 * @var string $code
	 */
	protected $code;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var DateTime $dateFrom platnost od
	 */
	public $dateFrom;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var string $dateTo platnost do
	 */
	public $dateTo;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 * @var string $discount sleva
	 */
	public $discount;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string $discountType částka / procenta
	 */
	public $discountType;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * @var int $maximumUsage maximální počet použití
	 */
	public $maximumUsage;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 * @var string $minimalPrice minimální cena objednávky bez poštovného
	 */
	public $minimalPrice;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string $name
	 */
	public $name;

	/**
	 * @param EntityManager $em
	 * @param string $code
	 * @throws DuplicateVoucherException
	 */
	public function __construct(EntityManager $em, $code) {
		//validovat duplicitní kód
		try {
			self::validateDuplicate($em, $code);
		} catch (DuplicateVoucherException $ex) {
			throw $ex;
		}

		parent::__construct();
		$this->code = $code;
	}

	public static function validateDuplicate(EntityManager $em, $code) {
		$objects = $em->getRepository("App\Model\Doc\Voucher")->findBy(['code' => $code]);
		if ($objects) {
			throw new DuplicateVoucherException("Kupón s tímto kódem existuje.");
		}
	}

	public function getCode() {
		return $this->code;
	}

	/**
	 * Omezení použití kupónu
	 * @todo translate
	 * @return string
	 */
	public function getLimitations() {
		$limitations = [];
		if ($this->minimalPrice) {
			$limitations[] = "Minimální cena objednávky: " . $this->minimalPrice . " Kč";
		}
		return $limitations;
	}

	public function getTranslatedDiscount() {
		return '- ' . $this->discount . ' ' . self::$DISCOUNT_TYPE[$this->discountType];
	}

	/**
	 * 
	 * @todo translate
	 * @return type
	 */
	public function getTranslatedMaximumUsage() {
		return $this->maximumUsage == 0 ? 'neomezeně' : $this->maximumUsage . ' x';
	}

	/**
	 * 
	 * @todo translate
	 * @return type
	 */
	public function getTranslatedUsage() {
		return $this->maximumUsage == 0 ? $this->getUsages() . '/neomezeně' : $this->getUsages() . '/' . $this->maximumUsage;
	}

	public function getUsages() {
		return count($this->sold); 
	}

	/**
	 * @todo Translate
	 * @param float $orderPrice
	 * @param array $vouchers [Voucher, Voucher,...]
	 * @throws VoucherFuture
	 * @throws VoucherExpired
	 * @throws VoucherActivated
	 * @throws VoucherMinimalPrice
	 */
	public function validateVoucher($orderPrice, $vouchers) {
		$dateTo = $this->dateTo->modify('+1 day'); //platnost do půlnoci druhého dne

		if ($this->dateFrom > new \DateTime()) {
			throw new VoucherExeption("Kupón není zatím aktivní.");
		}
		if ($dateTo < new \DateTime()) {
			throw new VoucherExeption("Platnost kupónu vypršela.");
		}
		if ($this->maximumUsage != 0 && $this->maximumUsage <= $this->getUsages()) {
			throw new VoucherExeption("Kupón byl již aktivován.");
		}
		if ($this->minimalPrice > $orderPrice) {
			throw new VoucherExeption("Kupón nelze využít, objednávka nedosahuje požadované částky.");
		}
		if ($this->discountType == 'percentage') {
			foreach ($vouchers as $voucher) {
				if ($voucher->discountType == 'percentage') {
					throw new VoucherExeption("Kupóny s procentuální slevou nelze kombinovat.");
				}
			}
		}
		foreach ($vouchers as $voucher) {
			if ($this->id === $voucher->id) {
				throw new VoucherExeption("Kupón lze vložit pouze jednou.");
			}
		}
	}

}
