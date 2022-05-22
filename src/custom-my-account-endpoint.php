<?php
//class My_Custom_My_Account_Endpoint
//{
//
//
//	public function __construct()
//	{
//
//
//
//
//
//		add_action('init', function() {
//			add_rewrite_endpoint('license-keys', EP_ROOT | EP_PAGES);
//		});
//
//
//		add_filter('woocommerce_account_menu_items', function($items) {
//			$logout = $items['customer-logout'];
//			unset($items['customer-logout']);
//			$items['license-keys'] = __('License keys', 'txtdomain');
//			$items['customer-logout'] = $logout;
//			return $items;
//		});
//
//
//
//
//		add_action('woocommerce_account_license-keys_endpoint', function() {
//			_e('Your license keys', 'txtdomain');
//		});
//
//
//		add_action('woocommerce_account_license-keys_endpoint', function() {
//			$licenses = [];  // Replace with function to return licenses for current logged in user
//
//			wc_get_template('myaccount/license-keys.php', [
//				'licenses' => $licenses
//			]);
//		});
//
//		add_action('woocommerce_account_license-keys_endpoint', function() {
//			$order_id = get_query_var('license-keys');
//			$licenses = [];  // Function to return licenses for order ID
//
//			wc_get_template('myaccount/license-keys.php', [
//				'licenses' => $licenses,
//				'order_id' => $order_id
//			]);
//		});
//		add_filter('woocommerce_my_account_my_orders_actions', function($actions, $order) {
//			if ($order->get_status() == 'completed') {
//				$actions['view-license-keys'] = [
//					'url' => wc_get_endpoint_url('license-keys', $order->get_id()),
//					'name' => __('View license keys', 'txtdomain')
//				];
//			}
//			return $actions;
//		}, 10, 2);
//	}
//
//
//
//
//
//}
