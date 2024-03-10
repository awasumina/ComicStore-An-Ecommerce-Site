<?php

namespace YayMail\Helper;

use YayMail\Page\Source\CustomPostType;
use YayMail\Page\Source\DefaultElement;
use YayMail\Templates\Templates;
use YayMail\Helper\Helper;

defined( 'ABSPATH' ) || exit;
/**
 * Plugin activate/deactivate logic
 */
class ActivePlugin {

	protected static $instance = null;
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		if ( function_exists( 'WC' ) ) {
			$this->activePlugin();
		}
		$this->addDefaultSetting();
	}

	public function activePlugin() {
		$templateEmail            = Templates::getInstance();
		$templates                = $templateEmail::getList();
		$orderItemsTitle          = Helper::OrderItemsTitle();
		$orderItemsDownloadsTitle = Helper::OrderItemsDownloadsTitle();

		foreach ( $templates as $key => $template ) {
			$postID = CustomPostType::postIDByTemplate( $key );
			if ( $postID ) {
				if ( ! metadata_exists( 'post', $postID, '_yaymail_email_textLinkColor_settings' ) ) {
					update_post_meta( $postID, '_yaymail_email_textLinkColor_settings', '#7f54b3' );
				}
				if ( ! metadata_exists( 'post', $postID, '_yaymail_email_order_item_title' ) ) {
					update_post_meta( $postID, '_yaymail_email_order_item_title', $orderItemsTitle );
				} else {
					$yaymail_email_order_item_title = get_post_meta( $postID, '_yaymail_email_order_item_title', true );
					if ( ! empty( $yaymail_email_order_item_title ) && is_array( $yaymail_email_order_item_title ) ) {
						$merge_yaymail_email_order_item_title = array();
						foreach ( $orderItemsTitle as $key => $value ) {
							if ( isset( $yaymail_email_order_item_title[ $key ] ) && '' !== $yaymail_email_order_item_title[ $key ] ) {
								$merge_yaymail_email_order_item_title[ $key ] = $yaymail_email_order_item_title[ $key ];
							} else {
								$merge_yaymail_email_order_item_title[ $key ] = $value;
							}
						}
						update_post_meta( $postID, '_yaymail_email_order_item_title', $merge_yaymail_email_order_item_title );
					}
				}
				if ( ! metadata_exists( 'post', $postID, '_yaymail_email_order_item_download_title' ) ) {
					update_post_meta( $postID, '_yaymail_email_order_item_download_title', $orderItemsDownloadsTitle );
				}
			}
		}

		if ( ! get_option( 'yaymail_version' ) ) {
			update_option( 'yaymail_version', YAYMAIL_VERSION );
		}
		if ( ! get_option( 'yaymail_direction' ) ) {
			update_option( 'yaymail_direction', 'ltr' );
		}

		/*
		 @@@@check key in settingRow whether or not it exists.
		 @@@@note: case when add setting row for element.
		*/
		$versionCurrent = YAYMAIL_VERSION;
		$versionOld     = get_option( 'yaymail_version' );

		if ( $versionCurrent != $versionOld ) {
			update_option( 'yaymail_version', YAYMAIL_VERSION );
			$posts = CustomPostType::getListPostTemplate();
			if ( count( $posts ) > 0 ) {
				foreach ( $posts as $post ) {
					$elements = get_post_meta( $post->ID, '_yaymail_elements', true );

					$defaultElement      = new DefaultElement();
					$defaultDataElements = $defaultElement->defaultDataElement;

					foreach ( $defaultDataElements as $defaultelement ) {

						foreach ( (array) $elements as $keyEl => $element ) {
							$element_type = isset( $element['type'] ) ? $element['type'] : null;
							if ( $defaultelement['type'] == $element_type ) {
								/*
								@@@ add key default for element
								*/
								$keyEleDefaus = array();
								$keyEleDefaus = array_diff_key( $defaultelement, $element );
								if ( count( $keyEleDefaus ) > 0 ) {
									$elements[ $keyEl ] = array_merge( $element, $keyEleDefaus );
								}

								/*
								@@@ add key default for setting row.
								@@@ note: when add a field in setting row
								*/
								$propSettings    = array();
								$propSettings    = array_diff_key( $defaultelement['settingRow'], $element['settingRow'] );
								$lenPropSettings = count( $propSettings );
								if ( $lenPropSettings > 0 ) {
										$result                           = array();
										$result                           = array_merge( $element['settingRow'], $propSettings );
										$elements[ $keyEl ]['settingRow'] = $result;
								}
							}
						}
					}
					update_post_meta( $post->ID, '_yaymail_elements', $elements );
				}
			}
		}

		$settingDefault = CustomPostType::templateEnableDisable();
		$listTemplates  = ! empty( $settingDefault ) ? array_keys( $settingDefault ) : array();
		$settingCurrent = array(
			'new_order'                 => 1,
			'cancelled_order'           => 1,
			'failed_order'              => 1,
			'customer_on_hold_order'    => 1,
			'customer_processing_order' => 1,
			'customer_completed_order'  => 1,
			'customer_refunded_order'   => 1,
			'customer_invoice'          => 0,
			'customer_note'             => 0,
			'customer_reset_password'   => 0,
			'customer_new_account'      => 0,
		);

		if ( ! empty( $listTemplates ) ) {
			foreach ( $settingCurrent as $key => $value ) {
				if ( in_array( $key, $listTemplates )
					&& ! metadata_exists( 'post', $settingDefault[ $key ]['post_id'], '_yaymail_status' )
				) {
					update_post_meta( $settingDefault[ $key ]['post_id'], '_yaymail_status', $value );
				}
			}
		}
	}
	public function addDefaultSetting() {
		$settings_default = array(
			'payment'                      => 2,
			'product_image'                => 0,
			'image_size'                   => 'thumbnail',
			'image_width'                  => '30px',
			'image_height'                 => '30px',
			'product_sku'                  => 1,
			'product_des'                  => 0,
			'product_hyper_links'          => 0,
			'product_regular_price'        => 0,
			'product_item_cost'            => 0,
			'background_color_table_items' => '#e5e5e5',
			'content_items_color'          => '#636363',
			'title_items_color'            => '#7f54b3',
			'container_width'              => '605px',
			'order_url'                    => '',
			'custom_css'                   => '',
			'enable_css_custom'            => 'no',
			'direction_rtl'                => 'ltr',
		);
		if ( ! get_option( 'yaymail_settings' ) ) {
			update_option( 'yaymail_settings', $settings_default );
		} else {
			$yaymail_settings_option       = get_option( 'yaymail_settings' );
			$merge_yaymail_settings_option = array_merge( $settings_default, $yaymail_settings_option );
			update_option( 'yaymail_settings', $merge_yaymail_settings_option );
		}
	}
}
