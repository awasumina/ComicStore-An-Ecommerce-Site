<?php
/**
 * YayCommerce licenses menu
 *
 * @package YayMail\Admin
 */

namespace YayMail\YayCommerceMenu;

defined( 'ABSPATH' ) || exit;

/**
 * Declare class
 */
class LicensesMenu {

	public static function render() {?>
		<script>
			document.querySelector("#wpbody-content").innerHTML = "";
		</script>
			<?php
			include plugin_dir_path( __FILE__ ) . 'views/licenses.php';
	}

	public static function load_data() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	public static function enqueue_scripts() {
		wp_enqueue_style( 'yaycommerce-licenses', plugin_dir_url( __FILE__ ) . 'assets/css/licenses.css', array(), '1.0' );
	}
}
