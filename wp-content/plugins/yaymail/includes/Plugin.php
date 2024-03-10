<?php
namespace YayMail;

use YayMail\Page\Source\CustomPostType;

defined( 'ABSPATH' ) || exit;
/**
 * Plugin activate/deactivate logic
 */
class Plugin {

	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->do_hooks();
		}

		return self::$instance;
	}

	private function do_hooks() {
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		$versionCurrent = YAYMAIL_VERSION;
		$versionOld     = get_option( 'yaymail_version' );
		if ( $versionCurrent != $versionOld ) {
			self::activate();
		}
	}

	/**
	 *
	 * Plugin activated hook
	 */
	public static function activate() {
		Helper\ActivePlugin::getInstance();
	}

	/**
	 *
	 * Plugin deactivate hook
	 */
	public static function deactivate() {

	}
}
