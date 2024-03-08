<?php
use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use Automattic\WooCommerce\Utilities\OrderUtil as CedEtsyHPOS;
class Ced_Order_Get {

	public static $_instance;
	private       $create_in_hpos;
	/**
	 * Ced_Etsy_Config Instance.
	 *
	 * Ensures only one instance of Ced_Etsy_Config is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 */

	public $is_sync = false;
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct($shop_name = ''){
	  $this->shop_name                  = !empty( $shop_name ) ? $shop_name : '';
	  $this->saved_global_settings_data = get_option( 'ced_etsy_global_settings', array());
	  $this->create_in_hpos             = false;

	}

	/**
	 * Fetch order from Etsy.
	 *
	 * @since 1.0.0
	 */

	public function get_orders( $shopId, $is_sync = false ) {
		if ( CedEtsyHPOS::custom_orders_table_usage_is_enabled() ) {
			$this->create_in_hpos = true;
		}
		$this->is_sync                    = $is_sync;
		$shop_id                          = get_etsy_shop_id( $shopId );
		$last_created_order               = get_option( 'ced_etsy_last_order_created_time', '' );
		$last_created_order               = date_i18n( 'F d, Y h:i', strtotime( $last_created_order ) );
		$current_time                     = current_time( 'F-i-j h:i:s' );
		$this->saved_global_settings_data = get_option( 'ced_etsy_global_settings', '' );
		$order_limit                      = isset( $this->saved_global_settings_data[ $shopId ]['order_limit'] ) ? $this->saved_global_settings_data[ $shopId ]['order_limit'] : '';

		$params = array(
			'limit'        => ! empty( $order_limit ) ? (int) $order_limit : 15,
			'was_paid'     => true,
			'offset'       => 0,
			'was_shipped'  => false,
			'was_canceled' => false,
		);

		/** Refresh token
		 *
		 * @since 2.0.0
		 */
		do_action( 'ced_etsy_refresh_token', $shopId );
		$result = etsy_request()->get( "application/shops/{$shop_id}/receipts", $shopId, $params );
		$result = json_decode( '{
							"count": 3064,
							"results": [{
								"receipt_id": 2750906110,
								"receipt_type": 0,
								"seller_user_id": 288298098,
								"seller_email": "padacosta@gmail.com",
								"buyer_user_id": 735542660,
								"buyer_email": "csdonovan@y7mail.com",
								"name": "craig donovan",
								"first_line": "32 Natan Rd",
								"second_line": null,
								"city": "MUDGEERABA",
								"state": "QLD",
								"zip": "4213",
								"status": "Paid",
								"formatted_address": "craig donovan\n32 Natan Rd\nMUDGEERABA QLD 4213\nAustralia",
								"country_iso": "AU",
								"payment_method": "cc",
								"payment_email": "",
								"message_from_payment": null,
								"message_from_seller": null,
								"message_from_buyer": "",
								"is_shipped": false,
								"is_paid": true,
								"create_timestamp": 1672732903,
								"created_timestamp": 1672732903,
								"update_timestamp": 1672732925,
								"updated_timestamp": 1672732925,
								"is_gift": false,
								"gift_message": "",
								"grandtotal": {
									"amount": 61500,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"subtotal": {
									"amount": 46500,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_price": {
									"amount": 46500,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_shipping_cost": {
									"amount": 15000,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_tax_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_vat_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"discount_amt": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"gift_wrap_price": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"shipments": [],
								"transactions": [{
									"transaction_id": 3371087994,
									"title": "Large Oval Bird Cage with Stand \/ Bird House \/ Canary Cage \/ Parakeet Cage",
									"description": "Large Oval Bird Cage with Stand\n\nGive to your lovely bird a new and large cage with stand, with one attractive designe that you can easily add in you home decor. \nThis cage is all produce handy, using material with great quality, we use real wood treated and galvanized wire.\n\nFeatures of the cage:\n- Two spring-loaded front doors and a lock;\n- Four plastic feeders, two on each side;\n- A waste drawer with a lock for easy cleaning of the cage floor;\n- A ring on top of the cage\n\nArtisan Made \nMade in Portugal\n\nCage Dimensions: \nL - 55 cm, W - 48 cm, H - 76 cm\nL - 21,7 in, W - 18,9 in, H - 29,9 in\n\nSpacing between wires - 1,1 cm; 0,4 in\n\nStand Dimensions: \nL - 64 cm, W - 56 cm, H - 90 cm\nL - 25,2 in, W - 22,0 in, H - 35,4 in\n\n\nOptions:\nCage + Stand__Light\t\n  - Cage and stand (Natural Wood) more 2 straw baskets,\n\nCage __Light\t\n  - Cage (Natural Wood),\t\n\nCage + Stand__Brown\n  - Cage and stand (Brown) more 2 straw baskets,\t\n\t\nCage __Brown\n  - Cage (Brown)\n\nWe need 2 -5 weeks to produce this cage after order.\n\nThis product is careful packed to make sure that they arrives in good conditions in your home.\nThe dimensions may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 735542660,
									"create_timestamp": 1672732903,
									"created_timestamp": 1672732903,
									"paid_timestamp": 1672732925,
									"shipped_timestamp": null,
									"quantity": 1,
									"listing_image_id": 3400203712,
									"receipt_id": 2750906110,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1077116959,
									"sku": "Woo-beanie-logo",
									"stock":5,
									"product_status":"in stock",
									"product_id": 434775,
									"transaction_type": "listing",
									"price": {
										"amount": 46500,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [{
										"property_id": 513,
										"value_id": 1034297440232,
										"formatted_name": "Option",
										"formatted_value": "Cage __Brown"
									}],
									"product_data": [{
										"property_id": 513,
										"property_name": "Option",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [1034297440232],
										"values": ["Cage __Brown"]
									}],
									"shipping_profile_id": 189944640304,
									"min_processing_days": 10,
									"max_processing_days": 25,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1675756925,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}],
								"refunds": []
							}, {
								"receipt_id": 2746298241,
								"receipt_type": 0,
								"seller_user_id": 288298098,
								"seller_email": "padacosta@gmail.com",
								"buyer_user_id": 739625037,
								"buyer_email": "carlosalbertoh@hotmail.com",
								"name": "CARLOS ALBERTO HERNANDEZ ORDONEZ",
								"first_line": "CALLE DE RUIZ PERELLO 13",
								"second_line": "PRIMERO D",
								"city": "MADRID",
								"state": "MADRID",
								"zip": "28028",
								"status": "Completed",
								"formatted_address": "CARLOS ALBERTO HERNANDEZ ORDONEZ\nCALLE DE RUIZ PERELLO 13\nPRIMERO D\n28028 MADRID MADRID\nSpain",
								"country_iso": "ES",
								"payment_method": "cc",
								"payment_email": "",
								"message_from_payment": null,
								"message_from_seller": "",
								"message_from_buyer": "",
								"is_shipped": true,
								"is_paid": true,
								"create_timestamp": 1672684341,
								"created_timestamp": 1672684341,
								"update_timestamp": 1672698331,
								"updated_timestamp": 1672698331,
								"is_gift": false,
								"gift_message": "",
								"grandtotal": {
									"amount": 6410,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"subtotal": {
									"amount": 4140,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_price": {
									"amount": 4140,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_shipping_cost": {
									"amount": 2270,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_tax_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_vat_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"discount_amt": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"gift_wrap_price": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"shipments": [{
									"receipt_shipping_id": 1129375900885,
									"shipment_notification_timestamp": 1672678800,
									"carrier_name": "GLS",
									"tracking_code": "43513772974760"
								}],
								"transactions": [{
									"transaction_id": 3373269675,
									"title": "Ceramic Glazed Sardines \/ Ceramic Sardine \/ Traditional Sardine \/ Ceramic Hanging Sardine \/ Sardine Art \/ Sardine Gifts \/ Ceramic Art",
									"description": "Ceramic Glazed Sardines\n\nArtisan Made\nMade in Portugal\n\nDimensions:\nSmall Size\nL - 15 cm, W - 2,5 cm, H - 3,5 cm\nL - 5,9 in, W - 1,0 in, H - 1,4 in\n\nMedium Size\nL - 16 cm, W - 2,5 cm, H - 4 cm\nL - 6,3 in, W - 1,0 in, H - 1,6 in\n\nLarge Size\nL - 20 cm, W - 3 cm, H - 5 cm\nL - 7,9 in, W - 1,2 in, H - 2,0 in\n\nThey have an opening on the back to hang on the wall.\nThe dimensions may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 739625037,
									"create_timestamp": 1672684341,
									"created_timestamp": 1672684341,
									"paid_timestamp": 1672684442,
									"shipped_timestamp": 1672698331,
									"quantity": 1,
									"listing_image_id": 4256801030,
									"receipt_id": 2746298241,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1272611522,
									"stock":3,
									"product_status":"out of stock",
									"sku": "VHCR01GLA",
									"product_id": 434771,
									"transaction_type": "listing",
									"price": {
										"amount": 690,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [{
										"property_id": 513,
										"value_id": 107366603999,
										"formatted_name": "Color",
										"formatted_value": "Blue"
									}, {
										"property_id": 514,
										"value_id": 449166994237,
										"formatted_name": "Size",
										"formatted_value": "Large"
									}],
									"product_data": [{
										"property_id": 513,
										"property_name": "Color",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [107366603999],
										"values": ["Blue"]
									}, {
										"property_id": 514,
										"property_name": "Size",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [449166994237],
										"values": ["Large"]
									}],
									"shipping_profile_id": 133228652547,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672698331,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}, {
									"transaction_id": 3373269671,
									"title": "Ceramic Glazed Sardines \/ Ceramic Sardine \/ Traditional Sardine \/ Ceramic Hanging Sardine \/ Sardine Art \/ Sardine Gifts \/ Ceramic Art",
									"description": "Ceramic Glazed Sardines\n\nArtisan Made\nMade in Portugal\n\nDimensions:\nSmall Size\nL - 15 cm, W - 2,5 cm, H - 3,5 cm\nL - 5,9 in, W - 1,0 in, H - 1,4 in\n\nMedium Size\nL - 16 cm, W - 2,5 cm, H - 4 cm\nL - 6,3 in, W - 1,0 in, H - 1,6 in\n\nLarge Size\nL - 20 cm, W - 3 cm, H - 5 cm\nL - 7,9 in, W - 1,2 in, H - 2,0 in\n\nThey have an opening on the back to hang on the wall.\nThe dimensions may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 739625037,
									"create_timestamp": 1672684341,
									"created_timestamp": 1672684341,
									"paid_timestamp": 1672684442,
									"shipped_timestamp": 1672698331,
									"quantity": 1,
									"listing_image_id": 4304195305,
									"receipt_id": 2746298241,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1272611522,
									"sku": "Woo-tshirt-logo",
									"stock":2,
									"product_status":"in stock",
									"product_id": 434741,
									"transaction_type": "listing",
									"price": {
										"amount": 690,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [{
										"property_id": 513,
										"value_id": 238513113165,
										"formatted_name": "Color",
										"formatted_value": "Dark Blue"
									}, {
										"property_id": 514,
										"value_id": 449166994237,
										"formatted_name": "Size",
										"formatted_value": "Large"
									}],
									"product_data": [{
										"property_id": 513,
										"property_name": "Color",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [238513113165],
										"values": ["Dark Blue"]
									}, {
										"property_id": 514,
										"property_name": "Size",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [449166994237],
										"values": ["Large"]
									}],
									"shipping_profile_id": 133228652547,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672698331,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}, {
									"transaction_id": 3370080690,
									"title": "Ceramic Glazed Sardines \/ Ceramic Sardine \/ Traditional Sardine \/ Ceramic Hanging Sardine \/ Sardine Art \/ Sardine Gifts \/ Ceramic Art",
									"description": "Ceramic Glazed Sardines\n\nArtisan Made\nMade in Portugal\n\nDimensions:\nSmall Size\nL - 15 cm, W - 2,5 cm, H - 3,5 cm\nL - 5,9 in, W - 1,0 in, H - 1,4 in\n\nMedium Size\nL - 16 cm, W - 2,5 cm, H - 4 cm\nL - 6,3 in, W - 1,0 in, H - 1,6 in\n\nLarge Size\nL - 20 cm, W - 3 cm, H - 5 cm\nL - 7,9 in, W - 1,2 in, H - 2,0 in\n\nThey have an opening on the back to hang on the wall.\nThe dimensions may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 739625037,
									"create_timestamp": 1672684341,
									"created_timestamp": 1672684341,
									"paid_timestamp": 1672684442,
									"shipped_timestamp": 1672698331,
									"quantity": 1,
									"listing_image_id": 4256800930,
									"receipt_id": 2746298241,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1272611522,
									"sku": "woo-single",
									"stock":1,
									"product_status":"out of stock",
									"product_id": 434736,
									"transaction_type": "listing",
									"price": {
										"amount": 690,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [{
										"property_id": 513,
										"value_id": 104227319054,
										"formatted_name": "Color",
										"formatted_value": "Yellow"
									}, {
										"property_id": 514,
										"value_id": 449166994237,
										"formatted_name": "Size",
										"formatted_value": "Large"
									}],
									"product_data": [{
										"property_id": 513,
										"property_name": "Color",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [104227319054],
										"values": ["Yellow"]
									}, {
										"property_id": 514,
										"property_name": "Size",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [449166994237],
										"values": ["Large"]
									}],
									"shipping_profile_id": 133228652547,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672698331,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}, {
									"transaction_id": 3370080688,
									"title": "Ceramic Glazed Sardines \/ Ceramic Sardine \/ Traditional Sardine \/ Ceramic Hanging Sardine \/ Sardine Art \/ Sardine Gifts \/ Ceramic Art",
									"description": "Ceramic Glazed Sardines\n\nArtisan Made\nMade in Portugal\n\nDimensions:\nSmall Size\nL - 15 cm, W - 2,5 cm, H - 3,5 cm\nL - 5,9 in, W - 1,0 in, H - 1,4 in\n\nMedium Size\nL - 16 cm, W - 2,5 cm, H - 4 cm\nL - 6,3 in, W - 1,0 in, H - 1,6 in\n\nLarge Size\nL - 20 cm, W - 3 cm, H - 5 cm\nL - 7,9 in, W - 1,2 in, H - 2,0 in\n\nThey have an opening on the back to hang on the wall.\nThe dimensions may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 739625037,
									"create_timestamp": 1672684341,
									"created_timestamp": 1672684341,
									"paid_timestamp": 1672684442,
									"shipped_timestamp": 1672698331,
									"quantity": 1,
									"listing_image_id": 4256800932,
									"receipt_id": 2746298241,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1272611522,
									"sku": "woo-album",
									"stock":1,
									"product_status":"out of stock",
									"product_id": 434732,
									"transaction_type": "listing",
									"price": {
										"amount": 690,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [{
										"property_id": 513,
										"value_id": 1087254099812,
										"formatted_name": "Color",
										"formatted_value": "Mixed Blue"
									}, {
										"property_id": 514,
										"value_id": 449166994237,
										"formatted_name": "Size",
										"formatted_value": "Large"
									}],
									"product_data": [{
										"property_id": 513,
										"property_name": "Color",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [1087254099812],
										"values": ["Mixed Blue"]
									}, {
										"property_id": 514,
										"property_name": "Size",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [449166994237],
										"values": ["Large"]
									}],
									"shipping_profile_id": 133228652547,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672698331,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}, {
									"transaction_id": 3370080686,
									"title": "Ceramic Glazed Sardines \/ Ceramic Sardine \/ Traditional Sardine \/ Ceramic Hanging Sardine \/ Sardine Art \/ Sardine Gifts \/ Ceramic Art",
									"description": "Ceramic Glazed Sardines\n\nArtisan Made\nMade in Portugal\n\nDimensions:\nSmall Size\nL - 15 cm, W - 2,5 cm, H - 3,5 cm\nL - 5,9 in, W - 1,0 in, H - 1,4 in\n\nMedium Size\nL - 16 cm, W - 2,5 cm, H - 4 cm\nL - 6,3 in, W - 1,0 in, H - 1,6 in\n\nLarge Size\nL - 20 cm, W - 3 cm, H - 5 cm\nL - 7,9 in, W - 1,2 in, H - 2,0 in\n\nThey have an opening on the back to hang on the wall.\nThe dimensions may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 739625037,
									"create_timestamp": 1672684341,
									"created_timestamp": 1672684341,
									"paid_timestamp": 1672684442,
									"shipped_timestamp": 1672698331,
									"quantity": 1,
									"listing_image_id": 4256800910,
									"receipt_id": 2746298241,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1272611522,
									"sku": "woo-polo",
									"stock":1,
									"product_status":"out of stock",
									"product_id": 434728,
									"transaction_type": "listing",
									"price": {
										"amount": 690,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [{
										"property_id": 513,
										"value_id": 107366604011,
										"formatted_name": "Color",
										"formatted_value": "Orange"
									}, {
										"property_id": 514,
										"value_id": 449166994237,
										"formatted_name": "Size",
										"formatted_value": "Large"
									}],
									"product_data": [{
										"property_id": 513,
										"property_name": "Color",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [107366604011],
										"values": ["Orange"]
									}, {
										"property_id": 514,
										"property_name": "Size",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [449166994237],
										"values": ["Large"]
									}],
									"shipping_profile_id": 133228652547,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672698331,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}, {
									"transaction_id": 3370080682,
									"title": "Ceramic Glazed Sardines \/ Ceramic Sardine \/ Traditional Sardine \/ Ceramic Hanging Sardine \/ Sardine Art \/ Sardine Gifts \/ Ceramic Art",
									"description": "Ceramic Glazed Sardines\n\nArtisan Made\nMade in Portugal\n\nDimensions:\nSmall Size\nL - 15 cm, W - 2,5 cm, H - 3,5 cm\nL - 5,9 in, W - 1,0 in, H - 1,4 in\n\nMedium Size\nL - 16 cm, W - 2,5 cm, H - 4 cm\nL - 6,3 in, W - 1,0 in, H - 1,6 in\n\nLarge Size\nL - 20 cm, W - 3 cm, H - 5 cm\nL - 7,9 in, W - 1,2 in, H - 2,0 in\n\nThey have an opening on the back to hang on the wall.\nThe dimensions may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 739625037,
									"create_timestamp": 1672684340,
									"created_timestamp": 1672684340,
									"paid_timestamp": 1672684442,
									"shipped_timestamp": 1672698331,
									"quantity": 1,
									"listing_image_id": 4256800922,
									"receipt_id": 2746298241,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1272611522,
									"sku": "woo-long-sleeve-tee",
									"stock":1,
									"product_status":"out of stock",
									"product_id": 434716,
									"transaction_type": "listing",
									"price": {
										"amount": 690,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [{
										"property_id": 513,
										"value_id": 1109670132293,
										"formatted_name": "Color",
										"formatted_value": "Teal Blue"
									}, {
										"property_id": 514,
										"value_id": 449166994237,
										"formatted_name": "Size",
										"formatted_value": "Large"
									}],
									"product_data": [{
										"property_id": 513,
										"property_name": "Color",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [1109670132293],
										"values": ["Teal Blue"]
									}, {
										"property_id": 514,
										"property_name": "Size",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [449166994237],
										"values": ["Large"]
									}],
									"shipping_profile_id": 133228652547,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672698331,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}],
								"refunds": []
							}, {
								"receipt_id": 2749861120,
								"receipt_type": 0,
								"seller_user_id": 288298098,
								"seller_email": "padacosta@gmail.com",
								"buyer_user_id": 269315592,
								"buyer_email": "nora_knieps@yahoo.de",
								"name": "Nora Stock",
								"first_line": "Lessingstr. 5",
								"second_line": "",
								"city": "Bad Neuenahr Ahrweiler",
								"state": null,
								"zip": "53474",
								"status": "Completed",
								"formatted_address": "Nora Stock\nLessingstr. 5\n53474 BAD NEUENAHR AHRWEILER\nGermany",
								"country_iso": "DE",
								"payment_method": "cc",
								"payment_email": "",
								"message_from_payment": null,
								"message_from_seller": "",
								"message_from_buyer": "",
								"is_shipped": true,
								"is_paid": true,
								"create_timestamp": 1672666054,
								"created_timestamp": 1672666054,
								"update_timestamp": 1672696353,
								"updated_timestamp": 1672696353,
								"is_gift": false,
								"gift_message": "",
								"grandtotal": {
									"amount": 7132,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"subtotal": {
									"amount": 5132,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_price": {
									"amount": 5132,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_shipping_cost": {
									"amount": 2000,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_tax_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_vat_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"discount_amt": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"gift_wrap_price": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"shipments": [{
									"receipt_shipping_id": 1106904733836,
									"shipment_notification_timestamp": 1672678800,
									"carrier_name": "GLS",
									"tracking_code": "Z00384PP"
								}],
								"transactions": [{
									"transaction_id": 3369712508,
									"title": "Runner Rug Dark Green 200cm \/ Kitchen Rug \/ Rug Runner \/ Floor Mat \/ Boho Rug \/ Washable Rug \/ Soft Rug \/ Rag Rug \/ Scandinavian Rug",
									"description": "Long Runner Rug Dark Green 200cm\n(color #29 - dark green)\n\nThis rug is woven in a manual loom with fabrics reused.\nThese rugs are robust, light, and can be regularly washed by hand or machine.\nIt is ecological because they reuse raw material wasted by the textil industry. \nWe recommend cold washing or to 30\u00b0C.\nSince this rug is made with recycled products from the textile industries, there may be slight variations in color.\n\nArtisan Made\nMade in Portugal\n\nDimensions:\nL - 200 cm, W - 60 cm\nL - 78,7 in, W - 23,2 in\nL - 6,6 ft, W - 2,0 ft\n\nThe dimensions may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 269315592,
									"create_timestamp": 1672666054,
									"created_timestamp": 1672666054,
									"paid_timestamp": 1672666102,
									"shipped_timestamp": 1672696353,
									"quantity": 1,
									"listing_image_id": 3540518735,
									"receipt_id": 2749861120,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1113842534,
									"sku": "woo-hoodie-with-zipper",
									"stock":2,
									"product_status":"out of stock",
									"product_id": 434712,
									"transaction_type": "listing",
									"price": {
										"amount": 5132,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [{
										"property_id": 500,
										"value_id": 261371258949,
										"formatted_name": "Finish",
										"formatted_value": "With Fringes"
									}, {
										"property_id": 513,
										"value_id": 164404156362,
										"formatted_name": "Size",
										"formatted_value": "80x160cm"
									}],
									"product_data": [{
										"property_id": 500,
										"property_name": "Finish",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [261371258949],
										"values": ["With Fringes"]
									}, {
										"property_id": 513,
										"property_name": "Size",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [164404156362],
										"values": ["80x160cm"]
									}],
									"shipping_profile_id": 161823185692,
									"min_processing_days": 1,
									"max_processing_days": 2,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672696353,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}],
								"refunds": []
							}, {
								"receipt_id": 2749379860,
								"receipt_type": 0,
								"seller_user_id": 288298098,
								"seller_email": "padacosta@gmail.com",
								"buyer_user_id": 211275821,
								"buyer_email": "kathryninez.oakley@gmail.com",
								"name": "Kathryn campbell",
								"first_line": "1061 Northeast 9th Avenue",
								"second_line": "Apt 1319",
								"city": "Portland",
								"state": "OR",
								"zip": "97232",
								"status": "Completed",
								"formatted_address": "Kathryn campbell\n1061 Northeast 9th Avenue\nApt 1319\nPORTLAND, OR 97232\nUnited States",
								"country_iso": "US",
								"payment_method": "cc",
								"payment_email": "",
								"message_from_payment": null,
								"message_from_seller": null,
								"message_from_buyer": "",
								"is_shipped": true,
								"is_paid": true,
								"create_timestamp": 1672614172,
								"created_timestamp": 1672614172,
								"update_timestamp": 1672696169,
								"updated_timestamp": 1672696169,
								"is_gift": false,
								"gift_message": "",
								"grandtotal": {
									"amount": 9918,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"subtotal": {
									"amount": 4951,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_price": {
									"amount": 4951,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_shipping_cost": {
									"amount": 4967,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_tax_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_vat_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"discount_amt": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"gift_wrap_price": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"shipments": [{
									"receipt_shipping_id": 1106904050650,
									"shipment_notification_timestamp": 1672678800,
									"carrier_name": "Correios de Portugal (CTT)",
									"tracking_code": "EE759056560PT"
								}],
								"transactions": [{
									"transaction_id": 3369035392,
									"title": "Large Cream Rug \/ Carpet Rug \/ Cotton Rug \/ Soft Rug \/ Rugs for Living Room \/ Area Rugs \/ Washable Rug \/ Hallway Rug \/ Rugs for Bedroom",
									"description": "Large Cream Rug\n\nThis rug is woven in a manual loom with fabrics reused.\nThese rugs are robust, light, and can be regularly washed by hand or machine.\nIt is ecological because they reuse raw material wasted by the textil industry. \nWe recommend cold washing or to 30\u00b0C.\nSince this rug is made with recycled products from the textile industries, there may be slight variations in color.\n\nArtisan Made\nMade in Portugal\n\nDimensions:\nL - 200cm, W - 150cm\nL - 78,7in, W - 59,1in\nL - 6,56ft, W - 4,92ft\n\nThe dimensions may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 211275821,
									"create_timestamp": 1672614172,
									"created_timestamp": 1672614172,
									"paid_timestamp": 1672614309,
									"shipped_timestamp": 1672696168,
									"quantity": 1,
									"listing_image_id": 3709183697,
									"receipt_id": 2749379860,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1090308089,
									"sku": "woo-hoodie-with-pocket",
									"stock":1,
									"product_status":"out of stock",
									"product_id": 11591780590,
									"transaction_type": "listing",
									"price": {
										"amount": 4951,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [],
									"product_data": [],
									"shipping_profile_id": 161829385776,
									"min_processing_days": 1,
									"max_processing_days": 2,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672696168,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}],
								"refunds": []
							}, {
								"receipt_id": 2745397379,
								"receipt_type": 0,
								"seller_user_id": 288298098,
								"seller_email": "padacosta@gmail.com",
								"buyer_user_id": 739288583,
								"buyer_email": "marion.c.cochet@gmail.com",
								"name": "Marion Cochet",
								"first_line": "86 rue Cambronne",
								"second_line": "",
								"city": "Paris",
								"state": null,
								"zip": "75015",
								"status": "Completed",
								"formatted_address": "Marion Cochet\n86 rue Cambronne\n75015 PARIS\nFrance",
								"country_iso": "FR",
								"payment_method": "cc",
								"payment_email": "",
								"message_from_payment": null,
								"message_from_seller": "",
								"message_from_buyer": "",
								"is_shipped": true,
								"is_paid": true,
								"create_timestamp": 1672608056,
								"created_timestamp": 1672608056,
								"update_timestamp": 1672695920,
								"updated_timestamp": 1672695920,
								"is_gift": false,
								"gift_message": "",
								"grandtotal": {
									"amount": 5501,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"subtotal": {
									"amount": 3501,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_price": {
									"amount": 3501,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_shipping_cost": {
									"amount": 2000,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_tax_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_vat_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"discount_amt": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"gift_wrap_price": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"shipments": [{
									"receipt_shipping_id": 1129366885155,
									"shipment_notification_timestamp": 1672678800,
									"carrier_name": "GLS",
									"tracking_code": "Z00384PM"
								}],
								"transactions": [{
									"transaction_id": 3372078171,
									"title": "Runner Rug Dark Green 200cm \/ Kitchen Rug \/ Rug Runner \/ Floor Mat \/ Boho Rug \/ Washable Rug \/ Soft Rug \/ Rag Rug \/ Scandinavian Rug",
									"description": "Long Runner Rug Dark Green 200cm\n(color #29 - dark green)\n\nThis rug is woven in a manual loom with fabrics reused.\nThese rugs are robust, light, and can be regularly washed by hand or machine.\nIt is ecological because they reuse raw material wasted by the textil industry. \nWe recommend cold washing or to 30\u00b0C.\nSince this rug is made with recycled products from the textile industries, there may be slight variations in color.\n\nArtisan Made\nMade in Portugal\n\nDimensions:\nL - 200 cm, W - 60 cm\nL - 78,7 in, W - 23,2 in\nL - 6,6 ft, W - 2,0 ft\n\nThe dimensions may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 739288583,
									"create_timestamp": 1672608056,
									"created_timestamp": 1672608056,
									"paid_timestamp": 1672608077,
									"shipped_timestamp": 1672695919,
									"quantity": 1,
									"listing_image_id": 3540518735,
									"receipt_id": 2745397379,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1113842534,
									"sku": "woo-sunglasses",
									"stock":1,
									"product_status":"out of stock",
									"product_id": 11228740045,
									"transaction_type": "listing",
									"price": {
										"amount": 3501,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [{
										"property_id": 500,
										"value_id": 261371258949,
										"formatted_name": "Finish",
										"formatted_value": "With Fringes"
									}, {
										"property_id": 513,
										"value_id": 855329107029,
										"formatted_name": "Size",
										"formatted_value": "60x200cm"
									}],
									"product_data": [{
										"property_id": 500,
										"property_name": "Finish",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [261371258949],
										"values": ["With Fringes"]
									}, {
										"property_id": 513,
										"property_name": "Size",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [855329107029],
										"values": ["60x200cm"]
									}],
									"shipping_profile_id": 161823185692,
									"min_processing_days": 1,
									"max_processing_days": 2,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672695919,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}],
								"refunds": []
							}, {
								"receipt_id": 2745130645,
								"receipt_type": 0,
								"seller_user_id": 288298098,
								"seller_email": "padacosta@gmail.com",
								"buyer_user_id": 82389895,
								"buyer_email": "ine.potargent@gmail.com",
								"name": "Ine Potargent",
								"first_line": "te nijverdoncklaan 5",
								"second_line": null,
								"city": "Edegem",
								"state": null,
								"zip": "2650",
								"status": "Completed",
								"formatted_address": "Ine Potargent\nte nijverdoncklaan 5\n2650 EDEGEM\nBelgium",
								"country_iso": "BE",
								"payment_method": "cc",
								"payment_email": "",
								"message_from_payment": null,
								"message_from_seller": null,
								"message_from_buyer": "",
								"is_shipped": true,
								"is_paid": true,
								"create_timestamp": 1672593305,
								"created_timestamp": 1672593305,
								"update_timestamp": 1672602816,
								"updated_timestamp": 1672602816,
								"is_gift": false,
								"gift_message": "",
								"grandtotal": {
									"amount": 1582,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"subtotal": {
									"amount": 812,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_price": {
									"amount": 812,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_shipping_cost": {
									"amount": 770,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_tax_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_vat_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"discount_amt": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"gift_wrap_price": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"shipments": [{
									"receipt_shipping_id": 1106637733272,
									"shipment_notification_timestamp": 1672592400,
									"carrier_name": "Correios de Portugal (CTT)",
									"tracking_code": "RL027008665PT"
								}],
								"transactions": [{
									"transaction_id": 3368570104,
									"title": "Spinning Top \/ Wood Spinning Top \/ Gift for Kids \/ Wooden Toy \/ Children Toy \/ Wedding Gift",
									"description": "Wooden Spinning Top\nChildren Toys\nArtisan Made\n\nDimensions:\n\nW - 7cm, H - 9cm \nW - 2,76in, H - 3,54in",
									"seller_user_id": 288298098,
									"buyer_user_id": 82389895,
									"create_timestamp": 1672593305,
									"created_timestamp": 1672593305,
									"paid_timestamp": 1672593322,
									"shipped_timestamp": 1672602815,
									"quantity": 1,
									"listing_image_id": 2957890015,
									"receipt_id": 2745130645,
									"is_digital": false,
									"file_data": "",
									"listing_id": 972526433,
									"sku": "woo-cap",
									"stock":1,
									"product_status":"out of stock",
									"product_id": 6102312871,
									"transaction_type": "listing",
									"price": {
										"amount": 812,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [],
									"product_data": [],
									"shipping_profile_id": 133228652547,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672602815,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}],
								"refunds": []
							}, {
								"receipt_id": 2744848101,
								"receipt_type": 0,
								"seller_user_id": 288298098,
								"seller_email": "padacosta@gmail.com",
								"buyer_user_id": 739163437,
								"buyer_email": "mbd5779@aol.com",
								"name": "Michelle Dasilva",
								"first_line": "69 West St",
								"second_line": "# 2",
								"city": "New Bedford",
								"state": "MA",
								"zip": "02740-2244",
								"status": "Completed",
								"formatted_address": "Michelle Dasilva\n69 West St\n# 2\nNEW BEDFORD, MA 02740-2244\nUnited States",
								"country_iso": "US",
								"payment_method": "cc",
								"payment_email": "",
								"message_from_payment": null,
								"message_from_seller": null,
								"message_from_buyer": "",
								"is_shipped": true,
								"is_paid": true,
								"create_timestamp": 1672553606,
								"created_timestamp": 1672553606,
								"update_timestamp": 1672681329,
								"updated_timestamp": 1672681329,
								"is_gift": false,
								"gift_message": "",
								"grandtotal": {
									"amount": 8269,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"subtotal": {
									"amount": 4526,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_price": {
									"amount": 4526,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_shipping_cost": {
									"amount": 3460,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_tax_cost": {
									"amount": 283,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_vat_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"discount_amt": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"gift_wrap_price": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"shipments": [{
									"receipt_shipping_id": 1106847102846,
									"shipment_notification_timestamp": 1672678800,
									"carrier_name": "Correios de Portugal (CTT)",
									"tracking_code": "EE759056539PT"
								}],
								"transactions": [{
									"transaction_id": 3368218342,
									"title": "Extra Large Multicolor Rooster \/ Portuguese Rooster \/ Barcelos Rooster \/ Rooster Decor \/ Rustic Home Decor \/ Farmhouse Rooster \/ Funny Gift",
									"description": "Extra Large Multicolor Rooster\n\nThe figure of the rooster of Barcelos comes from the interesting popular legend that transports Barcelos to medieval times, and which tells the story of a pilgrim on his way to Santiago de Compostela, who was miraculously saved from the gallows, thanks to Santiago, when a rooster crowed made itself heard in a surprising way.\nIt was from the 50s and 60s that the rooster of Barcelos became a symbol of national tourism and an icon of a nation&#39;s identity.\n\nWith bright colors, multifaceted in size and shapes, a testament to Portugal&#39;s cultural differences and ethnographic variety.\nThe image of the rooster of Barcelos is universal and is a symbol of Portuguese identity present in many parts of the world, going beyond the borders of the municipality that gave it its name.\n\nBecause these roosters are so beautiful and symbolic, we warn you that the patterns may change slightly but it is always with the same color pattern.\n\nArtisan Made\nHand painted\nMade in Portugal\n\nDimensions:\nL - 25 cm, W - 17 cm, H - 39 cm\nL - 9,8 in, W - 6,7 in, H - 15,4 in\n\nThe dimensions may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 739163437,
									"create_timestamp": 1672553606,
									"created_timestamp": 1672553606,
									"paid_timestamp": 1672553624,
									"shipped_timestamp": 1672681329,
									"quantity": 1,
									"listing_image_id": 4257387475,
									"receipt_id": 2744848101,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1095930404,
									"sku": "woo-belt",
									"stock":1,
									"product_status":"out of stock",
									"product_id": 8088965223,
									"transaction_type": "listing",
									"price": {
										"amount": 4526,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [],
									"product_data": [],
									"shipping_profile_id": 141802051111,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672681329,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}],
								"refunds": []
							}, {
								"receipt_id": 2744697787,
								"receipt_type": 0,
								"seller_user_id": 288298098,
								"seller_email": "padacosta@gmail.com",
								"buyer_user_id": 89732240,
								"buyer_email": "isabellewanson@skynet.be",
								"name": "Isabelle Wanson",
								"first_line": "Avenue Jupiter, 181",
								"second_line": "Apt 405",
								"city": "Brussels",
								"state": "Brussels",
								"zip": "1190",
								"status": "Completed",
								"formatted_address": "Isabelle Wanson\nAvenue Jupiter, 181\nApt 405\n1190 BRUSSELS\nBelgium",
								"country_iso": "BE",
								"payment_method": "cc",
								"payment_email": "",
								"message_from_payment": null,
								"message_from_seller": null,
								"message_from_buyer": "",
								"is_shipped": true,
								"is_paid": true,
								"create_timestamp": 1672537948,
								"created_timestamp": 1672537948,
								"update_timestamp": 1672604058,
								"updated_timestamp": 1672604058,
								"is_gift": false,
								"gift_message": "",
								"grandtotal": {
									"amount": 3657,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"subtotal": {
									"amount": 2597,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_price": {
									"amount": 2886,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_shipping_cost": {
									"amount": 1060,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_tax_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_vat_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"discount_amt": {
									"amount": 289,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"gift_wrap_price": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"shipments": [{
									"receipt_shipping_id": 1106642222492,
									"shipment_notification_timestamp": 1672592400,
									"carrier_name": "Correios de Portugal (CTT)",
									"tracking_code": "RL027008643PT"
								}],
								"transactions": [{
									"transaction_id": 3368019482,
									"title": "Straw Bag \/ Natural Reed Bag \/ Reed Basket Bag \/ Summer Bag \/ Portuguese Basket Bag \/ Small Bag \/ Straw Purse \/ Gift Bag \/ Mini Bag",
									"description": "Mini Natural Reed Straw Bag\n\nArtisan Made\nMade in Portugal\n\nDimensions:\nL - 18 cm, W - 12 cm, H - 15 cm \nL - 7,1 in, W - 4,7 in, H - 5,9 in\n\nWithout lining inside\nThe dimensions of the bag may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 89732240,
									"create_timestamp": 1672537948,
									"created_timestamp": 1672537948,
									"paid_timestamp": 1672537964,
									"shipped_timestamp": 1672604058,
									"quantity": 1,
									"listing_image_id": 3965708782,
									"receipt_id": 2744697787,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1013376484,
									"sku": "woo-beanie",
									"stock":1,
									"product_status":"out of stock",
									"product_id": 7220435178,
									"transaction_type": "listing",
									"price": {
										"amount": 2886,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [],
									"product_data": [],
									"shipping_profile_id": 133228731565,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672604058,
									"buyer_coupon": 2.890000000000000124344978758017532527446746826171875,
									"shop_coupon": 2.890000000000000124344978758017532527446746826171875
								}],
								"refunds": []
							}, {
								"receipt_id": 2744649757,
								"receipt_type": 0,
								"seller_user_id": 288298098,
								"seller_email": "padacosta@gmail.com",
								"buyer_user_id": 74894010,
								"buyer_email": "theresaflynn65@gmail.com",
								"name": "Theresa Flynn",
								"first_line": "4 Birchwood Gardens",
								"second_line": "Braithwell",
								"city": "Rotherham",
								"state": "South Yorkshire",
								"zip": "S66 7BT",
								"status": "Completed",
								"formatted_address": "Theresa Flynn\n4 Birchwood Gardens\nBraithwell\nROTHERHAM, South Yorkshire S66 7BT\nUnited Kingdom",
								"country_iso": "GB",
								"payment_method": "cc",
								"payment_email": "",
								"message_from_payment": null,
								"message_from_seller": null,
								"message_from_buyer": "",
								"is_shipped": true,
								"is_paid": true,
								"create_timestamp": 1672533996,
								"created_timestamp": 1672533996,
								"update_timestamp": 1672604028,
								"updated_timestamp": 1672604028,
								"is_gift": false,
								"gift_message": "",
								"grandtotal": {
									"amount": 2384,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"subtotal": {
									"amount": 1147,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_price": {
									"amount": 1274,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_shipping_cost": {
									"amount": 840,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_tax_cost": {
									"amount": 397,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_vat_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"discount_amt": {
									"amount": 127,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"gift_wrap_price": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"shipments": [{
									"receipt_shipping_id": 1106642113960,
									"shipment_notification_timestamp": 1672592400,
									"carrier_name": "Correios de Portugal (CTT)",
									"tracking_code": "RT141309899PT"
								}],
								"transactions": [{
									"transaction_id": 3371092815,
									"title": "Ceramic Sardine \/ Portuguese Sardine \/ Traditional Ceramic Sardine \/ Ceramic Hanging Sardine \/ Sardine Art \/ Vintage Ceramic \/ Ceramic Fish",
									"description": "Ceramic Sardine\n\nCeramic sardines made and painted by hand.\nThey have an opening on the back to hang on the wall.\n\nArtisan Made\nMade in Portugal\n\nDimensions:\nL - 19 cm, W - 2 cm, H - 4 cm\nL - 7,5 in, W - 0,8 in, H - 1,6 in\n\n\nThe dimensions and illustrations may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 74894010,
									"create_timestamp": 1672533996,
									"created_timestamp": 1672533996,
									"paid_timestamp": 1672534018,
									"shipped_timestamp": 1672604027,
									"quantity": 1,
									"listing_image_id": 3310232384,
									"receipt_id": 2744649757,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1019935843,
									"sku": "woo-tshirt",
									"stock":1,
									"product_status":"out of stock",
									"product_id": 7537062232,
									"transaction_type": "listing",
									"price": {
										"amount": 637,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [{
										"property_id": 200,
										"value_id": 1037156580623,
										"formatted_name": "Color",
										"formatted_value": "Green"
									}],
									"product_data": [{
										"property_id": 200,
										"property_name": "Primary color",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [1037156580623],
										"values": ["Verde"]
									}],
									"shipping_profile_id": 133228652547,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672604027,
									"buyer_coupon": 0.58999999999999996891375531049561686813831329345703125,
									"shop_coupon": 0.64000000000000001332267629550187848508358001708984375
								}, {
									"transaction_id": 3371092813,
									"title": "Ceramic Fish Hand Painted \/ Ceramic Sardine \/ Vintage Ceramic \/ Pottery Fish \/ Ceramic Fish \/Ceramic Hanging Sardine \/ Fish Decor \/ Fish Art",
									"description": "Ceramic Fish Hand Painted\n\nCeramic fishs made and painted by hand.\nThey have an opening on the back to hang on the wall.\n\nArtisan Made\nMade in Portugal\n\nDimensions:\nL - 18 cm, W - 2,5 cm, H - 4 cm\nL - 7,1 in, W - 1,0 in, H - 1,6 in\n\n\nThe dimensions and illustrations may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 74894010,
									"create_timestamp": 1672533996,
									"created_timestamp": 1672533996,
									"paid_timestamp": 1672534018,
									"shipped_timestamp": 1672604027,
									"quantity": 1,
									"listing_image_id": 4235581320,
									"receipt_id": 2744649757,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1321738217,
									"sku": "woo-hoodie-with-logo",
									"stock":1,
									"product_status":"out of stock",
									"product_id": 11322523400,
									"transaction_type": "listing",
									"price": {
										"amount": 637,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [{
										"property_id": 200,
										"value_id": 55181013993,
										"formatted_name": "Color",
										"formatted_value": "Blue"
									}],
									"product_data": [{
										"property_id": 200,
										"property_name": "Primary color",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [55181013993],
										"values": ["Blue"]
									}],
									"shipping_profile_id": 132237467910,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672604027,
									"buyer_coupon": 0.57999999999999996003197111349436454474925994873046875,
									"shop_coupon": 0.63000000000000000444089209850062616169452667236328125
								}],
								"refunds": []
							}, {
								"receipt_id": 2744309853,
								"receipt_type": 0,
								"seller_user_id": 288298098,
								"seller_email": "padacosta@gmail.com",
								"buyer_user_id": 734544734,
								"buyer_email": "dawn_arlotta@yahoo.com",
								"name": "Dawn Arlotta",
								"first_line": "195 14th Street NE",
								"second_line": "",
								"city": "Atlanta",
								"state": "GA",
								"zip": "30309",
								"status": "Completed",
								"formatted_address": "Dawn Arlotta\n195 14th Street NE\nATLANTA, GA 30309\nUnited States",
								"country_iso": "US",
								"payment_method": "cc",
								"payment_email": "",
								"message_from_payment": null,
								"message_from_seller": null,
								"message_from_buyer": "",
								"is_shipped": true,
								"is_paid": true,
								"create_timestamp": 1672511548,
								"created_timestamp": 1672511548,
								"update_timestamp": 1672681546,
								"updated_timestamp": 1672681546,
								"is_gift": false,
								"gift_message": "",
								"grandtotal": {
									"amount": 8697,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"subtotal": {
									"amount": 4526,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_price": {
									"amount": 4526,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_shipping_cost": {
									"amount": 3460,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_tax_cost": {
									"amount": 711,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_vat_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"discount_amt": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"gift_wrap_price": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"shipments": [{
									"receipt_shipping_id": 1106847889090,
									"shipment_notification_timestamp": 1672678800,
									"carrier_name": "Correios de Portugal (CTT)",
									"tracking_code": "EE759056556PT"
								}],
								"transactions": [{
									"transaction_id": 3370645735,
									"title": "Extra Large Multicolor Rooster \/ Portuguese Rooster \/ Barcelos Rooster \/ Rooster Decor \/ Rustic Home Decor \/ Farmhouse Rooster \/ Funny Gift",
									"description": "Extra Large Multicolor Rooster\n\nThe figure of the rooster of Barcelos comes from the interesting popular legend that transports Barcelos to medieval times, and which tells the story of a pilgrim on his way to Santiago de Compostela, who was miraculously saved from the gallows, thanks to Santiago, when a rooster crowed made itself heard in a surprising way.\nIt was from the 50s and 60s that the rooster of Barcelos became a symbol of national tourism and an icon of a nation&#39;s identity.\n\nWith bright colors, multifaceted in size and shapes, a testament to Portugal&#39;s cultural differences and ethnographic variety.\nThe image of the rooster of Barcelos is universal and is a symbol of Portuguese identity present in many parts of the world, going beyond the borders of the municipality that gave it its name.\n\nBecause these roosters are so beautiful and symbolic, we warn you that the patterns may change slightly but it is always with the same color pattern.\n\nArtisan Made\nHand painted\nMade in Portugal\n\nDimensions:\nL - 25 cm, W - 17 cm, H - 39 cm\nL - 9,8 in, W - 6,7 in, H - 15,4 in\n\nThe dimensions may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 734544734,
									"create_timestamp": 1672511548,
									"created_timestamp": 1672511548,
									"paid_timestamp": 1672511565,
									"shipped_timestamp": 1672681546,
									"quantity": 1,
									"listing_image_id": 4257387475,
									"receipt_id": 2744309853,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1095930404,
									"sku": "woo-hoodie",
									"stock":1,
									"product_status":"out of stock",
									"product_id": 8088965223,
									"transaction_type": "listing",
									"price": {
										"amount": 4526,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [],
									"product_data": [],
									"shipping_profile_id": 141802051111,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672681546,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}],
								"refunds": []
							}, {
								"receipt_id": 2748277184,
								"receipt_type": 0,
								"seller_user_id": 288298098,
								"seller_email": "padacosta@gmail.com",
								"buyer_user_id": 96869658,
								"buyer_email": "vikaskhaladkar@hotmail.com",
								"name": "Vikas Khaladkar",
								"first_line": "11 Pine River Road",
								"second_line": "",
								"city": "Middle Cove",
								"state": "NL",
								"zip": "A1K2A9",
								"status": "Completed",
								"formatted_address": "VIKAS KHALADKAR\n11 PINE RIVER ROAD\nMIDDLE COVE NL A1K2A9\nCanada",
								"country_iso": "CA",
								"payment_method": "cc",
								"payment_email": "",
								"message_from_payment": null,
								"message_from_seller": null,
								"message_from_buyer": "",
								"is_shipped": true,
								"is_paid": true,
								"create_timestamp": 1672509137,
								"created_timestamp": 1672509137,
								"update_timestamp": 1672603055,
								"updated_timestamp": 1672603055,
								"is_gift": false,
								"gift_message": "",
								"grandtotal": {
									"amount": 1983,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"subtotal": {
									"amount": 1103,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_price": {
									"amount": 1103,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_shipping_cost": {
									"amount": 880,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_tax_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_vat_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"discount_amt": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"gift_wrap_price": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"shipments": [{
									"receipt_shipping_id": 1106638537676,
									"shipment_notification_timestamp": 1672592400,
									"carrier_name": "Correios de Portugal (CTT)",
									"tracking_code": "RT141309810PT"
								}],
								"transactions": [{
									"transaction_id": 3370594097,
									"title": "Beach Hat \/ Straw Sun Hat \/ Straw Hat \/ Vintage Hat \/ Boho Straw Hat \/ Bohemian Straw Hat \/ Womens Hat \/ Mens Hat \/ Cowboy Hat",
									"description": "Beach Hat\nwith Black Ribbon\n\nArtisan Made\nMade in Portugal\n\nSize - 56 cm \/ 58 cm; 22 in \/ 22,8 in\nFull diameter - 29 cm; 11,5 in \n\nThe dimensions of the hat may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 96869658,
									"create_timestamp": 1672509137,
									"created_timestamp": 1672509137,
									"paid_timestamp": 1672509160,
									"shipped_timestamp": 1672603054,
									"quantity": 1,
									"listing_image_id": 3066846872,
									"receipt_id": 2748277184,
									"is_digital": false,
									"file_data": "",
									"listing_id": 999930139,
									"sku": "woo-vneck-tee",
									"stock":1,
									"product_status":"out of stock",
									"product_id": 6536156901,
									"transaction_type": "listing",
									"price": {
										"amount": 1103,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [],
									"product_data": [],
									"shipping_profile_id": 133228652547,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672603054,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}],
								"refunds": []
							}, {
								"receipt_id": 2744261761,
								"receipt_type": 0,
								"seller_user_id": 288298098,
								"seller_email": "padacosta@gmail.com",
								"buyer_user_id": 738892309,
								"buyer_email": "lakeishapug@charter.net",
								"name": "Stacy Odell",
								"first_line": "139 S Elm St",
								"second_line": "",
								"city": "Oconomowoc",
								"state": "WI",
								"zip": "53066-3510",
								"status": "Completed",
								"formatted_address": "Stacy Odell\n139 S Elm St\nOCONOMOWOC, WI 53066-3510\nUnited States",
								"country_iso": "US",
								"payment_method": "cc",
								"payment_email": "",
								"message_from_payment": null,
								"message_from_seller": null,
								"message_from_buyer": "",
								"is_shipped": true,
								"is_paid": true,
								"create_timestamp": 1672508619,
								"created_timestamp": 1672508619,
								"update_timestamp": 1672604456,
								"updated_timestamp": 1672604456,
								"is_gift": false,
								"gift_message": "",
								"grandtotal": {
									"amount": 28245,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"subtotal": {
									"amount": 19900,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_price": {
									"amount": 19900,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_shipping_cost": {
									"amount": 7000,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_tax_cost": {
									"amount": 1345,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_vat_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"discount_amt": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"gift_wrap_price": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"shipments": [{
									"receipt_shipping_id": 1129106193735,
									"shipment_notification_timestamp": 1672592400,
									"carrier_name": "Correios de Portugal (CTT)",
									"tracking_code": "EE759056511PT"
								}],
								"transactions": [{
									"transaction_id": 3367474714,
									"title": "Large Curved Bird Cage Full Handmade \/ Canary Cage \/ Bird House \/ Parakeet Cage \/ Love Your Bird",
									"description": "Large Curved Bird Cage Full Handmade\n\nGive to your lovely bird a new and large cage, with one attractive designe that you can easily add in you home decor. \nThis cage is all produce handy, using material with great quality, we use real wood treated and galvanized wire.\n\nFeatures of the cage:\n- One front door with spring and one lock\n- Two plastic feeders one in each side\n- One waste drawer with one lock to make easy cleaner the ground of the cage\n- One ring on top of the cage to hanging\n\nArtisan Made \nMade in Portugal\n \nDimensions: \nL - 56 cm, W - 33 cm, H - 50 cm\nL - 22,1 in, W - 13 in, H - 19,7 in\n\nSpacing between wires - 1,1 cm; 0,4 in\n\nThis product is careful packed to make sure that they arrives in good conditions in your home.\nThe dimensions may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 738892309,
									"create_timestamp": 1672508619,
									"created_timestamp": 1672508619,
									"paid_timestamp": 1672508635,
									"shipped_timestamp": 1672604455,
									"quantity": 1,
									"listing_image_id": 3924714030,
									"receipt_id": 2744261761,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1063927867,
									"sku": "woo-vneck-tee",
									"stock":1,
									"product_status":"out of stock",
									"product_id": 7752375168,
									"transaction_type": "listing",
									"price": {
										"amount": 19900,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [],
									"product_data": [],
									"shipping_profile_id": 183282911388,
									"min_processing_days": 1,
									"max_processing_days": 2,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672604455,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}],
								"refunds": []
							}, {
								"receipt_id": 2747894168,
								"receipt_type": 0,
								"seller_user_id": 288298098,
								"seller_email": "padacosta@gmail.com",
								"buyer_user_id": 119581001,
								"buyer_email": "warrengreenwell43@gmail.com",
								"name": "Warren Greenwell",
								"first_line": "2918 Strickland Street",
								"second_line": null,
								"city": "Baltimore",
								"state": "MD",
								"zip": "21223",
								"status": "Paid",
								"formatted_address": "Warren Greenwell\n2918 Strickland Street\nBALTIMORE, MD 21223\nUnited States",
								"country_iso": "US",
								"payment_method": "cc",
								"payment_email": "",
								"message_from_payment": null,
								"message_from_seller": null,
								"message_from_buyer": "",
								"is_shipped": false,
								"is_paid": true,
								"create_timestamp": 1672461421,
								"created_timestamp": 1672461421,
								"update_timestamp": 1672461436,
								"updated_timestamp": 1672461436,
								"is_gift": false,
								"gift_message": "",
								"grandtotal": {
									"amount": 27034,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"subtotal": {
									"amount": 18900,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_price": {
									"amount": 18900,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_shipping_cost": {
									"amount": 7000,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_tax_cost": {
									"amount": 1134,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_vat_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"discount_amt": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"gift_wrap_price": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"shipments": [],
								"transactions": [{
									"transaction_id": 3366951300,
									"title": "Large Oval Bird Cage Full Handmade \/ Brown Rustic Cage \/ Canary Cage \/ Bird House \/ Parakeet Cage \/ Love Your Bird \/ Bird Home",
									"description": "Large Oval Bird Cage Full Handmade\n\nGive to your lovely bird a new and large cage, with one attractive designe that you can easily add in you home decor. \nThis cage is all produce handy, using material with great quality, we use real wood treated and galvanized wire.\n\nFeatures of the cage:\n- One front door with spring and one lock\n- Two plastic feeders one in each side\n- One waste drawer with one lock to make easy cleaner the ground of the cage\n- One ring on top of the cage to hanging\n\nArtisan Made \nMade in Portugal\n\nDimensions: \nL - 55 cm, W - 30 cm, H - 48 cm\nL - 21,7 in, W - 11,8 in, H - 18,9 in\n\nSpacing between wires - 1,1 cm; 0,4 in\n\nThis product is careful packed to make sure that they arrives in good conditions in your home.\nThe dimensions may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 119581001,
									"create_timestamp": 1672461420,
									"created_timestamp": 1672461420,
									"paid_timestamp": 1672461435,
									"shipped_timestamp": null,
									"quantity": 1,
									"listing_image_id": 3972155179,
									"receipt_id": 2747894168,
									"is_digital": false,
									"file_data": "",
									"listing_id": 948841871,
									"sku": "woo-vneck-tee",
									"stock":1,
									"product_status":"out of stock",
									"product_id": 8533320752,
									"transaction_type": "listing",
									"price": {
										"amount": 18900,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [],
									"product_data": [],
									"shipping_profile_id": 184182506649,
									"min_processing_days": 5,
									"max_processing_days": 15,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1674275835,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}],
								"refunds": []
							}, {
								"receipt_id": 2743451277,
								"receipt_type": 0,
								"seller_user_id": 288298098,
								"seller_email": "padacosta@gmail.com",
								"buyer_user_id": 457049146,
								"buyer_email": "nathaliem.poinsignon@free.fr",
								"name": "POINSIGNON NATHALIE",
								"first_line": "32 RUE MONTANT",
								"second_line": null,
								"city": "BAR LE DUC",
								"state": null,
								"zip": "55000",
								"status": "Completed",
								"formatted_address": "POINSIGNON NATHALIE\n32 RUE MONTANT\n55000 BAR LE DUC\nFrance",
								"country_iso": "FR",
								"payment_method": "cc",
								"payment_email": "",
								"message_from_payment": null,
								"message_from_seller": "",
								"message_from_buyer": "",
								"is_shipped": true,
								"is_paid": true,
								"create_timestamp": 1672434776,
								"created_timestamp": 1672434776,
								"update_timestamp": 1672603486,
								"updated_timestamp": 1672603486,
								"is_gift": false,
								"gift_message": "",
								"grandtotal": {
									"amount": 2270,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"subtotal": {
									"amount": 1500,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_price": {
									"amount": 1500,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_shipping_cost": {
									"amount": 770,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_tax_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_vat_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"discount_amt": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"gift_wrap_price": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"shipments": [{
									"receipt_shipping_id": 1106640122326,
									"shipment_notification_timestamp": 1672592400,
									"carrier_name": "Correios de Portugal (CTT)",
									"tracking_code": "RL027008657PT"
								}],
								"transactions": [{
									"transaction_id": 3369505519,
									"title": "Set of 3 Mini Flowers Basket \/ Boho Basket \/ Flowers Girls Basket Bag \/ Small Straw Bag\/ Straw Basket \/ Wedding Flower Basket \/ Small Basket",
									"description": "Set of 3 Mini Flowers Basket\n\nDimensions:\nL - 19 cm, W - 9 cm, H - 11 cm\nL - 7,5 in, W - 3,5 in, H - 4,3 in\n\nThe dimensions may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 457049146,
									"create_timestamp": 1672434776,
									"created_timestamp": 1672434776,
									"paid_timestamp": 1672434793,
									"shipped_timestamp": 1672603486,
									"quantity": 1,
									"listing_image_id": 4223446473,
									"receipt_id": 2743451277,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1049843929,
									"sku": "woo-long-sleeve-tee",
									"product_id": 7329608055,
									"transaction_type": "listing",
									"price": {
										"amount": 1500,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [],
									"product_data": [],
									"shipping_profile_id": 133228652547,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672603486,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}],
								"refunds": []
							}, {
								"receipt_id": 2747501648,
								"receipt_type": 0,
								"seller_user_id": 288298098,
								"seller_email": "padacosta@gmail.com",
								"buyer_user_id": 127727862,
								"buyer_email": "mbgoodrick80@gmail.com",
								"name": "Mary Goodrick",
								"first_line": "840 W Ponce De Leon Ave",
								"second_line": "",
								"city": "Decatur",
								"state": "GA",
								"zip": "30030-2858",
								"status": "Completed",
								"formatted_address": "Mary Goodrick\n840 W Ponce De Leon Ave\nDECATUR, GA 30030-2858\nUnited States",
								"country_iso": "US",
								"payment_method": "cc",
								"payment_email": "",
								"message_from_payment": null,
								"message_from_seller": null,
								"message_from_buyer": "",
								"is_shipped": true,
								"is_paid": true,
								"create_timestamp": 1672432855,
								"created_timestamp": 1672432855,
								"update_timestamp": 1672603401,
								"updated_timestamp": 1672603401,
								"is_gift": false,
								"gift_message": "",
								"grandtotal": {
									"amount": 3050,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"subtotal": {
									"amount": 1274,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_price": {
									"amount": 1274,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_shipping_cost": {
									"amount": 1550,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_tax_cost": {
									"amount": 226,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"total_vat_cost": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"discount_amt": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"gift_wrap_price": {
									"amount": 0,
									"divisor": 100,
									"currency_code": "EUR"
								},
								"shipments": [{
									"receipt_shipping_id": 1129102320467,
									"shipment_notification_timestamp": 1672592400,
									"carrier_name": "Correios de Portugal (CTT)",
									"tracking_code": "RT141309868PT"
								}],
								"transactions": [{
									"transaction_id": 3366403880,
									"title": "Ceramic Sardine \/ Portuguese Sardine \/ Traditional Ceramic Sardine \/ Ceramic Hanging Sardine \/ Sardine Art \/ Vintage Ceramic \/ Ceramic Fish",
									"description": "Ceramic Sardine\n\nCeramic sardines made and painted by hand.\nThey have an opening on the back to hang on the wall.\n\nArtisan Made\nMade in Portugal\n\nDimensions:\nL - 19 cm, W - 2 cm, H - 4 cm\nL - 7,5 in, W - 0,8 in, H - 1,6 in\n\n\nThe dimensions and illustrations may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 127727862,
									"create_timestamp": 1672432855,
									"created_timestamp": 1672432855,
									"paid_timestamp": 1672432873,
									"shipped_timestamp": 1672603400,
									"quantity": 1,
									"listing_image_id": 3310232380,
									"receipt_id": 2747501648,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1019935843,
									"sku": "woo-long-sleeve-tee",
									"product_id": 7537062230,
									"transaction_type": "listing",
									"price": {
										"amount": 637,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [{
										"property_id": 200,
										"value_id": 1037159931791,
										"formatted_name": "Color",
										"formatted_value": "Black"
									}],
									"product_data": [{
										"property_id": 200,
										"property_name": "Primary color",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [1037159931791],
										"values": ["Preto"]
									}],
									"shipping_profile_id": 133228652547,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672603400,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}, {
									"transaction_id": 3366403878,
									"title": "Ceramic Sardine \/ Portuguese Sardine \/ Traditional Ceramic Sardine \/ Ceramic Hanging Sardine \/ Sardine Art \/ Vintage Ceramic \/ Ceramic Fish",
									"description": "Ceramic Sardine\n\nCeramic sardines made and painted by hand.\nThey have an opening on the back to hang on the wall.\n\nArtisan Made\nMade in Portugal\n\nDimensions:\nL - 19 cm, W - 2 cm, H - 4 cm\nL - 7,5 in, W - 0,8 in, H - 1,6 in\n\n\nThe dimensions and illustrations may vary slightly.",
									"seller_user_id": 288298098,
									"buyer_user_id": 127727862,
									"create_timestamp": 1672432855,
									"created_timestamp": 1672432855,
									"paid_timestamp": 1672432873,
									"shipped_timestamp": 1672603400,
									"quantity": 1,
									"listing_image_id": 3310232498,
									"receipt_id": 2747501648,
									"is_digital": false,
									"file_data": "",
									"listing_id": 1019935843,
									"sku": "woo-hoodie-with-zipper",
									"product_id": 8222466619,
									"transaction_type": "listing",
									"price": {
										"amount": 637,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"shipping_cost": {
										"amount": 0,
										"divisor": 100,
										"currency_code": "EUR"
									},
									"variations": [{
										"property_id": 200,
										"value_id": 1014707916014,
										"formatted_name": "Color",
										"formatted_value": "Silver"
									}],
									"product_data": [{
										"property_id": 200,
										"property_name": "Primary color",
										"scale_id": null,
										"scale_name": null,
										"value_ids": [1014707916014],
										"values": ["Prata"]
									}],
									"shipping_profile_id": 133228652547,
									"min_processing_days": 1,
									"max_processing_days": 1,
									"shipping_method": null,
									"shipping_upgrade": null,
									"expected_ship_date": 1672603400,
									"buyer_coupon": 0,
									"shop_coupon": 0
								}],
								"refunds": []
							}]
						}
						', true);


		if ( isset( $result['results'] ) && ! empty( $result['results'] ) ) {
			$order_created = $this->createLocalOrder( $result['results'], $shopId );
			if (!$is_sync) {
				return true;
			}
		}
		if (!$is_sync) {
			return false;
		}
	}

	/*
	*
	*function for creating a local order
	*
	*
	*/
	public function createLocalOrder( $orders, $shopId = '' ) {
		if ( is_array( $orders ) && ! empty( $orders ) ) {
			$address        = array();
			$OrderItemsInfo = array();
			foreach ( $orders as $order ) {
				$receipt_id = isset( $order['receipt_id'] ) ? $order['receipt_id'] : '';
				$order_id   = $this->is_etsy_order_exists( $receipt_id, $shopId );
				if ( $order_id ) {
					continue;
				}
				if ( ! empty( $receipt_id ) ) {
					$transactions_per_reciept = isset( $order['transactions'] ) ? $order['transactions'] : array();
					$ShipToFirstName          = isset( $order['name'] ) ? $order['name'] : '';
					$ShipToAddress1           = isset( $order['first_line'] ) ? $order['first_line'] : '';
					$ShipToAddress2           = isset( $order['second_line'] ) ? $order['second_line'] : '';
					$ShipToCityName           = isset( $order['city'] ) ? $order['city'] : '';

					$ShipToStateCode = isset( $order['state'] ) ? $order['state'] : '';
					$ShipToZipCode   = isset( $order['zip'] ) ? $order['zip'] : '';
					$is_country      = isset( $order['country_iso'] ) ? $order['country_iso'] : '';

					$message_from_buyer = isset( $order['message_from_buyer'] ) ? $order['message_from_buyer'] : '';
					$gift_message       = isset( $order['gift_message'] ) ? $order['gift_message'] : '';

					$exploded_name   = explode( ' ', $ShipToFirstName );
					$ShipToFirstName = isset( $exploded_name[0] ) ? $exploded_name[0] : '';
					$ShipToLastName  = isset( $exploded_name[1] ) ? $exploded_name[1] : '';

					$exploded_names_count = count( $exploded_name );
					if ( $exploded_names_count > 1 ) {
						$ShipToLastName  = array_pop( $exploded_name );
						$ShipToFirstName = implode( ' ', $exploded_name );
					}

					$ShippingAddress = array(
						'first_name' => $ShipToFirstName,
						'last_name'  => $ShipToLastName,
						'address_1'  => $ShipToAddress1,
						'address_2'  => $ShipToAddress2,
						'city'       => $ShipToCityName,
						'state'      => $ShipToStateCode,
						'postcode'   => $ShipToZipCode,
						'country'    => $is_country,
					);

					$BillToFirstName  = $ShipToFirstName;
					$BillEmailAddress = isset( $order['buyer_email'] ) ? $order['buyer_email'] : '';

					$BillingAddress = array(
						'first_name' => $BillToFirstName,
						'last_name'  => $ShipToLastName,
						'email'      => $BillEmailAddress,
						'address_1'  => $ShipToAddress1,
						'address_2'  => $ShipToAddress2,
						'city'       => $ShipToCityName,
						'state'      => $ShipToStateCode,
						'postcode'   => $ShipToZipCode,
						'country'    => $is_country,
					);

					$address['shipping'] = $ShippingAddress;
					$address['billing']  = $BillingAddress;

					$OrderNumber  = isset( $order['receipt_id'] ) ? $order['receipt_id'] : '';
					$order_status = 'processing';
					$ShipService  = 'Shipping';

					$update_stock_with_no_order = isset( $this->saved_global_settings_data[ $shopId ]['update_stock_with_no_order'] ) ? $this->saved_global_settings_data[ $shopId ]['update_stock_with_no_order'] : '';
					if ( ! empty( $transactions_per_reciept ) ) {

						$ItemArray = array();
						foreach ( $transactions_per_reciept as $transaction ) {
							$ID = false;

							$ShipService = ! empty( $transaction['shipping_upgrade'] ) ? $transaction['shipping_upgrade'] : '';
							if ( empty( $ShipService ) ) {
								$ShipService = ! empty( $transaction['shipping_method'] ) ? $transaction['shipping_method'] : 'Shipping';
							}

							$listing_id = isset( $transaction['listing_id'] ) ? $transaction['listing_id'] : false;
							$OrderedQty = isset( $transaction['quantity'] ) ? $transaction['quantity'] : 1;
							$basePrice  = isset( $transaction['price']['amount'] ) ? $transaction['price']['amount'] / $transaction['price']['divisor'] : '';
							$variations = isset( $transaction['variations'] ) ? $transaction['variations'] : array();
							$CancelQty  = 0;
							$sku        = isset( $transaction['sku'] ) ? $transaction['sku'] : '';
							if ( ! empty( $sku ) ) {
								$ID = $this->get_product_id_by_order_params( '_sku', $sku );
							}
							if ( 'on' == $update_stock_with_no_order ) {

								if ( ! $ID && ! empty( $sku ) ) {
									$_product = wc_get_product( $sku );
									if ( is_object( $_product ) ) {
										$ID = $sku;
									}
								}

								if ( ! $ID ) {
									$ID = $this->get_product_id_by_order_params( '_ced_etsy_listing_id_' . $shopId, $listing_id );
								}

								$stock_reduced = get_post_meta( $ID, '_ced_etsy_stock_reduced_' . $OrderNumber, true );

								if ( $ID && is_object( wc_get_product( $ID ) ) && 'yes' != $stock_reduced ) {
									$_product = wc_get_product( $ID );
									$_product->reduce_stock( $OrderedQty );
									update_post_meta( $ID, '_ced_etsy_stock_reduced_' . $OrderNumber, 'yes' );
								}

								continue;
							}

							$item = array(
								'OrderedQty' => $OrderedQty,
								'CancelQty'  => $CancelQty,
								'UnitPrice'  => $basePrice,
								'Sku'        => $sku,
								'ID'         => $ID,
								'variations' => $variations,
								'listing_id' => $listing_id,
							);

							$ItemArray[] = $item;

						}
					}
				}

				$ShippingAmount   = isset( $order['total_shipping_cost']['amount'] ) ? $order['total_shipping_cost']['amount'] / $order['total_shipping_cost']['divisor'] : 0;
				$DiscountedAmount = isset( $order['discount_amt']['amount'] ) ? $order['discount_amt']['amount'] / $order['discount_amt']['divisor'] : 0;
				$gift_wrap_price  = isset( $order['gift_wrap_price']['amount'] ) ? $order['gift_wrap_price']['amount'] / $order['gift_wrap_price']['divisor'] : 0;
				$finalTax         = isset( $order['total_tax_cost']['amount'] ) ? $order['total_tax_cost']['amount'] / $order['total_tax_cost']['divisor'] : '';

				$fees_array = array(
					'Discount'      => 0 - $DiscountedAmount,
					'Gift Wrapping' => $gift_wrap_price,
					'Tax'           => $finalTax,
				);

				$OrderItemsInfo = array(
					'OrderNumber'        => isset( $OrderNumber ) ? $OrderNumber : '',
					'ItemsArray'         => isset( $ItemArray ) ? $ItemArray : '',
					'tax'                => isset( $finalTax ) ? $finalTax : '',
					'ShippingAmount'     => isset( $ShippingAmount ) ? $ShippingAmount : '',
					'ShipService'        => isset( $ShipService ) ? $ShipService : '',
					'DiscountedAmount'   => isset( $DiscountedAmount ) ? $DiscountedAmount : '',
					'message_from_buyer' => isset( $message_from_buyer ) ? $message_from_buyer : '',
					'gift_message'       => isset( $gift_message ) ? $gift_message : '',
					'fees_array'         => $fees_array,
				);
				$orderItems     = isset( $transactions_per_reciept ) ? $transactions_per_reciept : '';

				$merchantOrderId = isset( $OrderNumber ) ? $OrderNumber : '';
				$purchaseOrderId = isset( $OrderNumber ) ? $OrderNumber : '';
				$fulfillmentNode = '';
				$orderDetail     = isset( $order ) ? $order : array();
				$etsyOrderMeta   = array(
					'merchant_order_id' => isset( $merchantOrderId ) ? $merchantOrderId : '',
					'purchaseOrderId'   => isset( $purchaseOrderId ) ? $purchaseOrderId : '',
					'fulfillment_node'  => isset( $fulfillmentNode ) ? $fulfillmentNode : '',
					'order_detail'      => isset( $orderDetail ) ? $orderDetail : '',
					'order_items'       => isset( $orderItems ) ? $orderItems : '',
				);
				$creation_date   = $order['created_timestamp'];
				if ( 'on' !== $update_stock_with_no_order ) {
					$order_id = $this->create_order( $address, $OrderItemsInfo, 'Etsy', $etsyOrderMeta, $creation_date, $shopId );
				}
			}
		}
	}

	public function get_product_id_by_order_params( $meta_key = '', $meta_value = '' ) {
		if ( ! empty( $meta_value ) ) {
			$posts = get_posts(
				array(

					'numberposts' => -1,
					'post_type'   => array( 'product', 'product_variation' ),
					'post_status' => array_keys( get_post_statuses() ),
					'meta_query'  => array(
						array(
							'key'     => $meta_key,
							'value'   => trim( $meta_value ),
							'compare' => '=',
						),
					),
					'fields'      => 'ids',

				)
			);
			if ( ! empty( $posts ) ) {
				return $posts[0];
			}
			return false;
		}
		return false;
	}


	/*
	*
	*function for creating order in woocommerce
	*
	*
	*/

	public function create_order( $address = array(), $OrderItemsInfo = array(), $frameworkName = 'etsy', $orderMeta = array(), $creation_date = '', $shopId = '' ) {
		$order_id      = '';
		$order_created = false;

		if ( count( $OrderItemsInfo ) ) {

			$OrderNumber = isset( $OrderItemsInfo['OrderNumber'] ) ? $OrderItemsInfo['OrderNumber'] : 0;
			$order_id    = $this->is_etsy_order_exists( $OrderNumber , $shopId );
			if ( $order_id ) {
				return $order_id;
			}

			global $activity;
			$activity->action        = 'Fetch';
			$activity->type          = 'order';
			$activity->input_payload = $OrderItemsInfo;
			$activity->post_title    = 'Etsy order : ' . $OrderNumber;
			$activity->post_id       = $OrderNumber;
			$activity->shop_name     = $shopId;
			$activity->is_auto       = $this->is_sync;
			$response                = array();
			if ( count( $OrderItemsInfo ) ) {
				$ItemsArray = isset( $OrderItemsInfo['ItemsArray'] ) ? $OrderItemsInfo['ItemsArray'] : array();
				if ( is_array( $ItemsArray ) ) {
					foreach ( $ItemsArray as $ItemInfo ) {
						$ProID         = isset( $ItemInfo['ID'] ) ? intval( $ItemInfo['ID'] ) : 0;
						$Sku           = isset( $ItemInfo['Sku'] ) ? $ItemInfo['Sku'] : '';
						$listing_id    = isset( $ItemInfo['listing_id'] ) ? $ItemInfo['listing_id'] : '';
						$MfrPartNumber = isset( $ItemInfo['MfrPartNumber'] ) ? $ItemInfo['MfrPartNumber'] : '';
						$Upc           = isset( $ItemInfo['UPCCode'] ) ? $ItemInfo['UPCCode'] : '';
						$Asin          = isset( $ItemInfo['ASIN'] ) ? $ItemInfo['ASIN'] : '';
						$variations    = isset( $ItemInfo['variations'] ) ? $ItemInfo['variations'] : array();
						$params        = array( '_sku' => $Sku );

						if ( ! $ProID && ! empty( $Sku ) ) {
							$_product = wc_get_product( $Sku );
							if ( is_object( $_product ) ) {
								$ProID = $Sku;
							}
						}

						if ( ! $ProID ) {
							$ProID = $this->get_product_id_by_order_params( '_ced_etsy_listing_id_' . $shopId, $listing_id );
						}

						$productsToUpdate[]   = $ProID;
						$Qty                  = isset( $ItemInfo['OrderedQty'] ) ? intval( $ItemInfo['OrderedQty'] ) : 0;
						$UnitPrice            = isset( $ItemInfo['UnitPrice'] ) ? floatval( $ItemInfo['UnitPrice'] ) : 0;
						$ExtendUnitPrice      = isset( $ItemInfo['ExtendUnitPrice'] ) ? floatval( $ItemInfo['ExtendUnitPrice'] ) : 0;
						$ExtendShippingCharge = isset( $ItemInfo['ExtendShippingCharge'] ) ? floatval( $ItemInfo['ExtendShippingCharge'] ) : 0;
						$_product             = wc_get_product( $ProID );

						if ( is_wp_error( $_product ) ) {
							$response[] = 'No product found with sku :' . $Sku . ' or Etsy listing ID : ' . $listing_id;
							continue;
						} elseif ( is_null( $_product ) ) {
							$response[] = 'No product found with sku :' . $Sku . ' or Etsy listing ID : ' . $listing_id;
							continue;
						} elseif ( ! $_product ) {
							$response[] = 'No product found with sku :' . $Sku . ' or Etsy listing ID : ' . $listing_id;
							continue;
						} else {
							if ( ! $order_created ) {
								$order_data = array(
									'status'        => 'pending',
									'customer_note' => $OrderItemsInfo['message_from_buyer'],
									'created_via'   => $frameworkName,
								);

								$create_customer = isset( $this->saved_global_settings_data[ $shopId ]['create_customer'] ) ? $this->saved_global_settings_data[ $shopId ]['create_customer'] : '';
								$buyer_email     = isset( $address['billing']['email'] ) ? $address['billing']['email'] : '';
								$user_id         = email_exists( $buyer_email );

								if ( 'on' == $create_customer ) {
									if ( ! empty( $buyer_email ) && ! $user_id ) {
										$user_id = wc_create_new_customer( $buyer_email );
									}
									if ( $user_id ) {
										$order_data['customer_id'] = $user_id;
									}
								}

								/* ORDER CREATED IN WOOCOMMERCE */
								$order = wc_create_order( $order_data );

								/* ORDER CREATED IN WOOCOMMERCE */

								if ( is_wp_error( $order ) ) {
									continue;
								} elseif ( false === $order ) {
									continue;
								} else {
									if ( WC()->version < '3.0.0' ) {
										$order_id = $order->id;
									} else {
										$order_id = $order->get_id();
									}									
									if ( $this->create_in_hpos ) {
										$order->update_meta_data( '_ced_etsy_order_id', $OrderNumber );
									}else{
										update_post_meta( $order_id, '_ced_etsy_order_id', $OrderNumber );
									}

									$order_created = true;
									$response[]    = 'Order created successfuly with woocommerce order id : ' . $order_id;
								}
							}

							if ( ! empty( $OrderItemsInfo['gift_message'] ) ) {
								$note = '<b><i>Gift message from buyer :</i></b> ' . $OrderItemsInfo['gift_message'];
								$order->add_order_note( $note );
							}

							if ( $this->create_in_hpos ) {
								$order->update_meta_data( '_ced_etsy_order_id', $OrderNumber );
							}else{
								update_post_meta( $order_id, '_ced_etsy_order_id', $OrderNumber );
							}

							$_product->set_price( $UnitPrice );
							$item_id = $order->add_product( $_product, $Qty );
							$order->calculate_totals();

							if ( ! empty( $variations ) && is_array( $variations ) ) {
								foreach ( $variations as $variation ) {
									wc_update_order_item_meta( $item_id, $variation['formatted_name'], $variation['formatted_value'] );
								}
							}
						}
					}
				}

				if ( ! $order_created ) {
					$activity->response = $response;
					$activity->execute();
					return false;
				}

				$OrderItemAmount = isset( $OrderItemsInfo['OrderItemAmount'] ) ? $OrderItemsInfo['OrderItemAmount'] : 0;
				$ShippingAmount  = isset( $OrderItemsInfo['ShippingAmount'] ) ? $OrderItemsInfo['ShippingAmount'] : 0;
				$DiscountAmount  = isset( $OrderItemsInfo['DiscountAmount'] ) ? $OrderItemsInfo['DiscountAmount'] : 0;
				$RefundAmount    = isset( $OrderItemsInfo['RefundAmount'] ) ? $OrderItemsInfo['RefundAmount'] : 0;
				$ShipService     = isset( $OrderItemsInfo['ShipService'] ) ? $OrderItemsInfo['ShipService'] : '';

				$fees_array = isset( $OrderItemsInfo['fees_array'] ) ? $OrderItemsInfo['fees_array'] : '';

				if ( ! empty( $fees_array ) ) {
					foreach ( $fees_array as $fee_name => $fee_value ) {
						$item_fee = new WC_Order_Item_Fee();
						$item_fee->set_name( $fee_name );
						$fee_amount = (float) $fee_value;
						$item_fee->set_total( $fee_amount );
						$order->add_item( $item_fee );
					}
				}

				if ( ! empty( $ShipService ) ) {
					$Ship_params = array(
						'ShippingCost' => $ShippingAmount,
						'ShipService'  => $ShipService,
					);
					$this->add_shipping_charge( $order, $Ship_params );
				}

				$ShippingAddress = isset( $address['shipping'] ) ? $address['shipping'] : '';
				if ( is_array( $ShippingAddress ) && ! empty( $ShippingAddress ) ) {
					if ( WC()->version < '3.0.0' ) {
						$order->set_address( $ShippingAddress, 'shipping' );
					} else {
						$type = 'shipping';
						foreach ( $ShippingAddress as $key => $value ) {
							if ( ! empty( $value ) && null != $value && ! empty( $value ) ) {
								if( $this->create_in_hpos ) {
									$order->update_meta_data(  "_{$type}_" . $key, $value );
								}else{
									update_post_meta( $order->get_id(), "_{$type}_" . $key, $value );
								}
								if ( is_callable( array( $order, "set_{$type}_{$key}" ) ) ) {
									$order->{"set_{$type}_{$key}"}( $value );
								}
							}
						}
					}
				}

				$new_fee            = new stdClass();
				$new_fee->name      = 'Tax';
				$new_fee->amount    = (float) esc_attr( $OrderItemsInfo['tax'] );
				$new_fee->tax_class = '';
				$new_fee->taxable   = 0;
				$new_fee->tax       = '';
				$new_fee->tax_data  = array();
				if ( WC()->version < '3.0.0' ) {
					$item_id = $order->add_fee( $new_fee );
				} else {
					$item_id = $order->add_item( $new_fee );
				}

				$BillingAddress = isset( $address['billing'] ) ? $address['billing'] : '';
				if ( is_array( $BillingAddress ) && ! empty( $BillingAddress ) ) {
					if ( WC()->version < '3.0.0' ) {
						$order->set_address( $ShippingAddress, 'billing' );
					} else {
						$type = 'billing';
						foreach ( $BillingAddress as $key => $value ) {
							if ( null != $value && ! empty( $value ) ) {
								if ( $this->create_in_hpos ) {
									$order->update_meta_data(  "_{$type}_" . $key, $value );
								}else{
									update_post_meta( $order->get_id(), "_{$type}_" . $key, $value );
								}
								if ( is_callable( array( $order, "set_{$type}_{$key}" ) ) ) {
									$order->{"set_{$type}_{$key}"}( $value );
								}
							}
						}
					}
				}
				wc_reduce_stock_levels( $order->get_id() );
				$order->set_payment_method( 'check' );

				if ( WC()->version < '3.0.0' ) {
					$order->set_total( $DiscountAmount, 'cart_discount' );
				} else {
					$order->set_total( $DiscountAmount );
				}
				$order->calculate_totals();
				if ( $this->create_in_hpos ) {
					$order->update_meta_data(  "_is_ced_etsy_order", 1 );
					$order->update_meta_data(  "_is_ced_order", 1 );
					$order->update_meta_data(  "_etsy_umb_order_status", "Fetched" );
					$order->update_meta_data(  "_umb_etsy_marketplace", $frameworkName );
					$order->update_meta_data(  "ced_etsy_order_shop_id", $shopId );
					$order->update_meta_data(  "ced_etsy_last_order_created_time", $creation_date );
					$order->save();
				}else{
					update_post_meta( $order_id, '_is_ced_etsy_order', 1 );
					update_post_meta( $order_id, '_is_ced_order', 1 );
					update_post_meta( $order_id, '_etsy_umb_order_status', 'Fetched' );
					update_post_meta( $order_id, '_umb_etsy_marketplace', $frameworkName );
					update_post_meta( $order_id, 'ced_etsy_order_shop_id', $shopId );
					update_post_meta( $order_id, 'ced_etsy_last_order_created_time', $creation_date );
				}

				update_option( 'ced_etsy_last_order_created_time', $creation_date );
				$renderDataOnGlobalSettings = get_option( 'ced_etsy_global_settings', array() );
				$default_order_status       = ! empty( $renderDataOnGlobalSettings[ $shopId ]['default_order_status'] ) ? $renderDataOnGlobalSettings[ $shopId ]['default_order_status'] : 'wc-processing';
				$order->update_status( $default_order_status );
				if ( count( $orderMeta ) ) {
					foreach ( $orderMeta as $oKey => $oValue ) {
						if ($this->create_in_hpos) {
							$order->update_meta_data( $oKey, $oValue );
						}else{
							update_post_meta( $order_id, $oKey, $oValue );
						}
					}
				}
				$order->save();
			}
			$final_response = $response;
			if ( $order_created ) {
				$final_response = array( 'response' => array( 'results' => $response ) );
			}
			$activity->response = $final_response;
			$activity->execute();
			return $order_id;
		}
		return false;
	}

	/**
	 * Etsy checking if order already exists
	 *
	 * @since    1.0.0
	 */
	public function is_etsy_order_exists( $order_number = 0, $shop_name = '' ) {
		if ($this->create_in_hpos) {
			$orders = wc_get_orders(
			    array(
			    	'limit'     => -1,
			    	'status'    => 'all',
			    	'return'    => 'ids',
			        'meta_query' => array(
			            array(
			                'key'        => '_ced_etsy_order_id',
			                'value'      => $order_number,
			                'comparison' => '==',
			            ),
			             array(
			                'key'        => '_umb_etsy_marketplace',
			                'value'      => 'Etsy',
			                'comparison' => '=='
			            ),
			            array(
			                'key'        => 'ced_etsy_order_shop_id',
			                'value'      => $shop_name,
			                'comparison' => '=='
			            ),
			            'fields' => 'ids',

			        ),
			    )
			);
			$order_id = isset( $orders[0] ) ? $orders[0] : false;
			return $order_id;
		}else{
			global $wpdb;
			if ( $order_number ) {
				$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_ced_etsy_order_id' AND meta_value=%s LIMIT 1", $order_number ) );
				if ( $order_id ) {
					return $order_id;
				}
			}
		}
		return false;
	}

	/**
	 * Function to add shipping data
	 *
	 * @since 1.0.0
	 * @param object $order Order details.
	 * @param array  $ship_params Shipping details.
	 */
	public function add_shipping_charge( $order, $ship_params = array() ) {
		$ship_name = isset( $ship_params['ShipService'] ) ? ( $ship_params['ShipService'] ) : 'UMB Default Shipping';
		$ship_cost = isset( $ship_params['ShippingCost'] ) ? $ship_params['ShippingCost'] : 0;
		$ship_tax  = isset( $ship_params['ShippingTax'] ) ? $ship_params['ShippingTax'] : 0;
		$item      = new WC_Order_Item_Shipping();
		$item->set_method_title( $ship_name );
		$item->set_method_id( $ship_name );
		$item->set_total( $ship_cost );
		$order->add_item( $item );
		$order->calculate_totals();
		$order->save();
	}
}
