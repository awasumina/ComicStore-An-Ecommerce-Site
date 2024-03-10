<?php

namespace YayMail\License;

defined( 'ABSPATH' ) || exit;

class LicenseAPI {
	public static function activate_license( $item_id, $license_key ) {
		try {
			$response = wp_remote_get( YAYCOMMERCE_SELLER_SITE_URL . '?edd_action=activate_license&item_id=' . $item_id . '&license=' . $license_key . '&url=' . home_url() );

			$response = json_decode( $response['body'] );

			if ( $response->success ) {
				return array(
					'success'       => true,
					'expires'       => $response->expires,
					'license_limit' => $response->license_limit,
					'payment_id'    => $response->payment_id,
					'customer_name' => $response->customer_name,
				);
			}
			return array(
				'success' => false,
				'message' => $response->error,
			);
		} catch ( \Error $error ) {
			return array(
				'success' => false,
				'message' => 'server_error',
			);
		}
	}

	public static function check_license( $item_id, $license_key ) {
		try {
			$response = wp_remote_get( YAYCOMMERCE_SELLER_SITE_URL . '?edd_action=check_license&item_id=' . $item_id . '&license=' . $license_key . '&url=' . home_url() );

			$response = json_decode( $response['body'] );

			if ( true === $response->success ) {
				if ( 'valid' === $response->license || 'expired' === $response->license ) {
					return array(
						'success'       => true,
						'expires'       => $response->expires,
						'license_limit' => $response->license_limit,
						'payment_id'    => $response->payment_id,
						'customer_name' => $response->customer_name,
					);
				}
			}
			return array(
				'success' => false,
			);
		} catch ( \Error $error ) {
			return array(
				'success'         => false,
				'is_server_error' => true,
			);
		}
	}

	public static function get_version( $item_id, $license_key = null ) {
		try {
			$api_url = YAYCOMMERCE_SELLER_SITE_URL . '?edd_action=get_version&item_id=' . $item_id;
			if ( ! empty( $license_key ) ) {
				$api_url .= '&license=' . $license_key;
			}
			$response = wp_remote_get( $api_url );
			$response = json_decode( $response['body'] );
			if ( isset( $response->new_version ) ) {
				return (array) $response;
			}
			return false;
		} catch ( \Error $error ) {
			return false;
		}
	}

	public static function get_error_message( $message ) {
		$result = '';
		switch ( $message ) {
			case 'missing':
				$result = "License doesn't exist";
				break;
			case 'license_not_activable':
				$result = "Attempting to activate a bundle's parent license";
				break;
			case 'disabled':
				$result = 'License key revoked';
				break;
			case 'no_activations_left':
				$result = 'No activations left';
				break;
			case 'expired':
				$result = 'License has expired';
				break;
			case 'key_mismatch':
				$result = 'License is not valid for this product';
				break;
			case 'item_name_mismatch':
				$result = 'License is not valid for this product';
				break;
			case 'server_error':
				$result = 'Your license could not be activated because of server error.';
				break;
			default:
				$result = 'Your license could not be activated.';
				break;
		}
		return $result;
	}
}
