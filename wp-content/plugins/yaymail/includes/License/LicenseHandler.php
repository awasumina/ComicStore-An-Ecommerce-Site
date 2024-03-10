<?php

namespace YayMail\License;

use YayMail\License\EDD_SL_Plugin_Updater;
use YayMail\License\RestAPI;
use YayMail\License\CorePlugin;

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'YAYCOMMERCE_SELLER_SITE_URL' ) ) {
	define( 'YAYCOMMERCE_SELLER_SITE_URL', 'https://yaycommerce.com/' );
}

class LicenseHandler {
	protected static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	protected function __construct() {
		new RestAPI();
		if ( is_admin() ) {
			$this->do_hooks();
			$this->do_cron_job();
			$this->show_plugin_page_notification();
			$this->license_set_site_transient();
		}
	}

	public function do_hooks() {
		add_action( 'admin_notices', array( $this, 'not_activate_license_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_license_scripts' ) );
		add_action( 'yaycommerce_licenses_page', array( $this, 'yaycommerce_licenses_settings' ), 100 );
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'yaycommerce_licenses_setting_tab' ), 100, 1 );
		add_action( 'woocommerce_settings_yaycommerce_licenses', array( $this, 'yaycommerce_licenses_settings' ), 100 );
		add_action( 'yaycommerce_licensing_plugins', array( $this, 'register_licensing_plugins' ) );

		/** Expired license admin notice */
		add_action( 'admin_notices', array( $this, 'license_expired_admin_notice' ) );

		$licensing_plugins = $this->get_licensing_plugins();
		foreach ( $licensing_plugins as $plugin ) {
			$license = new License( $plugin['slug'] );
			if ( ! $license->is_active() || $license->is_expired() ) {
				/** Add plugin action when expired license */
				add_filter( 'plugin_action_links_' . $plugin['basename'], array( $this, 'edit_action_links' ) );
			}
		}

		/** Filter for view changelog */
		add_filter( 'plugins_api', array( $this, 'change_plugin_data' ), 21, 3 );

		/** Auto update */
		add_action( 'admin_init', array( $this, 'auto_update' ) );
		add_filter( 'auto_update_plugin', array( $this, 'add_disabled_auto_update_text' ), 100, 2 );
	}

	public function not_activate_license_notice() {

	}

	public function do_cron_job() {
		add_filter( 'cron_schedules', array( $this, 'custom_schedules' ) );
		add_action( 'check_license_cron', array( $this, 'check_license_cron_run' ) );
		if ( ! wp_next_scheduled( 'check_license_cron' ) ) {
			wp_schedule_event( time(), 'daily', 'check_license_cron' );
		};

		add_action( 'get_version_cron', array( $this, 'get_version_cron_run' ) );
		if ( ! wp_next_scheduled( 'get_version_cron' ) ) {
			$schedules = \wp_get_schedules();
			if ( isset( $schedules['hourly'] ) ) {
				wp_schedule_event( time(), 'hourly', 'get_version_cron' );
			}
		};
	}

	public function custom_schedules( $schedules ) {
		$schedules['3hours'] = array(
			'interval' => 60 * 60 * 3,
			'display'  => 'Three Hours',
		);
		return $schedules;
	}

	public function check_license_cron_run() {
		$licensing_plugins = $this->get_licensing_plugins();
		foreach ( $licensing_plugins as $plugin ) {
			$license = new License( $plugin['slug'] );
			$license->update();
		}
	}

	public function get_version_cron_run() {
		$licensing_plugins = $this->get_licensing_plugins();
		foreach ( $licensing_plugins as $plugin ) {
			$licensing_plugin = new LicensingPlugin( $plugin['slug'] );
			$licensing_plugin->update_version_info();
		}
	}

	public function enqueue_license_scripts() {
		if ( ! isset( $_GET['page'] ) || ! 'yaycommerce-licenses' === $_GET['page'] ) {
			return;
		}
		wp_enqueue_script( CorePlugin::get( 'slug' ) . '-license-script', plugin_dir_url( __FILE__ ) . 'assets/js/license.js', array(), CorePlugin::get( 'version' ), true );
		wp_localize_script(
			CorePlugin::get( 'slug' ) . '-license-script',
			CorePlugin::get( 'slug' ) . 'LicenseData',
			array(
				'apiSettings' => array(
					'restNonce' => wp_create_nonce( 'wp_rest' ),
					'restUrl'   => esc_url_raw( rest_url( CorePlugin::get( 'slug' ) . '-license/v1' ) ),
					'adminUrl'  => admin_url(),
				),
			)
		);
	}

	public function yaycommerce_licenses_setting_tab( $settings_array ) {
		$licensing_plugins = $this->get_licensing_plugins();
		if ( count( $licensing_plugins ) > 0 ) {
			$settings_array = array_merge( $settings_array, array( 'yaycommerce_licenses' => 'YayCommerce Licenses' ) );
		}
		return $settings_array;
	}

	public function yaycommerce_licenses_settings() {
		$licensing_plugins = $this->get_licensing_plugins();
		foreach ( $licensing_plugins as $_plugin ) {
			$licensing_plugin = new LicensingPlugin( $_plugin['slug'] );
			$license          = $licensing_plugin->get_license();
			if ( $license->is_active() ) {
				require plugin_dir_path( __FILE__ ) . 'views/information-card.php';
			} else {
				require plugin_dir_path( __FILE__ ) . 'views/activate-card.php';
			}
		}
	}

	public static function get_plugin_data( $plugin_info ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugin_data = get_plugin_data( $plugin_info['dir_path'] . basename( $plugin_info['basename'] ) );
		return $plugin_data;
	}

	public static function get_licensing_plugins() {
		$plugins = array();
		return apply_filters( CorePlugin::get( 'slug' ) . '_available_licensing_plugins', $plugins );
	}
	public function register_licensing_plugins( $plugins ) {
		return array_merge( $plugins, self::get_licensing_plugins() );
	}

	/** NOTIFICATION */
	public function license_expired_admin_notice() {
		require plugin_dir_path( __FILE__ ) . 'views/expired-admin-notice.php';
	}

	public function show_plugin_page_notification() {
		$licensing_plugins = $this->get_licensing_plugins();
		foreach ( $licensing_plugins as $plugin ) {
			add_action( 'after_plugin_row_' . $plugin['basename'], array( $this, 'plugin_notifications' ), 10, 2 );
		}
	}

	public function plugin_notifications( $file, $plugin_info ) {
		$wp_list_table     = _get_list_table( 'WP_MS_Themes_List_Table' );
		$licensing_plugins = $this->get_licensing_plugins();
		$match_position    = array_search( $file, array_column( $licensing_plugins, 'basename' ) );
		if ( false === $match_position ) {
			return;
		}
		$plugin              = $licensing_plugins[ $match_position ];
		$licensing_plugin    = new LicensingPlugin( $plugin['slug'] );
		$license             = $licensing_plugin->get_license();
		$plugin_version_info = $licensing_plugin->get_version_info();
		if ( ! $license->is_active() || $license->is_expired() ) {
			$new_version = isset( $plugin_version_info['new_version'] ) ? $plugin_version_info['new_version'] : '0';
			?>
			<script>
				var plugin_row_element = document.querySelector('tr[data-plugin="<?php echo esc_js( plugin_basename( $file ) ); ?>"]');
				plugin_row_element.classList.add('update');
			</script>
			<tr class="plugin-update-tr <?php echo esc_attr( is_plugin_active( $file ) ? 'active' : '' ); ?>">
				<td colspan="<?php echo esc_attr( $wp_list_table->get_column_count() ); ?>" class="plugin-update colspanchange">
			<?php
			if ( $plugin_info['Version'] < $new_version ) {
				require plugin_dir_path( __FILE__ ) . 'views/update-plugin-notification.php';
			}
			if ( ! $license->is_active() ) {
				require plugin_dir_path( __FILE__ ) . 'views/not-activate-license-notification.php';
			}
			if ( $license->is_expired() ) {
				require plugin_dir_path( __FILE__ ) . 'views/expired-license-notification.php';
			}
		}
	}

	/** Plugin action link */
	public function edit_action_links( $action_links ) {
		$new_action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=yaycommerce-licenses' ) . '" aria-label="' . esc_attr( 'View YayCommerce license settings' ) . '">' . esc_html( 'Enter license key' ) . '</a>',
		);
		return array_merge( $new_action_links, $action_links );
	}

	public function change_plugin_data( $_data, $_action = '', $_args = null ) {
		if ( ! isset( $_args->slug ) ) {
			return $_data;
		}
		$licensing_plugins = $this->get_licensing_plugins();
		$match_position    = array_search( $_args->slug, array_column( $licensing_plugins, 'basename' ) );
		if ( false === $match_position ) {
			return $_data;
		}

		$plugin           = $licensing_plugins[ $match_position ];
		$licensing_plugin = new LicensingPlugin( $plugin['slug'] );
		$license          = $licensing_plugin->get_license();

		if ( $license->is_active() && ! $license->is_expired() ) {
			return $_data;
		}

		$plugin_data = self::get_plugin_data( $plugin );

		$plugin_version_info = $licensing_plugin->get_version_info();

		$_data = (object) $plugin_version_info;
		$sites = get_site_transient( 'update_plugins' );
		if ( ! isset( $sites->response[ $plugin['basename'] ] ) ) {
			$sites->response[ $plugin['basename'] ] = $_data;
			set_site_transient( 'update_plugins', $sites );
		}

		$_data->version        = $_data->new_version;
		$_data->author         = $plugin_data['AuthorName'];
		$_data->author_profile = $plugin_data['AuthorURI'];
		$_data->download_link  = '#';

		add_action(
			'admin_head',
			function() use ( $plugin ) {
				?>
				<script>
					jQuery(document).ready(function(){
						var update_a_tag = document.querySelector('a[data-plugin="<?php echo esc_js( $plugin['basename'] ); ?>"]#plugin_update_from_iframe');
						update_a_tag.innerHTML = '<?php echo esc_html( 'Active your license to update' ); ?>';
						update_a_tag.href = '<?php echo esc_url( admin_url() ) . 'admin.php?page=wc-settings&tab=yaycommerce_licenses'; ?>';
						update_a_tag.id = '<?php echo esc_attr( CorePlugin::get( 'slug' ) ); ?>-activate-license';
						update_a_tag.target = '_blank';
					})
				</script>
				<?php
			},
			100
		);

		if ( isset( $_data->sections ) && ! is_array( $_data->sections ) ) {
			$_data->sections = maybe_unserialize( $_data->sections );
		}

		if ( isset( $_data->banners ) && ! is_array( $_data->banners ) ) {
			$_data->banners = maybe_unserialize( $_data->banners );
		}

		if ( isset( $_data->icons ) && ! is_array( $_data->icons ) ) {
			$_data->icons = maybe_unserialize( $_data->icons );
		}

		if ( isset( $_data->contributors ) && ! is_array( $_data->contributors ) ) {
			$_data->contributors = $this->convert_object_to_array( $_data->contributors );
		}

		if ( ! isset( $_data->plugin ) ) {
			$_data->plugin = $plugin['basename'];
		}

		return $_data;
	}

	public function convert_object_to_array( $data ) {
		$new_data = array();
		foreach ( $data as $key => $value ) {
			$new_data[ $key ] = is_object( $value ) ? $this->convert_object_to_array( $value ) : $value;
		}
		return $new_data;
	}

	/** Auto update */
	public function auto_update() {
		$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
		if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
			return;
		}
		$licensing_plugins = $this->get_licensing_plugins();
		foreach ( $licensing_plugins as $plugin ) {
			$license = new License( $plugin['slug'] );
			if ( $license->is_active() && ! $license->is_expired() ) {
				$_file = $plugin['dir_path'] . basename( $plugin['basename'] );

				$plugin_data = self::get_plugin_data( $plugin );
				$license_key = $license->get_license_key();
				$args        = array(
					'version' => $plugin_data['Version'],
					'license' => $license_key,
					'author'  => $plugin_data['AuthorName'],
					'item_id' => $plugin['item_id'],
				);
				$edd_updater = new EDD_SL_Plugin_Updater(
					YAYCOMMERCE_SELLER_SITE_URL,
					$_file,
					$args
				);
			}
		}
	}

	public function license_set_site_transient() {
		global $pagenow;
		if ( 'plugin-install.php' === $pagenow ) {
			return;
		}
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$licensing_plugins             = $this->get_licensing_plugins();
		$site_transient_update_plugins = get_site_transient( 'update_plugins' );
		$check                         = false;

		foreach ( $licensing_plugins as $plugin ) {
			$license = new License( $plugin['slug'] );
			if ( ! $license->is_active() || $license->is_expired() ) {
				if ( isset( $site_transient_update_plugins->response[ $plugin['basename'] ] )
				|| isset( $site_transient_update_plugins->no_update[ $plugin['basename'] ] )
				) {
					$check = true;
					unset( $site_transient_update_plugins->response[ $plugin['basename'] ] );
					unset( $site_transient_update_plugins->no_update[ $plugin['basename'] ] );
				}
			}
		}
		if ( false !== $site_transient_update_plugins && $check ) {
			set_site_transient( 'update_plugins', $site_transient_update_plugins );
		}
	}

	public function add_disabled_auto_update_text( $value, $plugin_info ) {
		$licensing_plugins = $this->get_licensing_plugins();
		foreach ( $licensing_plugins as $plugin ) {
			if ( $plugin['basename'] === $plugin_info->plugin ) {
				$license = new License( $plugin['slug'] );
				if ( ! $license->is_active() || $license->is_expired() ) {
					return false;
				}
			}
		}
		return $value;
	}
}
