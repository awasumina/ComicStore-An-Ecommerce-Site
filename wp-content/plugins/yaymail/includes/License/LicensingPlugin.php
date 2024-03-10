<?php

namespace YayMail\License;

use YayMail\License\LicenseHandler;
use YayMail\License\LicenseAPI;
use YayMail\License\License;

class LicensingPlugin {

	public $slug = null;

	protected $plugin_info = null;

	protected $license = null;

	public function __construct( $slug ) {
		$this->slug        = $slug;
		$licensing_plugins = LicenseHandler::get_licensing_plugins();
		$matching_position = array_search( $this->slug, array_column( $licensing_plugins, 'slug' ) );
		if ( false !== $matching_position ) {
			$this->plugin_info = $licensing_plugins[ $matching_position ];
		}
		$this->license = new License( $slug );
	}

	public function get_option( $key ) {
		if ( $this->plugin_info ) {
			return $this->plugin_info[ $key ];
		}
		return null;
	}

	public function get_license() {
		return $this->license;
	}

	public function get_version_info() {
		$info = get_option( $this->slug . '_version_info' );
		$info = is_string( $info ) ? \json_decode( $info, true ) : $info;
		return $info;
	}

	public function set_version_info( $data ) {
		update_option( $this->slug . '_version_info', $data );
	}

	public function update_version_info() {
		$license = $this->license;
		if ( $license instanceof License ) {
			$license_key = $license->get_license_key();
			$item_id     = $this->get_option( 'item_id' );
			$response    = LicenseAPI::get_version( $item_id, $license_key );
			if ( $response ) {
				$this->set_version_info( $response );
			}
		}
	}

}
