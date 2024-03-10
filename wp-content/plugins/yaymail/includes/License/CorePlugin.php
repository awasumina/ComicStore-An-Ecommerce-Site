<?php

namespace YayMail\License;

defined( 'ABSPATH' ) || exit;

class CorePlugin {

	public static function get( $name ) {
		$data = array(
			'path'        => YAYMAIL_PLUGIN_PATH,
			'url'         => YAYMAIL_PLUGIN_URL,
			'basename'    => YAYMAIL_PLUGIN_BASENAME,
			'version'     => YAYMAIL_VERSION,
			'slug'        => 'yaymail',
			'link'        => 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/',
			'download_id' => '4216',
		);

		if ( isset( $data[ $name ] ) ) {
			return $data[ $name ];
		}
		return null;
	}
}
