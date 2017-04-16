<?php

namespace Nimleaf\Shopleaf\Vendor\Aukro;

use App\Model\Doc\Aukro as AukroModel;
use App\Model\Doc\Product;
use Doctrine\ORM\EntityManager;
use stdClass;
use \SoapClient;
use \SoapFault;

class Aukro {

	/** @var AukroModel */
	protected $aukroModel;

	/** @var EntityMananger */
	protected $em;

	/** @var string */
	protected $soapUrl = "http://webapi.aukro.cz/uploader.php?wsdl";

	public function __construct(EntityManager $em) {
		$this->aukroModel = AukroModel::getAukro($em);
		$this->em = $em;
	}

	public function doCheckNewAuctionExt(Product $product) {
		$sessionHandle = $this->doLoginEnc()['session-handle-part'];
		$fields = $this->newAuctionFields($product);
		$localId = $product->id;

		try {
			$client = new SoapClient($this->soapUrl, ['cache_wsdl' => WSDL_CACHE_NONE, 'exceptions' => true]);
			$call = $client->doCheckNewAuctionExt($sessionHandle, $fields, NULL, $localId);
		} catch (SoapFault $ex) {
			throw $ex;
		}

		return $call;
	}

	public function doGetCountries($countryCode) {
		try {
			$client = new SoapClient($this->soapUrl, ['cache_wsdl' => WSDL_CACHE_NONE, 'exceptions' => true]);
			$call = $client->doGetCountries($countryCode, $this->aukroModel->apiKey);
		} catch (SoapFault $ex) {
			throw $ex;
		}

		return $call;
	}

	public function doGetMyData() {
		$sessionHandle = $this->doLoginEnc()['session-handle-part'];

		try {
			$client = new SoapClient($this->soapUrl, ['cache_wsdl' => WSDL_CACHE_NONE, 'exceptions' => true]);
			$call = $client->doGetMyData($sessionHandle);
		} catch (SoapFault $ex) {
			throw $ex;
		}

		return $call;
	}

	public function doGetSellFormFieldsExt() {
		$apiKey = $this->aukroModel->apiKey;

		$component = 1; // AllegroWebAPI
		$country = $this->aukroModel->country;

		try {
			$client = new SoapClient($this->soapUrl, ['cache_wsdl' => WSDL_CACHE_NONE, 'exceptions' => true]);
			$call = $client->doGetSellFormFieldsExt($country, $component, $apiKey);
		} catch (SoapFault $ex) {
			throw $ex;
		}

		return $call;
	}
	public function doGetStatesInfo($countryCode, $stateCode = NULL) {
		try {
			$client = new SoapClient($this->soapUrl, ['cache_wsdl' => WSDL_CACHE_NONE, 'exceptions' => true]);
			$call = $client->doGetStatesInfo($countryCode, $this->aukroModel->apiKey);
		} catch (SoapFault $ex) {
			throw $ex;
		}

		return $call;
	}

	public function doLoginEnc() {
		$sysStatus = (array) $this->doQuerySysStatus();
		$username = $this->aukroModel->username;
		$password = base64_encode(hash('sha256', $this->aukroModel->password, true));
		$country = $this->aukroModel->country;
		$apiKey = $this->aukroModel->apiKey;
		$localVersion = $sysStatus['ver-key'];

		try {
			$client = new SoapClient($this->soapUrl, ['cache_wsdl' => WSDL_CACHE_NONE, 'exceptions' => true]);
			$call = $client->doLoginEnc($username, $password, $country, $apiKey, $localVersion);
		} catch (SoapFault $ex) {
			throw $ex;
		}

		return $call;
	}

	public function doMyBilling() {
		$sessionHandle = $this->doLoginEnc()['session-handle-part'];

		try {
			$client = new SoapClient($this->soapUrl, ['cache_wsdl' => WSDL_CACHE_NONE, 'exceptions' => true]);
			$call = $client->doMyBilling($sessionHandle);
		} catch (SoapFault $ex) {
			throw $ex;
		}

		return $call;
	}
	
	public function doVerifyItem($localId) {
		$sessionHandle = $this->doLoginEnc()['session-handle-part'];

		try {
			$client = new SoapClient($this->soapUrl, ['cache_wsdl' => WSDL_CACHE_NONE, 'exceptions' => true]);
			$call = $client->doVerifyItem($sessionHandle, $localId);
		} catch (SoapFault $ex) {
			throw $ex;
		}

		return $call;
	}

	public function doQuerySysStatus() {
		$apiKey = $this->aukroModel->apiKey;

		$component = 1; // AllegroWebAPI
		$country = $this->aukroModel->country;

		try {
			$client = new SoapClient($this->soapUrl, ['cache_wsdl' => WSDL_CACHE_NONE, 'exceptions' => true]);
			$call = $client->doQuerySysStatus($component, $country, $apiKey);
		} catch (SoapFault $ex) {
			throw $ex;
		}

		return $call;
	}
	
	protected function newAuctionFields(Product $product) {
		$myData = $this->doGetMyData();
		$userData = (array) $myData['user-data'];

		# inicializace proměnných
		$empty = new stdClass();
		$empty->{'fvalue-string'} = '';
		$empty->{'fvalue-int'} = 0;
		$empty->{'fvalue-float'} = 0;
		$empty->{'fvalue-image'} = ' ';
		$empty->{'fvalue-datetime'} = 0;
		$empty->{'fvalue-date'} = '';
		$empty->{'fvalue-range-int'} = [
			'fvalue-range-int-min' => 0,
			'fvalue-range-int-max' => 0,
		];
		$empty->{'fvalue-range-float'} = [
			'fvalue-range-float-min' => 0,
			'fvalue-range-float-max' => 0,
		];
		$empty->{'fvalue-range-date'} = [
			'fvalue-range-date-min' => '',
			'fvalue-range-date-max' => '',
		];
		$empty->{'fvalue-boolean'} = false;

		$form = [];

		# název importovaného produktu
		$field = clone $empty;
		$field->{'fid'} = 1;
		$field->{'fvalue-string'} = $product->name;
		$form[] = $field;
		
		# popis produktu
		$field = clone $empty;
		$field->{'fid'} = 24;
		$field->{'fvalue-string'} = $product->description;
		$form[] = $field;

		# kategorie, do které chceme vložit náš produkt
		$field = clone $empty;
		$field->{'fid'} = 2;
		$field->{'fvalue-int'} = $product->category->aukro->category;
		$form[] = $field;
		
		# Obrázek produktu
		$image = $product->getImageAbsoluteUrl($this->em, 'thumb');
//		$image = Image::fromFile($input_file);
		if (!empty($input_file)) {
			$field = clone $empty;
			$field->{'fid'} = 16;

			# otevřeme soubor obrázku
			$ifp = fopen($image, "rb");
			$image_data = fread($ifp, filesize($image));
			fclose($ifp);

			$field->{'fvalue-image'} = $image_data;
			$form[] = $field;
		}

		# počet položek daného produktu v nové aukci
		$field = clone $empty;
		$field->{'fid'} = 5;
		$field->{'fvalue-int'} = $product->cachePiece;
		$form[] = $field;

		# cena zboží, když bude aukce typu Kup teď!
		$field = clone $empty;
		$field->{'fid'} = 8;
		$field->{'fvalue-float'} = $product->getFinalPrice();
		$form[] = $field;
		
		# kód zboží
		$field = clone $empty;
		$field->{'fid'} = 3775;
		$field->{'fvalue-int'} = $product->id;
		$form[] = $field;
		
		# ean
		$field = clone $empty;
		$field->{'fid'} = 337;
		$field->{'fvalue-int'} = $product->id;
		$form[] = $field;
		
		# stav zboží
		$field = clone $empty;
		$field->{'fid'} = 2270;
		$field->{'fvalue-int'} = 1; //todo #nové
		$form[] = $field;
		
		# datum, kdy se má zveřejnit naše aukce
		$field = clone $empty;
		$field->{'fid'} = 3;
		$field->{'fvalue-datetime'} = time(); # zveřejníme rovnou
		$form[] = $field;

		# délka trvání aukce - 2 pro 7dní, 3 pro 10dní, 4 pro 14dní, 5 pro 30dní
		$field = clone $empty;
		$field->{'fid'} = 4;
		$field->{'fvalue-int'} = $this->aukroModel->days;
		$form[] = $field;

		# Po skončení automaticky opětovně vystavit položky
		# 0|1|2 - Opětovně nevystavovat | Všechny předměty | Pouze neprodané předměty
		$field = clone $empty;
		$field->{'fid'} = 30;
		$field->{'fvalue-int'} = 2; //todo
		$form[] = $field;
		
		# město
		$field = clone $empty;
		$field->{'fid'} = 11;
		$field->{'fvalue-string'} = $userData['user-city'];
		$form[] = $field;
		
		# PSČ místa, ve kterém prodáváme zboží
		$field = clone $empty;
		$field->{'fid'} = 32;
		$field->{'fvalue-string'} = $userData['user-postcode'];
		$form[] = $field;

		# ID kraje pro novou aukci 
		$field = clone $empty;
		$field->{'fid'} = 10;
		$field->{'fvalue-int'} = $userData['user-state-id'];
		$form[] = $field;

		# ID země pro novou aukci
		$field = clone $empty;
		$field->{'fid'} = 9;
		$field->{'fvalue-int'} = $userData['user-country-id'];
		$form[] = $field;

		# Kdo hradí náklady na dopravu
		$field = clone $empty;
		$field->{'fid'} = 12;
		$field->{'fvalue-int'} = $this->aukroModel->shippingForm;
		$form[] = $field;

		# Forma platby
		$field = clone $empty;
		$field->{'fid'} = 14;
		$field->{'fvalue-int'} = $this->aukroModel->paymentForm;
		$form[] = $field;

		# aukce bez přihazováni - pouze Kup teď!
		# 0 => aukce (s PayPal nebo Kup Teď! s omezeným počtem dnů)
		# 1 => aukce Kup Teď! s aktivnim Aukro Shopem
		$field = clone $empty;
		$field->{'fid'} = 29;
		$field->{'fvalue-int'} = 0;
		$form[] = $field;

		# přidání bezplatné přepravy - osobní převzetí
		$field = clone $empty;
		$field->{'fid'} = 35;
		$field->{'fvalue-int'} = 1;
		$form[] = $field;

		# Doporučená zásilka (první položka)
		$field = clone $empty;
		$field->{'fid'} = 42;
		$field->{'fvalue-float'} = 50.0; //todo
		$form[] = $field;

		# Doporučená zásilka (další položka)
		$field = clone $empty;
		$field->{'fid'} = 142;
		$field->{'fvalue-float'} = 0; //todo
		$form[] = $field;

		# Doporučená zásilka (množství v balení)
		$field = clone $empty;
		$field->{'fid'} = 242;
		$field->{'fvalue-float'} = 100; //todo
		$form[] = $field;

		# cena za poštovné - dobírka (první položka)
		$field = clone $empty;
		$field->{'fid'} = 43;
		$field->{'fvalue-float'} = 95.0; //todo
		$form[] = $field;
		
		# cena za poštovné - dobírka (další položka)
		$field = clone $empty;
		$field->{'fid'} = 143;
		$field->{'fvalue-float'} = 0; //todo
		$form[] = $field;
		
		# Doporučená zásilka (množství v balení)
		$field = clone $empty;
		$field->{'fid'} = 242;
		$field->{'fvalue-float'} = 100; //todo
		$form[] = $field;
		
		return $form;
	}

}
