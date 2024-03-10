<?php

namespace YayMail\License;

class RestAPI {
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'init_rest_api' ) );
	}

	public function init_rest_api() {
		register_rest_route(
			CorePlugin::get( 'slug' ) . '-license/v1',
			'/license/activate',
			array(
				'methods'             => array( \WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'activate_license' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);
		register_rest_route(
			CorePlugin::get( 'slug' ) . '-license/v1',
			'/license/update',
			array(
				'methods'             => array( \WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'update_license' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);
		register_rest_route(
			CorePlugin::get( 'slug' ) . '-license/v1',
			'/license/delete',
			array(
				'methods'             => array( \WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'remove_license' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

	}

	public function activate_license( $request_data ) {
		$nonce = $request_data->get_header( 'x_wp_nonce' );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Nonce is invalid',
				)
			);
		}
		$plugin_slug              = sanitize_text_field( $request_data->get_param( 'plugin' ) );
		$license_key              = sanitize_text_field( $request_data->get_param( 'license_key' ) );
		$licensing_plugin         = new LicensingPlugin( $plugin_slug );
		$license                  = $licensing_plugin->get_license();
		$activate_response        = $license->activate( $license_key );
		$return_result['success'] = $activate_response['success'];
		$return_result['name']    = $licensing_plugin->get_option( 'name' );
		$return_result['slug']    = $plugin_slug;
		if ( $activate_response['success'] ) {
			$_plugin = array(
				'slug' => $plugin_slug,
				'name' => $licensing_plugin->get_option( 'name' ),
			);
			ob_start();
			include plugin_dir_path( __FILE__ ) . 'views/information-card.php';
			$html = ob_get_contents();
			ob_end_clean();

			$return_result['html'] = $html;
		} else {
			$return_result['message'] = LicenseAPI::get_error_message( $activate_response['message'] );
		}
		return new \WP_REST_Response( $return_result );
	}

	public function update_license( $request_data ) {
		$nonce = $request_data->get_header( 'x_wp_nonce' );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Nonce is invalid',
				)
			);
		}
		$plugin_slug              = sanitize_text_field( $request_data->get_param( 'plugin' ) );
		$licensing_plugin         = new LicensingPlugin( $plugin_slug );
		$license                  = $licensing_plugin->get_license();
		$update_response          = $license->update();
		$return_result['success'] = $update_response['success'];
		$return_result['name']    = $licensing_plugin->get_option( 'name' );
		$return_result['slug']    = $plugin_slug;
		$_plugin                  = $return_result;
		if ( $update_response['success'] || ! empty( $update_response['is_server_error'] ) ) {
			ob_start();
			include plugin_dir_path( __FILE__ ) . 'views/information-card.php';
			$html = ob_get_contents();
			ob_end_clean();
		} else {
			ob_start();
			include plugin_dir_path( __FILE__ ) . 'views/activate-card.php';
			$html = ob_get_contents();
			ob_end_clean();
		}
		$return_result['html'] = $html;
		return new \WP_REST_Response( $return_result );
	}
	public function remove_license( $request_data ) {
		$nonce = $request_data->get_header( 'x_wp_nonce' );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Nonce is invalid',
				)
			);
		}
		$plugin_slug      = sanitize_text_field( $request_data->get_param( 'plugin' ) );
		$licensing_plugin = new LicensingPlugin( $plugin_slug );
		$license          = $licensing_plugin->get_license();
		$license->remove();
		$return_result = array(
			'slug' => $plugin_slug,
			'name' => $licensing_plugin->get_option( 'name' ),
		);
		$_plugin       = $return_result;
		ob_start();
		include plugin_dir_path( __FILE__ ) . 'views/activate-card.php';
		$html = ob_get_contents();
		ob_end_clean();
		$return_result['html'] = $html;
		return new \WP_REST_Response( $return_result );
	}

	public function permission_callback() {
		return true;
	}
}
