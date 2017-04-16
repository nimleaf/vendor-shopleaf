<?php

/** přidat do menu následující:


$adminnemu = [
	'orders' => [
		'title' => "Objednávky",
		'items' => [
			[
				"Správa objednávek", "Order:", "fa-cart-arrow-down",
				'action' => [
					'view' => "Souhrn objednávky",
				],
			],
			[
				"Způsoby dopravy", "Shipping:", "fa-truck",
				'action' => [
					'edit' => "Úprava způsobu dopravy",
					'create' => "Nová možnost způsobu dopravy",
				],
			],
			[
				"Možnosti platby", "Payment:", "fa-money",
				'action' => [
					'edit' => "Úprava platby",
					'create' => "Nová možnost platby",
				],
			],
		],
	],
	'products' => [
		'title' => "Produkty",
		'items' => [
			[
				"Sklad", "Product:", "fa-archive",
				'action' => [
					'edit' => "Úprava produktu",
					'create' => "Nový produkt",
					'purchaseCreate' => "Nový nákup",
					'purchaseEdit' => "Úprava nákupu",
					'correctionCreate' => "Nová korekce",
					'correctionEdit' => "Úprava korekce",
				],
			],
			[
				"Kategorie produktů", "ProductCategory:", "fa-navicon",
				'action' => [
					'edit' => "Úprava kategorie",
					'create' => "Nová kategorie",
				],
			],
			[
				"Tagy", "ProductTag:", "fa-tags",
				'action' => [
					'edit' => "Úprava tagu",
					'create' => "Nový tag",
				],
			],
		],
	],
	'advertising' => [
		'title' => "Propagace",
		'items' => [
			[
				"Kupóny", "Voucher:", "fa-barcode",
				'action' => [
					'edit' => "Úprava kupónu",
					'create' => "Nový kupón",
				],
			],
		],
	],
	'display' => [
		'title' => "Zobrazení",
		'items' => [
			[
				"Banery", "Banner:", "fa-file-o",
				'action' => [
					'edit' => "Úprava baneru",
				],
			],
		],
	],
	'generally' => [
		'title' => "Nastavení",
		'items' => [
			[
				"Aukro", "Aukro:", "fa-cog",
			],
			[
				"Obchod", "SettingsShop:", "fa-cog",
			],
			[
				"XML Feed", ":Front:Xml:heureka", "fa-external-link-square",
			],
		],
	],
];
