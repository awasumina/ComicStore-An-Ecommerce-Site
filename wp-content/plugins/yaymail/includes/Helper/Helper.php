<?php

namespace YayMail\Helper;

defined( 'ABSPATH' ) || exit;
class Helper {


	public static function checkNonce() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'email-nonce' ) ) {
			wp_send_json_error( array( 'mess' => __( 'Nonce is invalid', 'yaymail' ) ) );
		}
	}

	public static function sanitize_array( $var ) {
		if ( is_array( $var ) ) {
			return array_map( 'self::sanitize_array', $var );
		} else {
			return is_scalar( $var ) ? wp_kses_allowed_html( $var ) : $var;
		}
	}

	public static function unsanitize_array( $var ) {
		if ( is_array( $var ) ) {
			return array_map( 'self::unsanitize_array', $var );
		} else {
			return html_entity_decode( $var, ENT_QUOTES, 'UTF-8' );
		}
	}

	// Fix bugs for only Customer Invoice (core code of Woocommerce)
	public static function getCustomerInvoiceSubject( $objEmail ) {
		if ( $objEmail->object && $objEmail->object->has_status( array( 'completed', 'processing' ) ) ) {
			$subject = $objEmail->get_option( 'subject_paid', $objEmail->get_default_subject( true ) );
			return apply_filters( 'woocommerce_email_subject_customer_invoice_paid', $objEmail->format_string( $subject ), $objEmail->object, $objEmail );
		}

		$subject = $objEmail->get_option( 'subject', $objEmail->get_default_subject() );
		return apply_filters( 'woocommerce_email_subject_customer_invoice', $objEmail->format_string( $subject ), $objEmail->object, $objEmail );
	}

	// Get Subject for email WC_Email_New_Booking (Woo Bookings plugin)
	public static function getNewBookingSubject( $objEmail ) {
		$subject = $objEmail->get_option( 'subject', $objEmail->subject );
		return apply_filters( 'woocommerce_email_subject_' . $objEmail->id, $objEmail->format_string( $subject ), $objEmail->object );
	}

	// Check Key Exist
	public static function checkKeyExist( $array, $key, $valueDefault ) {
		if ( isset( $array->$key ) ) {
			return $key;
		} else {
			return $valueDefault;
		}
	}

	public static function preventXSS( $yaymail_elements ) {
		foreach ( $yaymail_elements as $key => $value ) {
			if ( isset( $value['settingRow']['content'] ) ) {
				$yaymail_elements[ $key ]['settingRow']['content'] = wp_kses_post( html_entity_decode( $value['settingRow']['content'], ENT_COMPAT, 'UTF-8' ) );
			}
			if ( isset( $value['settingRow']['contentTitle'] ) ) {
				$yaymail_elements[ $key ]['settingRow']['contentTitle'] = wp_kses_post( html_entity_decode( $value['settingRow']['contentTitle'], ENT_COMPAT, 'UTF-8' ) );
			}
			if ( isset( $value['settingRow']['contentAfter'] ) ) {
				$yaymail_elements[ $key ]['settingRow']['contentAfter'] = wp_kses_post( html_entity_decode( $value['settingRow']['contentAfter'], ENT_COMPAT, 'UTF-8' ) );
			}
			if ( isset( $value['settingRow']['contentBefore'] ) ) {
				$yaymail_elements[ $key ]['settingRow']['contentBefore'] = wp_kses_post( html_entity_decode( $value['settingRow']['contentBefore'], ENT_COMPAT, 'UTF-8' ) );
			}
			if ( isset( $value['settingRow']['col1TtContent'] ) ) {
				$yaymail_elements[ $key ]['settingRow']['col1TtContent'] = wp_kses_post( html_entity_decode( $value['settingRow']['col1TtContent'], ENT_COMPAT, 'UTF-8' ) );
			}
			if ( isset( $value['settingRow']['col2TtContent'] ) ) {
				$yaymail_elements[ $key ]['settingRow']['col2TtContent'] = wp_kses_post( html_entity_decode( $value['settingRow']['col2TtContent'], ENT_COMPAT, 'UTF-8' ) );
			}
			if ( isset( $value['settingRow']['col3TtContent'] ) ) {
				$yaymail_elements[ $key ]['settingRow']['col3TtContent'] = wp_kses_post( html_entity_decode( $value['settingRow']['col3TtContent'], ENT_COMPAT, 'UTF-8' ) );
			}
			if ( isset( $value['settingRow']['HTMLContent'] ) ) {
				$yaymail_elements[ $key ]['settingRow']['HTMLContent'] = wp_kses_post( html_entity_decode( $value['settingRow']['HTMLContent'], ENT_COMPAT, 'UTF-8' ) );
			}
			if ( isset( $value['settingRow']['col2Content'] ) ) {
				$yaymail_elements[ $key ]['settingRow']['col2Content'] = wp_kses_post( html_entity_decode( $value['settingRow']['col2Content'], ENT_COMPAT, 'UTF-8' ) );
			}

			// for column
			// column1
			if ( isset( $value['settingRow']['column1'] ) ) {
				foreach ( $value['settingRow']['column1'] as $key1 => $value1 ) {
					if ( isset( $value1['settingRow']['content'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column1'][ $key1 ]['settingRow']['content'] = wp_kses_post( html_entity_decode( $value1['settingRow']['content'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['contentTitle'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column1'][ $key1 ]['settingRow']['contentTitle'] = wp_kses_post( html_entity_decode( $value1['settingRow']['contentTitle'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['contentAfter'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column1'][ $key1 ]['settingRow']['contentAfter'] = wp_kses_post( html_entity_decode( $value1['settingRow']['contentAfter'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['contentBefore'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column1'][ $key1 ]['settingRow']['contentBefore'] = wp_kses_post( html_entity_decode( $value1['settingRow']['contentBefore'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col1TtContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column1'][ $key1 ]['settingRow']['col1TtContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col1TtContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col2TtContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column1'][ $key1 ]['settingRow']['col2TtContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col2TtContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col3TtContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column1'][ $key1 ]['settingRow']['col3TtContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col3TtContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['HTMLContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column1'][ $key1 ]['settingRow']['HTMLContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['HTMLContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col2Content'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column1'][ $key1 ]['settingRow']['col2Content'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col2Content'], ENT_COMPAT, 'UTF-8' ) );
					}
				}
			}
			// column2
			if ( isset( $value['settingRow']['column2'] ) ) {
				foreach ( $value['settingRow']['column2'] as $key1 => $value1 ) {
					if ( isset( $value1['settingRow']['content'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column2'][ $key1 ]['settingRow']['content'] = wp_kses_post( html_entity_decode( $value1['settingRow']['content'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['contentTitle'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column2'][ $key1 ]['settingRow']['contentTitle'] = wp_kses_post( html_entity_decode( $value1['settingRow']['contentTitle'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['contentAfter'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column2'][ $key1 ]['settingRow']['contentAfter'] = wp_kses_post( html_entity_decode( $value1['settingRow']['contentAfter'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['contentBefore'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column2'][ $key1 ]['settingRow']['contentBefore'] = wp_kses_post( html_entity_decode( $value1['settingRow']['contentBefore'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col1TtContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column2'][ $key1 ]['settingRow']['col1TtContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col1TtContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col2TtContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column2'][ $key1 ]['settingRow']['col2TtContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col2TtContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col3TtContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column2'][ $key1 ]['settingRow']['col3TtContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col3TtContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['HTMLContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column2'][ $key1 ]['settingRow']['HTMLContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['HTMLContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col2Content'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column2'][ $key1 ]['settingRow']['col2Content'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col2Content'], ENT_COMPAT, 'UTF-8' ) );
					}
				}
			}
			// column3
			if ( isset( $value['settingRow']['column3'] ) ) {
				foreach ( $value['settingRow']['column3'] as $key1 => $value1 ) {
					if ( isset( $value1['settingRow']['content'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column3'][ $key1 ]['settingRow']['content'] = wp_kses_post( html_entity_decode( $value1['settingRow']['content'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['contentTitle'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column3'][ $key1 ]['settingRow']['contentTitle'] = wp_kses_post( html_entity_decode( $value1['settingRow']['contentTitle'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['contentAfter'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column3'][ $key1 ]['settingRow']['contentAfter'] = wp_kses_post( html_entity_decode( $value1['settingRow']['contentAfter'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['contentBefore'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column3'][ $key1 ]['settingRow']['contentBefore'] = wp_kses_post( html_entity_decode( $value1['settingRow']['contentBefore'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col1TtContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column3'][ $key1 ]['settingRow']['col1TtContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col1TtContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col2TtContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column3'][ $key1 ]['settingRow']['col2TtContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col2TtContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col3TtContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column3'][ $key1 ]['settingRow']['col3TtContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col3TtContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['HTMLContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column3'][ $key1 ]['settingRow']['HTMLContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['HTMLContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col2Content'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column3'][ $key1 ]['settingRow']['col2Content'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col2Content'], ENT_COMPAT, 'UTF-8' ) );
					}
				}
			}

			// column4
			if ( isset( $value['settingRow']['column4'] ) ) {
				foreach ( $value['settingRow']['column4'] as $key1 => $value1 ) {
					if ( isset( $value1['settingRow']['content'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column4'][ $key1 ]['settingRow']['content'] = wp_kses_post( html_entity_decode( $value1['settingRow']['content'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['contentTitle'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column4'][ $key1 ]['settingRow']['contentTitle'] = wp_kses_post( html_entity_decode( $value1['settingRow']['contentTitle'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['contentAfter'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column4'][ $key1 ]['settingRow']['contentAfter'] = wp_kses_post( html_entity_decode( $value1['settingRow']['contentAfter'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['contentBefore'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column4'][ $key1 ]['settingRow']['contentBefore'] = wp_kses_post( html_entity_decode( $value1['settingRow']['contentBefore'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col1TtContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column4'][ $key1 ]['settingRow']['col1TtContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col1TtContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col2TtContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column4'][ $key1 ]['settingRow']['col2TtContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col2TtContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col3TtContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column4'][ $key1 ]['settingRow']['col3TtContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col3TtContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['HTMLContent'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column4'][ $key1 ]['settingRow']['HTMLContent'] = wp_kses_post( html_entity_decode( $value1['settingRow']['HTMLContent'], ENT_COMPAT, 'UTF-8' ) );
					}
					if ( isset( $value1['settingRow']['col2Content'] ) ) {
						$yaymail_elements[ $key ]['settingRow']['column4'][ $key1 ]['settingRow']['col2Content'] = wp_kses_post( html_entity_decode( $value1['settingRow']['col2Content'], ENT_COMPAT, 'UTF-8' ) );
					}
				}
			}
		}
		return $yaymail_elements;
	}

	public static function isPreview( $is_preview = true ) {
		if ( $is_preview ) {
			return true;
		}
		return false;
	}

	public static function OrderItemsTitle() {
		$orderTitle = array(
			'order_title'                   => __( '[Order #', 'yaymail' ) . wp_kses_post( do_shortcode( '[yaymail_order_id]' ) . '] (' ) . wp_kses_post( do_shortcode( '[yaymail_order_date]' ) . ')' ),
			'product_title'                 => __( 'Product', 'yaymail' ),
			'cost_title'                    => __( 'Cost', 'yaymail' ),
			'quantity_title'                => __( 'Quantity', 'yaymail' ),
			'price_title'                   => __( 'Price', 'yaymail' ),
			'subtoltal_title'               => __( 'Subtotal:', 'yaymail' ),
			'discount_title'                => __( 'Discount:', 'yaymail' ),
			'shipping_title'                => __( 'Shipping:', 'yaymail' ),
			'payment_method_title'          => __( 'Payment method:', 'yaymail' ),
			'fully_refunded'                => __( 'Order fully refunded.', 'yaymail' ),
			'customer_note'                 => __( 'Note:', 'yaymail' ),
			'total_title'                   => __( 'Total:', 'yaymail' ),
			'subscript_id'                  => __( 'ID', 'yaymail' ),
			'subscript_start_date'          => __( 'Start date', 'yaymail' ),
			'subscript_end_date'            => __( 'End date', 'yaymail' ),
			'subscript_recurring_total'     => __( 'Recurring total', 'yaymail' ),
			'subscript_subscription'        => __( 'Subscription', 'yaymail' ),
			'subscript_price'               => __( 'Price', 'yaymail' ),
			'subscript_last_order_date'     => __( 'Last Order Date', 'yaymail' ),
			'subscript_end_of_prepaid_term' => __( 'End of Prepaid Term', 'yaymail' ),
			'subscript_date_suspended'      => __( 'Date Suspended', 'yaymail' ),
		);
		return $orderTitle;
	}

	public static function OrderItemsDownloadsTitle() {
		$orderTitle = array(
			'items_download_header_title'   => __( 'Downloads', 'yaymail' ),
			'items_download_product_title'  => __( 'Product', 'yaymail' ),
			'items_download_expires_title'  => __( 'Expires', 'yaymail' ),
			'items_download_download_title' => __( 'Download', 'yaymail' ),
		);
		return $orderTitle;
	}

	public static function inforShortcode( $postID, $template, $order ) {
		$yaymail_settings     = get_option( 'yaymail_settings' );
		$yaymail_informations = array(
			'post_id'          => $postID,
			'template'         => $template,
			'order'            => $order,
			'yaymail_elements' => get_post_meta( $postID, '_yaymail_elements', true ),
			'general_settings' => array(
				'tableWidth'           => $yaymail_settings['container_width'],
				'emailBackgroundColor' => get_post_meta( $postID, '_email_backgroundColor_settings', true ) ? get_post_meta( $postID, '_email_backgroundColor_settings', true ) : '#ECECEC',
				'textLinkColor'        => get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#7f54b3',
			),
		);
		return $yaymail_informations;
	}

	/**
	 * This function returns an array of allowed HTML tags for a WordPress post, including any custom tags
	 * specified by the user.
	 *
	 * @param cusTags An array of custom HTML tags and their attributes that should be allowed in addition
	 * to the default allowed HTML tags.
	 *
	 * @return an array of allowed HTML tags for a WordPress post, with any custom HTML tags added to the
	 * list. The custom HTML tags are passed as an argument to the function.
	 */
	public static function customAllowedHTMLTags( $cusTags = array() ) {
		$customs_html_attr = $cusTags;
		$allowed_html_tags = wp_kses_allowed_html( 'post' );
		return array_map(
			function ( $item ) use ( $customs_html_attr ) {
				return is_array( $item ) ? array_merge( $item, $customs_html_attr ) : $item;
			},
			$allowed_html_tags
		);
	}

	/**
	 * It replaces the string 'yaymail-style' with ':style' in all the values of an array
	 *
	 * @param value The value to be sanitized.
	 *
	 * @return The value of the array.
	 */
	public static function replaceCustomAllowedHTMLTags( $value ) {
		if ( is_string( $value ) ) {
			return str_replace( ':style', 'yaymail-style', $value );
		} else {
			array_walk_recursive(
				$value,
				function ( &$item, $key ) {
					$item = str_replace( 'yaymail-style', ':style', $item );
				}
			);
			return $value;
		}
	}

}
