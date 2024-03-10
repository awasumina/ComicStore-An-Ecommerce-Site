<?php

namespace YayMail\MailBuilder;

use YayMail\Page\Source\CustomPostType;
use YayMail\MailBuilder\PIPTemplate;

defined( 'ABSPATH' ) || exit;
/**
 * Settings Page
 */
class WooTemplate {


	protected static $instance = null;
	private $templateAccount;
	private $templateSubscription;
	private $automatewoo_info                    = null;
	private $automatewoo_referrals_email_content = null;
	private $trackShipArgs                       = null;
	private $FUE_Sending_Email_Variables         = null;
	private $templateGermanizedForWC             = null;
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->doHooks();
		}

		return self::$instance;
	}

	private function doHooks() {
		$this->templateAccount         = array( 'customer_new_account', 'customer_new_account_activation', 'customer_reset_password' );
		$this->templateGermanizedForWC = array( 'sab_simple_invoice', 'sab_cancellation_invoice', 'sab_packing_slip', 'sab_document_admin', 'sab_document' );
		add_filter( 'storeabill_get_template', array( $this, 'storeabill_get_template' ), 100, 5 );
		add_filter( 'wc_get_template', array( $this, 'getTemplateMail' ), 100, 5 );

		if ( has_filter( 'YaymailCreateFollowUpTemplates' ) ) {
			add_action( 'fue_before_variable_replacements', array( $this, 'register_variable_replacements' ), 100, 4 );
			add_filter( 'fue_before_sending_email', array( $this, 'getFollowUpTemplates' ), 100, 3 );
		}

		if ( class_exists( 'WC_PIP_Loader' ) && class_exists( 'YayMailWooPrintInvoices\\templateDefault\\DefaultInvoice' ) && class_exists( 'YayMailWooPrintInvoices\\templateDefault\\DefaultPickList' ) ) {
			PIPTemplate::handle_trigger();
		}
		add_filter( 'retrieve_password_message', array( $this, 'admin_reset_password' ), 100, 4 );
		add_action( 'automatewoo_before_action_run', array( $this, 'automatewoo_before_action_run' ), 10 );
		add_filter( 'automatewoo/referrals/invite_email/mailer', array( $this, 'automatewoo_invite_email' ), 100, 2 );

		if ( class_exists( 'WCFM' ) ) {
			$WCFMWooFM_Template = CustomPostType::postIDByTemplate( 'WCFMWooFM_Template' );
			if ( get_post_meta( $WCFMWooFM_Template, '_yaymail_status', true ) ) {
				global $WCFM;
				remove_action( 'wcfm_email_content_wrapper', array( $WCFM, 'wcfm_email_content_wrapper' ), 10 );
				add_filter( 'wcfm_email_content_wrapper', array( &$this, 'wcfm_email_content_wrapper' ), 1, 2 );
			}
		}

		add_filter( 'wpml_translate_single_string', array( $this, 'yaymail_ywces_new_template' ), 1, 3 );
		// change german market template dir
		$this->yaymail_get_german_market_templates();

		if ( class_exists( 'CARTFLOWS_CA_Loader' ) && function_exists( 'YayMailAddonWcCartAbandonmentRecovery\\init' ) ) {
			add_filter( 'woo_ca_recovery_email_data', array( $this, 'yaymail_addon_ca_recovery_send_mail' ), 10, 1 );
		}

		if ( class_exists( 'WPDesk\\ShopMagic\\Plugin' ) && function_exists( 'YaymailAddonShopMagicForWoocommerce\\init' ) ) {
			add_filter( 'shopmagic/core/action/send_mail/sending', array( $this, 'yaymail_addon_shopmagic_send_mail' ) );
			//add_action( 'shopmagic/core/initialized/v2', array( $this, 'shopmagic_do_action_external_plugins_access' ), 10, 1 );
			add_action( 'shopmagic/core/action/before_execution', array( $this, 'shopmagic_before_send_mail' ), 10, 3 );
		}
	}

	public function shopmagic_before_send_mail( $action, $automation, $event ) {
		$automation_id = $automation->get_id();

		if ( empty( $automation_id ) ) {
			return;
		}

		$shopmagic_data = array(
			'automation' => $automation,
			'action'     => $action,
		);

		session_start();

		$_SESSION['shopmagic_data'] = $shopmagic_data;

	}

	public function shopmagic_do_action_external_plugins_access( $external_plugins_access ) {
		$external_plugins_access->add_template_resolver( new \ShopMagicVendor\WPDesk\View\Resolver\DirResolver( YAYMAIL_ADDON_SHOP_MAGIC_FOR_WC_PLUGIN_PATH . '/views/Template' ) );
	}

	public function yaymail_addon_shopmagic_send_mail( $sending_email ) {
		if ( ! $sending_email->get_email()->is_html() ) {
			return $sending_email;
		}

		$shopmagic_automation = $sending_email->get_automation();
		if ( method_exists( $shopmagic_automation, 'get_id' ) ) {
			$shopmagic_template_id = $shopmagic_automation->get_id();
		}

		if ( isset( $shopmagic_template_id ) ) {
			$shopmagic_template_name = 'ShopMagic_' . $shopmagic_template_id;
		}

		if ( ! empty( $shopmagic_template_name ) ) {
			$postID          = null;
			$postID          = CustomPostType::postIDByTemplate( $shopmagic_template_name );
			$template_status = get_post_meta( $postID, '_yaymail_status', true );
		}

		if ( $template_status && ! empty( $postID ) ) {
			$active_template = file_exists( YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' ) ? YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' : false;

			$args['email']      = $sending_email->get_email();
			$args['automation'] = $sending_email->get_automation();
			$template           = $shopmagic_template_name;
			ob_start();
			include $active_template;
			$template_body = ob_get_contents();
			ob_end_clean();
		}

		if ( ! empty( $template_body ) ) {
			$sending_email->email->message = $template_body;
		}

		return $sending_email;
	}

	public function yaymail_addon_ca_recovery_send_mail( $email_data ) {
		$ca_recovery_template_id = $email_data->email_template_id;

		if ( empty( $ca_recovery_template_id ) ) {
			return $email_data;
		}
		$yaymail_template_id = 'YaymailWcCaRecovery_' . $ca_recovery_template_id;

		if ( ! empty( $yaymail_template_id ) ) {
			$postID          = null;
			$postID          = CustomPostType::postIDByTemplate( $yaymail_template_id );
			$template_status = get_post_meta( $postID, '_yaymail_status', true );
		}

		if ( $template_status && ! empty( $postID ) ) {
			$active_template = file_exists( YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' ) ? YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' : false;

			$args['email'] = $email_data;
			$template      = $yaymail_template_id;
			if ( $active_template ) {
				ob_start();
				include $active_template;
				$template_body = ob_get_contents();
				ob_end_clean();
			}
		}

		if ( ! empty( $template_body ) ) {
			$email_data->email_body = $template_body;
		}

		return $email_data;
	}

	public function yaymail_ywces_new_template( $mail_body_content, $admin_mail_body_text, $mail_body_text ) {
		if ( class_exists( 'YayMailYITHWooCouponEmailSystem\templateDefault\DefaultCouponEmailSystem' ) && class_exists( 'YITH_WC_Coupon_Email_System' ) && false !== strpos( $mail_body_text, 'ywces_mailbody_' ) ) {
			$mail_body_text_explode = explode( 'ywces_mailbody_', $mail_body_text );
			$email_template_type    = $mail_body_text_explode[1];
			$template               = 'YWCES_' . $email_template_type;
			$postID                 = CustomPostType::postIDByTemplate( $template );
			if ( $postID ) {
				if ( get_post_meta( $postID, '_yaymail_status', true ) && ! empty( get_post_meta( $postID, '_yaymail_elements', true ) ) ) {
					$check_YWCES    = true;
					$templateActive = file_exists( YAYMAIL_PLUGIN_PATH . 'views/templates/single-mail-template.php' ) ? YAYMAIL_PLUGIN_PATH . 'views/templates/single-mail-template.php' : false;
					ob_start();
					include $templateActive;
					$template_body = ob_get_contents();
					// Replace newline (\n) in a string to YAYMAIL_YWCES_YAY
					$template_body = str_replace( "\n", 'YAYMAIL_YWCES_YAY', $template_body );
					ob_end_clean();
					return $template_body;
				}
			}
		}

		return $mail_body_content;
	}

	public function yaymail_get_german_market_templates() {
		if ( class_exists( 'Woocommerce_German_Market' ) ) {
			add_filter(
				'wgm_locate_template',
				function( $template, $template_name, $template_path ) {
					if ( 'emails/customer-confirm-order.php' === $template_name ) {
						$postID          = CustomPostType::postIDByTemplate( 'wgm_confirm_order_email' );
						$template_status = get_post_meta( $postID, '_yaymail_status', true );
						if ( $template_status ) {
							return YAYMAIL_PLUGIN_PATH . 'views/templates/emails/customer-confirm-order.php';
						}
					} elseif ( 'emails/double-opt-in-customer-registration.php' === $template_name ) {
						$postID          = CustomPostType::postIDByTemplate( 'wgm_double_opt_in_customer_registration' );
						$template_status = get_post_meta( $postID, '_yaymail_status', true );
						if ( $template_status ) {
							return YAYMAIL_PLUGIN_PATH . 'views/templates/emails/double-opt-in-customer-registration.php';
						}
					} elseif ( 'emails/sepa-mandate.php' === $template_name ) {
						$postID          = CustomPostType::postIDByTemplate( 'wgm_sepa' );
						$template_status = get_post_meta( $postID, '_yaymail_status', true );
						if ( $template_status ) {
							return YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sepa.php';
						}
					}
					return $template;
				},
				100,
				3
			);
		}
	}

	public function wcfm_email_content_wrapper( $content_body, $email_heading ) {
		$template       = 'WCFMWooFM_Template';
		$postID         = CustomPostType::postIDByTemplate( 'WCFMWooFM_Template' );
		$templateActive = file_exists( YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' ) ? YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' : false;
		$args           = array(
			'order'         => null,
			'content_body'  => $content_body,
			'email_heading' => $email_heading,
		);
		ob_start();
		include $templateActive;
		$template_body = ob_get_contents();
		ob_end_clean();
		return $template_body;
	}

	public function automatewoo_invite_email( $mailer, $invite_email ) {
		if ( defined( 'YAYMAIL_ADDON_AUTOMATEWOO' ) || ! empty( YAYMAIL_ADDON_AUTOMATEWOO ) ) {
			$template        = 'AutomateWoo_Referrals_Email';
			$postID          = CustomPostType::postIDByTemplate( $template );
			$template_status = get_post_meta( $postID, '_yaymail_status', true );
			if ( $template_status ) {
				$templateActive = file_exists( YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' ) ? YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' : false;
				ob_start();
				include $templateActive;
				$template_body = ob_get_contents();
				ob_end_clean();
				if ( '' !== $template_body ) {
					$template_body                             = $invite_email->replace_variables( $template_body );
					$this->automatewoo_referrals_email_content = $template_body;
				}
				add_filter(
					'woocommerce_mail_content',
					function( $email_content ) {
						return $this->automatewoo_referrals_email_content;
					},
					100,
					1
				);
			}
		}
		return $mailer;
	}
	public function automatewoo_before_action_run( $action ) {
		if ( defined( 'YAYMAIL_ADDON_AUTOMATEWOO' ) && ! empty( YAYMAIL_ADDON_AUTOMATEWOO ) ) {
			$this->automatewoo_info = $action;
			$template               = 'AutomateWoo_' . $action->workflow->get_id();
			$postID                 = CustomPostType::postIDByTemplate( $template );
			$template_status        = get_post_meta( $postID, '_yaymail_status', true );
			if ( $template_status ) {
				add_filter(
					'woocommerce_mail_content',
					function( $html ) {
						$workflow       = $this->automatewoo_info->workflow;
						$template       = 'AutomateWoo_' . $workflow->get_id();
						$postID         = CustomPostType::postIDByTemplate( $template );
						$templateActive = file_exists( YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' ) ? YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' : false;
						$raw_data       = $workflow->data_layer()->get_raw_data();
						$args           = array(
							'order'        => isset( $raw_data['order'] ) ? $raw_data['order'] : null,
							'subscription' => isset( $raw_data['subscription'] ) ? $raw_data['subscription'] : null,
							'email'        => '',
							'workflow'     => $workflow,
						);

						ob_start();
						include $templateActive;
						$template_body = ob_get_contents();
						ob_end_clean();
						if ( '' !== $template_body ) {
							$template_body = $workflow->variable_processor()->process_field( $template_body, true );
							$email         = new \AutomateWoo\Workflow_Email( $workflow, '', '', $template_body );
							$get_mailer    = $email->get_mailer();
							if ( $workflow->is_tracking_enabled() ) {
								$template_body = $get_mailer->inject_tracking_pixel( $template_body );
								$template_body = $get_mailer->replace_urls_in_content( $template_body );
							}
							$template_body = $get_mailer->style_inline( $template_body );
							return $template_body;
						}
						return $html;
					},
					10
				);
			}
		}
	}

	public function storeabill_get_template( $located, $template_name, $args, $template_path, $default_path ) {
		if ( is_plugin_active( 'yaymail-addon-for-germanized/yaymail-premium-addon-germanized.php' ) || is_plugin_active( 'YayMail-Addons/yaymail-premium-addon-germanized.php' ) ) {
			$this_template  = false;
			$templateActive = file_exists( YAYMAIL_PLUGIN_PATH . 'views/templates/single-mail-template.php' ) ? YAYMAIL_PLUGIN_PATH . 'views/templates/single-mail-template.php' : false;
			$template       = isset( $args['email'] ) && isset( $args['email']->id ) && ! empty( $args['email']->id ) ? $args['email']->id : false;

			if ( $template ) {
				$postID = CustomPostType::postIDByTemplate( $template );
				if ( $postID ) {
					if ( get_post_meta( $postID, '_yaymail_status', true ) && ! empty( get_post_meta( $postID, '_yaymail_elements', true ) ) ) {
						if ( in_array( $template, $this->templateGermanizedForWC ) ) { // template mail with account
							$this_template = $templateActive;
						}
					}
				}
			}
			$this_template = $this_template ? $this_template : $located;
			return $this_template;
		}
		return $located;
	}

	private function __construct() {}
	// define the woocommerce_new_order callback

	public function admin_reset_password( $message, $key, $user_login, $user_data ) {
		$this_template  = false;
		$templateActive = file_exists( YAYMAIL_PLUGIN_PATH . 'views/templates/single-mail-template.php' ) ? YAYMAIL_PLUGIN_PATH . 'views/templates/single-mail-template.php' : false;
		$template       = 'customer_reset_password';
		$email          = array(
			'id'         => 'customer_reset_password',
			'user_login' => $user_login,
			'user_id'    => $user_data->ID,
			'user_email' => $user_data->data->user_email,
			'user_data'  => $user_data,
			'key'        => $key,
		);
		$args           = array(
			'email'         => (object) $email,
			'sent_to_admin' => false,
			'reset_key'     => $key,
		);
		$holder_order   = isset( $args['order'] ) ? $args['order'] : null;
		if ( CustomPostType::postIDByTemplate( $template, $holder_order ) ) {
			$postID = CustomPostType::postIDByTemplate( $template, $holder_order );
			if ( get_post_meta( $postID, '_yaymail_status', true ) && ! empty( get_post_meta( $postID, '_yaymail_elements', true ) ) ) {
				if ( isset( $args['order'] ) || in_array( $template, $this->templateAccount ) ) { // template mail with order
					$this_template = $templateActive;
				}
			}
		}
		$template_path = '';
		$template_name = 'emails/customer-reset-password.php';
		if ( false !== $this_template ) {
			ob_start();
			include $this_template;
			$message = ob_get_contents();
			ob_end_clean();
			$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
			// translators: none.
			$title = sprintf( __( '[%s] Password Reset', 'yaymail' ), $site_name );
			wc_mail( $email['user_email'], $title, $message );
			$message = false;
		}
		return $message;
	}

		// support addon TrackShip for WooCommerce
	public function woocommerce_mail_content( $html ) {
		$trackshipArgs  = $this->trackShipArgs;
		$template       = 'trackship_' . $trackshipArgs['new_status'];
		$postID         = CustomPostType::postIDByTemplate( $template );
		$templateActive = file_exists( YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' ) ? YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' : false;
		$args           = array(
			'order_id'             => isset( $trackshipArgs['order_id'] ) ? $trackshipArgs['order_id'] : '0',
			'show_shipment_status' => isset( $trackshipArgs['show_shipment_status'] ) ? $trackshipArgs['show_shipment_status'] : false,
			'new_status'           => isset( $trackshipArgs['new_status'] ) ? $trackshipArgs['new_status'] : '',
			'tracking_items'       => isset( $trackshipArgs['tracking_items'] ) ? $trackshipArgs['tracking_items'] : array(),
			'shipment_status'      => isset( $trackshipArgs['shipment_status'] ) ? $trackshipArgs['shipment_status'] : array(),
		);

		ob_start();
		include $templateActive;
		$template_body = ob_get_contents();
		ob_end_clean();
		if ( '' !== $template_body ) {
			return $template_body;
		}
		return $html;

	}

	public function getTemplateMail( $located, $template_name, $args, $template_path, $default_path ) {
		// support addon TrackShip for WooCommerce
		if ( 'emails/tracking-info.php' == $template_name ) {
			$this->trackShipArgs = $args;
			if ( isset( $args['new_status'] ) ) {
				$template        = 'trackship_' . $args['new_status'];
				$postID          = CustomPostType::postIDByTemplate( $template );
				$template_status = get_post_meta( $postID, '_yaymail_status', true );
				if ( $template_status ) {
					add_filter( 'woocommerce_mail_content', array( $this, 'woocommerce_mail_content' ), 100 );
				}
			}
		}
		$this_template  = false;
		$templateActive = file_exists( YAYMAIL_PLUGIN_PATH . 'views/templates/single-mail-template.php' ) ? YAYMAIL_PLUGIN_PATH . 'views/templates/single-mail-template.php' : false;
		if ( isset( $args['yith_wc_email'] ) && isset( $args['yith_wc_email']->id ) && ! empty( $args['yith_wc_email']->id ) ) {
			// Get Email ID in yith-woocommerce-multi-vendor-premium
			$template = $args['yith_wc_email']->id;
		} else {
			$template = isset( $args['email'] ) && isset( $args['email']->id ) && ! empty( $args['email']->id ) ? $args['email']->id : false;
			if ( 'dokan-wholesale/' == $template_path ) {
				$template = 'Dokan_Email_Wholesale_Register';
			}
			if ( 'new-user-registration.php' == $template_name ) {
				$template = 'wp_crowdfunding_new_user';
			}
			if ( 'campaign-accepted.php' == $template_name ) {
				$template = 'wp_crowdfunding_campaign_accept';
			}
			if ( 'submit-campaign.php' == $template_name ) {
				$template = 'wp_crowdfunding_submit_campaign';
			}
			if ( 'campaign-updated.php' == $template_name ) {
				$template = 'wp_crowdfunding_campaign_update_email';
			}
			if ( 'new-backed.php' == $template_name ) {
				$template = 'wp_crowdfunding_new_backed';
			}
			if ( 'campaign-target-reached.php' == $template_name ) {
				$template = 'wp_crowdfunding_target_reached_email';
			}
			if ( 'withdraw-request.php' == $template_name ) {
				$template = 'wp_crowdfunding_withdraw_request';
			}
			if ( class_exists( 'WC_Smart_Coupons' ) ) {
				if ( isset( $args['email'] ) && strpos( $located, plugin_dir_path( WC_SC_PLUGIN_FILE ) ) !== false ) {
					$templateName = str_replace( plugin_dir_path( WC_SC_PLUGIN_FILE ) . 'templates/', '', $located );
					if ( 'email.php' == $templateName ) {
						$template   = 'wc_sc_email_coupon';
						$args['id'] = 'wc_sc_email_coupon';
					}
					if ( 'combined-email.php' == $templateName ) {
						$template   = 'wc_sc_combined_email_coupon';
						$args['id'] = 'wc_sc_combined_email_coupon';
					}
					if ( 'acknowledgement-email.php' == $templateName ) {
						$template   = 'wc_sc_acknowledgement_email';
						$args['id'] = 'wc_sc_acknowledgement_email';
					}
				}
			}
			if ( 'emails/waitlist-mailout.php' == $template_name ) {
				$template = 'woocommerce_waitlist_mailout';
			}
			if ( 'emails/waitlist-left.php' == $template_name ) {
				$template = 'woocommerce_waitlist_left_email';
			}
			if ( 'emails/waitlist-joined.php' == $template_name ) {
				$template = 'woocommerce_waitlist_joined_email';
			}
			if ( 'emails/waitlist-new-signup.php' == $template_name ) {
				$template = 'woocommerce_waitlist_signup_email';
			}

			if ( 'emails/dokan-admin-new-booking.php' == $template_name ) {
				$template = 'Dokan_Email_Booking_New';
			}
			if ( 'emails/dokan-customer-booking-cancelled.php' == $template_name ) {
				$template = 'Dokan_Email_Booking_Cancelled_NEW';
			}
		}

		if ( isset( $args['email'] ) && isset( $args['email']->id ) && false !== strpos( get_class( $args['email'] ), 'ORDDD_Email_Delivery_Reminder' ) ) {
			$template .= '_customer';
		}

		if ( isset( $args['email'] ) && is_object( $args['email'] ) && 'WC_GZD_Email_Customer_Cancelled_Order' === get_class( $args['email'] ) && 'customer_failed_order' === $template ) {
			$template = 'customer_cancelled_order';
		}

		// can't load tempalte email-delivery-date.php because it will has error when check out, with plugin WooCommerce Order Delivery
		if ( $template && 'emails/email-delivery-date.php' != $template_name && 'emails/email-order-details.php' != $template_name ) {

			// Yith Stripe

			if ( 'emails/expiring-card-email.php' === $template_name ) {
				$template = 'expiring_card';
			}
			if ( 'emails/renew-needs-action-email.php' === $template_name ) {
				$template = 'renew_needs_action';
			}

			if ( 'emails/admin-notify-approved.php' === $template_name ) {
				$template = 'admin_notify_approved';
			}
			if ( 'customer_partially_refunded_order' === $template ) {
				$template = 'customer_refunded_order';
			}
			if ( CustomPostType::postIDByTemplate( $template ) ) {
				$postID = CustomPostType::postIDByTemplate( $template );
				if ( get_post_meta( $postID, '_yaymail_status', true ) && ! empty( get_post_meta( $postID, '_yaymail_elements', true ) ) ) {
					if ( isset( $args['order'] ) || in_array( $template, $this->templateAccount ) ) { // template mail with order
						$this_template = $templateActive;
					} else {
						$checkHasTempalte = apply_filters( 'yaymail_addon_defined_template', false, $template );
						if ( $checkHasTempalte ) { // template mail with account
							$this_template = $templateActive;
						}
					}
				}
			}
		}
		$this_template = $this_template ? $this_template : $located;
		return $this_template;
	}

	public function register_variable_replacements( $var, $email_data, $email, $queue_item ) {
		$this->FUE_Sending_Email_Variables = $var;
	}

	public function getFollowUpTemplates( $email_data, $email, $queue_item ) {
		if ( has_filter( 'yaymail_follow_up_shortcode' ) ) {
			$templateActive  = file_exists( YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' ) ? YAYMAIL_PLUGIN_PATH . 'views/templates/single-follow-up-mail-template.php' : false;
			$template        = 'follow_up_email_' . $email->id;
			$postID          = CustomPostType::postIDByTemplate( $template );
			$template_status = get_post_meta( $postID, '_yaymail_status', true );
			$args            = array(
				'email_data' => $email_data,
				'email'      => $email,
				'queue_item' => $queue_item,
			);
			if ( $template_status ) {
				ob_start();
				include $templateActive;
				$template_body               = ob_get_contents();
				$FUE_Sending_Email_Variables = $this->FUE_Sending_Email_Variables;
				$template_body               = $FUE_Sending_Email_Variables->apply_replacements( $template_body );
				ob_end_clean();
				$email_data['message'] = $template_body;
			}
		}
		return $email_data;
	}
}
