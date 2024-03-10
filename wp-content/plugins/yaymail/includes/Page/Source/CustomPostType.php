<?php
namespace YayMail\Page\Source;

use YayMail\Helper\Helper;

defined( 'ABSPATH' ) || exit;
/**
 * Plugin activate/deactivate logic
 */
class CustomPostType {

	public static function checkEmailTemplateExist( $args = false, $order = null ) {
		if ( false != $args ) {
			if ( ! isset( $GLOBALS[ 'yaymail_template_' . $args['email_template'] ] ) ) {
				$default = array(
					'post_type'      => 'yaymail_template',
					'post_status'    => array( 'publish', 'pending', 'future' ),
					'posts_per_page' => '1',
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'key'     => '_yaymail_template',
							'value'   => $args['email_template'],
							'compare' => '=',
						),
						array(
							'key'     => '_yaymail_template_language',
							'compare' => 'NOT EXISTS',
							'value'   => '',
						),
					),
				);
				$res     = false;
				$query   = new \WP_Query( $default );
				if ( $query->have_posts() ) {
					$posts = $query->get_posts();
					foreach ( $posts as $post ) {
						$res = $post->ID;
						break;
					}
				}
				wp_reset_postdata();
				$GLOBALS[ 'yaymail_template_' . $args['email_template'] ] = $res;
			}
			return $GLOBALS[ 'yaymail_template_' . $args['email_template'] ];
		}
		return false;
	}

	public static function getListPostTemplate() {

		$posts = get_posts(
			array(
				'post_type'   => 'yaymail_template',
				'post_status' => array( 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit' ),
				'numberposts' => -1,
			)
		);
		return $posts;
	}

	public static function templateEnableDisable( $getPostID = true ) {
		$template_export = array();
		$posts           = self::getListPostTemplate();
		if ( count( $posts ) > 0 ) {
			foreach ( $posts as $key => $post ) {
				$template              = get_post_meta( $post->ID, '_yaymail_template', true );
				$template              = get_post_meta( $post->ID, '_yaymail_template', true );
					$template_language = get_post_meta( $post->ID, '_yaymail_template_language', true );
				if ( isset( $list_use_temp[ $template ][ $template_language ] )
					&& isset( $list_use_temp[ $template ][ $template_language ]['prev_id'] ) ) {
					wp_delete_post( $post->ID );
				} else {
					$list_use_temp[ $template ][ $template_language ]['prev_id'] = $post->ID;
					if ( ( ! $template_language ) || 'en' === $template_language ) {
						if ( $getPostID ) {
							$template_export[ $template ]['post_id']         = $post->ID;
							$template_export[ $template ]['_yaymail_status'] = get_post_meta( $post->ID, '_yaymail_status', true );
						} else {
							$template_export[ $template ] = get_post_meta( $post->ID, '_yaymail_status', true );
						}
					}
				}
			}
		}
		return $template_export;
	}
	public static function insert( $args = false ) {
		if ( false != $args && is_array( $args ) ) {
			$arr       = array(
				'post_content'  => $args['mess'],
				'post_date'     => $args['post_date'],
				'post_date_gmt' => $args['post_date'],
				'post_type'     => $args['post_type'],
				'post_title'    => wp_trim_words( $args['mess'], 200 ),
				'post_status'   => 'publish',
			);
			$insert_id = wp_insert_post( $arr );
			if ( 'yaymail_template' == $args['post_type'] ) {
				$orderItemsTitle          = Helper::OrderItemsTitle();
				$orderItemsDownloadsTitle = Helper::OrderItemsDownloadsTitle();

				update_post_meta( $insert_id, '_yaymail_email_order_item_title', $orderItemsTitle );
				update_post_meta( $insert_id, '_yaymail_email_order_item_download_title', $orderItemsDownloadsTitle );
				update_post_meta( $insert_id, '_yaymail_template', $args['_yaymail_template'] );
				if ( isset( $args['_yaymail_template_language'] ) ) {
					update_post_meta( $insert_id, '_yaymail_template_language', $args['_yaymail_template_language'] );
				}
				update_post_meta( $insert_id, '_yaymail_elements', $args['_yaymail_elements'] );
				update_post_meta( $insert_id, '_yaymail_email_backgroundColor_settings', $args['_email_backgroundColor_settings'] );
				update_post_meta( $insert_id, '_yaymail_status', 0 );
			}
			return $insert_id;
		}
		return false;
	}

	public static function postIDByTemplate( $template ) {
		$agrs_template = array( 'email_template' => $template );
		if ( self::checkEmailTemplateExist( $agrs_template ) ) {
			$postID = self::checkEmailTemplateExist( $agrs_template );
			return $postID;
		}
		return false;
	}
	public static function postIDByTemplateLanguage( $template, $language ) {
		$args    = array(
			'email_template' => $template,
			'email_language' => $language,
		);
		$default = array(
			'post_type'   => 'yaymail_template',
			'post_status' => array( 'publish', 'pending', 'future' ),
			'meta_query'  => array(
				'relation' => 'AND',
				array(
					'key'     => '_yaymail_template',
					'value'   => $args['email_template'],
					'compare' => '=',
				),
				array(
					'key'     => '_yaymail_template_language',
					'compare' => '' === $args['email_language'] ? 'NOT EXISTS' : '=',
					'value'   => '' === $args['email_language'] ? '' : $args['email_language'],
				),
			),
		);
		$posts   = new \WP_Query( $default );
		if ( $posts->have_posts() ) {
			return $posts->post->ID;
		}
		return false;
	}

	public static function getTemplateExport() {
		$template_export                    = array();
		$template_export['yaymail_version'] = get_option( 'yaymail_version' );
		$posts                              = self::getListPostTemplate();
		if ( count( $posts ) > 0 ) {
			foreach ( $posts as $key => $post ) {
				$template_export['yaymailTemplateExport'][ $key ]['_yaymail_template']          = get_post_meta( $post->ID, '_yaymail_template', true );
				$template_export['yaymailTemplateExport'][ $key ]['_yaymail_elements']          = get_post_meta( $post->ID, '_yaymail_elements', true );
				$template_export['yaymailTemplateExport'][ $key ]['_yaymail_template_language'] = get_post_meta( $post->ID, '_yaymail_template_language', true );
			}
		}
		return $template_export;
	}

	public static function getListOrders() {
		$data_orders = array();

		if ( function_exists( 'WC' ) ) {
			$wc_list_orders = wc_get_orders(
				array(
					'limit' => 50,
				)
			);

			foreach ( $wc_list_orders as $order ) {
				if ( method_exists( $order, 'get_id' ) && method_exists( $order, 'get_order_number' ) ) {
					$order_id     = strval( $order->get_id() );
					$order_number = $order->get_order_number();
					$email        = method_exists( $order, 'get_billing_email' ) ? $order->get_billing_email() : '';
					$first_name   = method_exists( $order, 'get_billing_first_name' ) ? $order->get_billing_first_name() : '';
					$last_name    = method_exists( $order, 'get_billing_last_name' ) ? $order->get_billing_last_name() : '';

					$data_orders[] = array(
						'id'         => $order_id,
						'id_real'    => $order_number,
						'email'      => $email,
						'first_name' => $first_name,
						'last_name'  => $last_name,
					);
				}
			}
		}
		return $data_orders;
	}

}
