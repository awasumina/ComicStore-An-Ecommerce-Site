<?php

namespace YayMail\License;

use YayMail\License\LicenseAPI;
use YayMail\License\LicenseHandler;
use YayMail\License\LicensingPlugin;

defined( 'ABSPATH' ) || exit;


class License {

	protected $slug            = null;
	protected $license_key     = null;
	protected $license_info    = null;
	protected static $instance = null;

	public function __construct( $plugin_slug ) {
		$this->slug         = $plugin_slug;
		$this->license_key  = $this->get_license_key();
		$this->license_info = $this->get_license_info();
	}

	public function update_license_info( $license_info ) {
		unset( $license_info['success'] );
		// $license_info['expires'] = '2020-06-30 23:59:59';
		update_option( $this->slug . '_license_info', $license_info );
	}

	public function update_license_key( $license_key ) {
		update_option( $this->slug . '_license_key', $license_key );
	}

	public function get_license_key() {
		return get_option( $this->slug . '_license_key' );
	}

	public function get_license_info() {
		$default_info = array(
			'expires' => 'Not updated',
		);
		$info         = get_option( $this->slug . '_license_info' );
		$info         = is_string( $info ) ? \json_decode( $info, true ) : $info;
		return $info ? $info : $default_info;
	}

	public function remove_license_key() {
		delete_option( $this->slug . '_license_key' );
	}

	public function remove_license_info() {
		delete_option( $this->slug . '_license_info' );
	}

	public function activate( $license_key ) {
		$licensing_plugin  = new LicensingPlugin( $this->slug );
		$item_id           = $licensing_plugin->get_option( 'item_id' );
		$activate_response = LicenseAPI::activate_license( $item_id, $license_key );
		if ( $activate_response['success'] ) {
			$this->update_license_key( $license_key );
			$this->update_license_info( $activate_response );
			$this->license_key = $license_key;
		}
		$licensing_plugin->update_version_info();
		return $activate_response;
	}

	public function update() {
		$licensing_plugin = new LicensingPlugin( $this->slug );
		$item_id          = $licensing_plugin->get_option( 'item_id' );

		$license_key = $this->get_license_key();

		$response = LicenseAPI::check_license( $item_id, $license_key );
		if ( $response['success'] ) {
			$this->update_license_info( $response );
		} else {
			if ( empty( $response['is_server_error'] ) ) {
				$this->remove();
			}
		}
		$licensing_plugin->update_version_info();
		return $response;
	}

	public function remove() {
		$this->remove_license_key();
		$this->remove_license_info();
	}

	public function is_active() {
		return $this->license_key;
	}

	public function is_expired() {
		if ( $this->is_active() ) {
			$license_info = $this->get_license_info();
			if ( 'lifetime' !== $license_info['expires'] ) {
				if ( strtotime( $license_info['expires'] ) < time() ) {
					return true;
				}
			}
		}
		return false;
	}

	public function format_license_key( $number_seperate = 8, $seperate = '-', $number_hidden = 20 ) {
		$license_key        = $this->license_key;
		$license_key_length = strlen( $license_key );
		if ( $license_key_length <= $number_hidden ) {
			return $license_key;
		}
		for ( $i = $license_key_length - $number_hidden; $i < $license_key_length; $i++ ) {
			$license_key[ $i ] = '*';
		}
		$formatted_license_key = '';
		for ( $i = 0; $i < $license_key_length; $i++ ) {
			$formatted_license_key .= $license_key[ $i ];
			if ( 0 === ( ( $i + 1 ) % $number_seperate ) && $i + 1 >= $number_seperate && $i !== $license_key_length - 1 ) {
				$formatted_license_key .= $seperate;
			}
		}
		return $formatted_license_key;
	}

	public function get_renewal_url() {
		$licensing_plugin = new LicensingPlugin( $this->slug );
		$item_id          = $licensing_plugin->get_option( 'item_id' );
		return YAYCOMMERCE_SELLER_SITE_URL . 'checkout/?edd_license_key=' . $this->license_key . '&download_id=' . $item_id;
	}

	public function get_upgrade_url() {
		return YAYCOMMERCE_SELLER_SITE_URL . 'checkout/purchase-history/?view=upgrades&action=manage_licenses&payment_id=' . ( isset( $this->license_info['payment_id'] ) ? $this->license_info['payment_id'] : '' );
	}
}
