<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Gateway_Partially extends WC_Payment_Gateway {

    public static $log = false;
    private static $_instance = NULL;

    private $_lastErrorMessage;

    const PLUGIN_VERSION = '2.2.5';

    const NOTIFICATION_PATH = '/partially-notification';

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        global $woocommerce;
        $this->id 					= 'partially';
        // title and description merchant sees in wp admin
        $this->method_title = __('Partial.ly Payment Plans', 'woo_partially');
        $this->method_description = __('Offer customers the option to pay with a Partial.ly payment plan at checkout. Optionally show a widget on your product page showing payment plans options, or a Partial.ly checkout button on your cart. <a href="https://partial.ly/register?ga_campaign=woocommerce_plugin">Register for a Partial.ly account</a> if you don\'t already have one and start offering payment plans today.', 'woo_partially');
        $this->icon = 'https://d2nacfpe3n8791.cloudfront.net/images/glyph-gradient-sm.png';
        $this->supports = array('products');
        // only init form fields on our settings page
        if (isset($_GET['page']) && $_GET['page'] == 'wc-settings' && isset($_GET['section']) && $_GET['section'] == 'partially') {
          $this->init_form_fields();
        }
        $this->init_settings();
        $this->title = $this->get_option('title');
        // description customer sees at checkout
        $this->description = $this->get_option('description');

        // check for v1.x plugin version options
        if (get_option('partially_settings')) {
          self::log('Detected plugin v1.x settings');
          $old_settings = get_option('partially_settings');
          // get the old offer id
          $this->settings['offer-id'] = $old_settings['partially_offer'];
          self::log('updating offer to '.$this->settings['offer-id'].' and enabling button on cart');
          // automatically enable checkout button on cart
          $this->settings['button-enabled-cart'] = 'yes';
          update_option( $this->get_option_key(), apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings ) );
          // remove old settings
          delete_option('partially_settings');
          self::log('deleted v1.x settings');
        }

        // check for v2.1.x settings
        $baseUrl = $this->get_option('base-url');
        $testMode = $this->get_option('test_mode');
        if ( ! empty($baseUrl) && strpos($baseUrl, 'demo.partial.ly') !== false && empty($testMode)) {
          $this->update_option('test_mode', 'yes');
          $this->update_option('base-url', null);
        }

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ));
    }

    public static function log($message) {
        if (empty(self::$log)) {
            self::$log = new WC_Logger();
        }
        self::$log->add('Partial.ly', $message);
    }

    public function process_admin_options() {
      $result = parent::process_admin_options();

      // maybe show an error if invalid api key entered
      $apiKey = $this->get_option('api-key');
      if ($apiKey) {
        $offersResult = $this->get_offers();
        if ($offersResult['success'] !== true) {
          $this->_lastErrorMessage = $offersResult['error'];
          add_action( 'admin_notices', array($this, 'showError'));
        }
      }

      return $result;
    }

    function init_form_fields() {
      $fields = array(
            'test_mode' => array(
                'title' => __( 'Enable test mode', 'woo_partially' ),
                'type' => 'checkbox',
                'label' => __( 'Use the demo.partial.ly server for testing', 'woo_partially' ),
                'default' => 'no',
                'description' => __( 'Note that your credentials for test mode are different than live mode. Sign up for an account at demo.partial.ly for a testing account', 'woo_partially' ),
                'desc_tip' => true
            ),
            'api-key' => array(
                'title' => __( 'Partial.ly API Key', 'woo_partially' ),
                'label' => __('Your Partial.ly API key can be found in the settings area of Partial.ly merchant portal', 'woo_partially'),
                'type' => 'text',
                'default' => ''
            ),
            'enabled' => array(
                'title' => __( 'Partial.ly gateway enabled', 'woo_partially' ),
                'type' => 'checkbox',
                'label' => __( 'Enable Partial.ly payment method at checkout', 'woo_partially' ),
                'default' => 'yes'
            ),
            'title' => array(
                'title' => __( 'Title', 'woo_partially' ),
                'type' => 'text',
                'description' => __( 'This controls the payment method title which the user sees during checkout.', 'woo_partially' ),
                'default' => __( 'Partial.ly Payment Plan', 'woo_partially' )
            ),
            'description' => array(
                'title' => __( 'Description', 'woo_partially' ),
                'type' => 'text',
                'description' => __( 'This controls the description which the user sees during checkout.', 'woo_partially' ),
                'default' => __( 'Easy and flexible payment plan options from Partial.ly', 'woo_partially' )
            ),
            'gateway_complete_status' => array(
              'title' => __( 'Partial.ly gateway order status', 'woo_partially'),
              'type' => 'select',
              'options' => wc_get_order_statuses(),
              'default' => 'paid',
              'description' => __( 'After a payment plan is created, set the order status to this', 'woo_partially'),
              'desc_tip' => true
            ));

            $min = $this->get_option('min_amount');
            $max = $this->get_option('max_amount');
            if ($min || $max) {
              $fields['min_amount'] = array(
                'title' => __( 'Minimum order amount', 'woo_partially' ),
                'type' => 'text',
                'description' => __( 'Enter an amount here to only enable Partial.ly for orders over the amount given', 'woo_partially' ),
                'desc_tip' => true
              );
              $fields['max_amount'] = array(
                'title' => __( 'Maximum order amount', 'woo_partially' ),
                'type' => 'text',
                'description' => __( 'Enter an amount here to only enable Partial.ly for orders less than the amount given', 'woo_partially' ),
                'desc_tip' => true
              );
            }

            $offerField = array(
                'title' => __( 'Partial.ly Offer ID', 'woo_partially' ),
                'type' => 'text',
                'description'=>__('Offer ID to use. Create an offer in the Partial.ly merchant portal', 'woo_partially'),
                'desc_tip'=>true
            );

            $apiKey = $this->get_option('api-key');
            if ($apiKey) {
              $offersResult = $this->get_offers();
              if ($offersResult['success'] === true) {
                $opts = array('' => __( 'Not set', 'woo_partially' ) );
                foreach ($offersResult['data'] as $o) $opts[$o['id']] = $o['name'];
                $offerField = array(
                    'title' => __( 'Partial.ly Offer', 'woo_partially' ),
                    'type' => 'select',
                    'options'=>$opts
                );
              }
            }

            $fields['offer-id'] = $offerField;

        $rest_fields = array(
          'gateway_send_image' => array(
              'title' => __('Send product images to Partial.ly', 'woo_partially'),
              'type' => 'checkbox',
              'default' => 'yes',
              'description' => __('Send product thumbnails to Partial.ly to display in Partial.ly checkout', 'woo_partially'),
              'desc_tip' => true
          ),
          'widget'=>array(
              'title'=> __('Widget options', 'woo_partially'),
              'type'=>'title',
              'description'=>''
            ),
            'widget-enabled' => array(
              'title' => __('Partial.ly widget enabled', 'woo_partially'),
              'label' => __('Enable Partial.ly widget for your product page, shows available Partial.ly options', 'woo_partially'),
              'type' => 'checkbox',
              'default' => 'yes'
            ),
            'widget-style' => array(
              'title' => __('Widget style', 'woo_partially'),
              'type' => 'select',
              'default'=>'stacked',
              'options' => array(
                'stacked' => __('stacked', 'woo_partially'),
                'thin' => __('thin', 'woo_partially')
              )
            ),
            'widget-title' => array(
                'title' => __( 'Widget title', 'woo_partially' ),
                'type' => 'text',
                'default'=>'Flexible Payments'
            ),
            'widget-body' => array(
                'title' => __( 'Widget body', 'woo_partially' ),
                'type' => 'text',
                'description'=>__('Customize the text for the widget. Leave empty for default text', 'woo_partially'),
                'desc_tip'=>true
            ),
            'widget-trigger-text' => array(
                'title' => __( 'Widget trigger text', 'woo_partially' ),
                'type' => 'text',
                'default'=>'learn more'
            ),
            'widget-popup-details' => array(
                'title' => __( 'Widget popup details', 'woo_partially' ),
                'type' => 'textarea'
            )
        );

        // check if we should add widget checkout fields
        if ( ! empty($this->get_option('widget-checkout-enabled'))) {
          $rest_fields['widget-checkout-enabled'] = array(
            'title' => __('Widget checkout enabled', 'woo_partially'),
            'label' => __('Enable Partial.ly checkout directly from the widget', 'woo_partially'),
            'type' => 'checkbox',
            'default' => 'no'
          );
          $rest_fields['widget-checkout-button-text'] = array(
            'title' => __( 'Widget checkout button text', 'woo_partially' ),
            'type' => 'text',
            'default'=>__('Purchase with Partial.ly', 'woo_partially')
          );
        }

        // check if we should add checkout button fields
        if ( ! empty($this->get_option('button-enabled-cart')) || ! empty($this->get_option('button-enabled-product'))) {
          $rest_fields['checkout-button'] = array(
            'title'=> __('Checkout button options', 'woo_partially'),
            'type'=>'title',
            'description'=>''
          );
          $rest_fields['button-enabled-cart'] = array(
            'title' => __('Partial.ly button on cart', 'woo_partially'),
            'label' => __('Enable Partial.ly button on your cart for direct checkout on Partial.ly', 'woo_partially'),
            'type' => 'checkbox',
            'default' => 'no'
          );
          $rest_fields['button-enabled-product'] = array(
            'title' => __('Partial.ly button on product pages', 'woo_partially'),
            'label' => __('Enable Partial.ly button on your product landing pages for direct checkout on Partial.ly', 'woo_partially'),
            'type' => 'checkbox',
            'default' => 'no'
          );
          $rest_fields['checkout_button_image'] = array(
            'title' => __( 'Checkout button image', 'woo_partially' ),
            'label' => __('Image to use for the checkout button', 'woo_partially'),
            'type' => 'text',
            'default' => 'https://d2nacfpe3n8791.cloudfront.net/images/buttons/purchase-with.png',
            'desc_tip' => true,
            'description' => __('Override the default image for the Partial.ly checkout button. See the offer integration tool in the Partial.ly merchant portal for options', 'woo_partially')
          );
        }

        $this->form_fields = array_merge($fields, $rest_fields);
    }

    function process_payment($order_id) {
        global $woocommerce;
        if( function_exists("wc_get_order") ) {
            $order = wc_get_order($order_id);
        }
        else {
            $order = new WC_Order($order_id);
        }

        return $this->get_redirect_url($order);
    }

    function get_redirect_url($order) {
        // create array of data to post to partial.ly gateway api
        $offer_id = $this->get_option('offer-id');

        // see if any products have a custom offer
        foreach (WC()->cart->get_cart() as $cart_item_key => $item) {
          $customOffer = get_post_meta( $item['product_id'], 'partially_offer', true );
          if ( ! empty($customOffer)) $offer_id = $customOffer;
        }

        global $wp_version;

        $body = array(
            'payment_plan' => array(
              'offer_id' => apply_filters('partially_gateway_offer', $offer_id),
              'integration' => 'woocommerce',
              'integration_id' => (string) $order->get_id(),
              'amount' => $order->get_total(),
              'subtotal' => $order->get_subtotal(),
              'currency' => get_woocommerce_currency(),
              'customer' => array(
                  'first_name' => $order->get_billing_first_name(),
                  'last_name' => $order->get_billing_last_name(),
                  'email' => $order->get_billing_email(),
                  'phone' => $order->get_billing_phone(),
              ),
              'shipto_address' => $order->get_shipping_address_1(),
              'shipto_address2' => $order->get_shipping_address_2(),
              'shipto_city' => $order->get_shipping_city(),
              'shipto_state' => $order->get_shipping_state(),
              'shipto_postal_code' => $order->get_shipping_postcode(),
              'shipto_country' => $order->get_shipping_country(),
              'meta' => array(
                'checkout_complete_url' => $this->get_return_url($order),
                'checkout_notify_url' => get_site_url(null, self::NOTIFICATION_PATH),
                'items' => array(),
                'plugin_version' => self::PLUGIN_VERSION,
                'wp_version' => $wp_version,
                'wc_version' => WC_VERSION
              )
            ),
            'context' => $this->buildContextForOrder($order)
        );

        if( method_exists($order, "get_cancel_order_url_raw") ) {
            $body['payment_plan']['meta']['checkout_cancel_url'] = $order->get_cancel_order_url_raw();
        }
        else {
            $body['payment_plan']['meta']['checkout_cancel_url'] = $order->get_cancel_order_url();
        }

        if (count($order->get_items())) {
            foreach ($order->get_items() as $item) {
              $this->log(print_r($item, true));
                if ($item['variation_id']) {
                    if(function_exists("wc_get_product")) {
                        $product = wc_get_product($item['variation_id']);
                    }
                    else {
                        $product = new WC_Product($item['variation_id']);
                    }
                } else {
                    if(function_exists("wc_get_product")) {
                        $product = wc_get_product($item['product_id']);
                    }
                    else {
                        $product = new WC_Product($item['product_id']);
                    }
                }
                $qty = $item['qty'];
                $price = round($item['line_subtotal'] / $qty, 2);
                $item_data = array(
                    'name' => $item['name'],
                    'sku' => $product->get_sku(),
                    'price' => $price,
                    'quantity' => $qty,
                    'total' => $item['line_total'],
                    'product_id' => $item['product_id']
                );
                if ($item['variation_id']) $item_data['variation_id'] = $item['variation_id'];
                if ($this->get_option('gateway_send_image') != 'no') {
                  $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($item['product_id']));
                  if ($thumb && $thumb[0]) $item_data['image'] = $thumb[0];
                }
                array_push($body['payment_plan']['meta']['items'], $item_data);
            }
        }

        // shipping
        if ($body['context']['shipping_total'] > 0) {
          $body['payment_plan']['meta']['shipping'] = array(
            'name' => $order->get_shipping_method(),
            // TODO exclude tax?
            'amount' => $body['context']['shipping_total']
          );
        }

        // tax
        $totalTax = $order->get_total_tax();
        // get_total_tax is supposed to return a float, casting shouldn't be necessary
        if ($totalTax > 0) $body['payment_plan']['meta']['tax'] = (float) $totalTax;

        // discounts
        $discount = $order->get_total_discount();
        if ($discount > 0) {
          // TODO try to derive a name/description from coupons array?
          $body['payment_plan']['meta']['discount'] = (float) $discount;
        }

        // check for fees
        if ($order->get_total_fees() > 0) {
          $body['payment_plan']['meta']['fees'] = array();
          foreach ($order->get_fees() as $fee) {
            if ($fee->get_total() > 0) {
              $feeName = $fee->get_name();
              // check for woocommerce-checkout-add-ons
              if ($fee->get_meta('_wc_checkout_add_on_id') && class_exists('SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_Factory')) {
                $addOn = SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_Factory::get_add_on( $fee->get_meta('_wc_checkout_add_on_id') );
                $label = $addOn->get_label();
                $feeName = empty($label) ? $addOn->get_name() : $label;
              }
              $body['payment_plan']['meta']['fees'] []= array(
                'name' => $feeName,
                'amount' => $fee->get_total()
              );
            }
          }
        }

        $request = array(
            'headers' => array(
                'Content-Type' 	=> 'application/json',
                'Authorization' => 'Bearer ' . $this->get_option('api-key')
            ),
            'body' => json_encode( apply_filters('partially_gateway_settings', $body) )
        );

        $response = wp_remote_post($this->get_create_checkout_url(), $request);
        $response_status = wp_remote_retrieve_response_code($response);

        if ( $response_status == 200) {
          $json = json_decode(wp_remote_retrieve_body($response));

          // see if we should add a language query string parameter
          $redirect_url = $json->gateway_purchase_url;

          $lang = self::currentLanguage();
          if ($lang != 'en') {
            $redirect_url .= strpos($redirect_url, '?') === false ? '?' : '&';
            $redirect_url .= 'language=' . apply_filters('partially_gateway_language', $lang);
          }

          return array(
              'result' => 'success',
              'redirect' => $redirect_url
          );
        }
        else {
          // try to parse an error message from the response
          $json = json_decode(wp_remote_retrieve_body($response));
          if ($json !== null && ! empty($json->message)) {
            // log the error message
            $this->log("Error checking out with Partial.ly: {$json->message}");
            // show message
            wc_add_notice( __('Could not checkout with Partial.ly.', 'woo_partially') .' '. $json->message, 'error' );
          }
          elseif ($json !== null && ! empty($json->error)) {
            // log the error message
            $this->log("Error checking out with Partial.ly: {$json->error}");
            // show message
            wc_add_notice( __('Could not checkout with Partial.ly.', 'woo_partially') .' '. $json->error, 'error' );
          }
          else {
            // general error, we couldn't parse json response
            // log this
            $this->log("Unknown error checking out with Partial.ly, got response status code $response_status");
            wc_add_notice( __('Unknown error checking out with Partial.ly.', 'woo_partially'), 'error' );
          }

          return;
        }
    }

    function buildContextForOrder($order) {
      $context = $order->get_data();

      // need to cast some keys to numeric
      $castFields = array(
        'discount_total',
        'discount_tax',
        'shipping_total',
        'shipping_tax',
        'cart_tax',
        'total',
        'total_tax'
      );
      foreach ($castFields as $field) $context[$field] = $context[$field] * 1;

      $itemCastFields = array(
        'subtotal',
        'subtotal_tax',
        'total',
        'total_tax'
      );

      $context['line_items'] = [];

      foreach ($order->get_items() as $item) {
        $lineItem = $item->get_data();

        foreach ($itemCastFields as $field) $lineItem[$field] = $lineItem[$field] * 1;

        $context['line_items'] []= $lineItem;
      }

      return $context;
    }

    function get_transaction_url($order) {
      if ($order) {
        $plan_id = $order->get_meta('_partially_id');
        if ($plan_id) return $this->get_base_url()."/merchant/plan/$plan_id";
        else return '';
      }
      else {
        return '';
      }
    }

    function partially_payment_callback($plan) {
      $order_id = $plan->integration_id;
      $payment_plan_id = $plan->id;
      self::log("partially_payment_callback running for order $order_id and plan $payment_plan_id");
        if(function_exists("wc_get_order")) {
            $order = wc_get_order($order_id);
        }
        else {
            $order = new WC_Order($order_id);
        }

        if ( ! $order) {
          self::log("could not load order $order_id in gateway notification for plan $payment_plan_id");
        }

        $status = apply_filters('partially_order_created_status', $this->get_option('gateway_complete_status'));

        // only do this for partially payments
        if ($order && $order->get_payment_method() == 'partially') {
          // set transaction_id to plan number, as this will be displayed to end users
          $order->set_transaction_id($plan->number);
          // set a custom posta meta for plan id
          $order->update_meta_data('_partially_id', $plan->id);
          $order->save();
          if ($status == 'paid') {
            // no "paid" status in woo, comparable is "completed"
            $order->update_status('completed', __('Partial.ly payment plan created.', 'woo_partially'));
            $order->payment_complete();
          }
          else {
            self::log("updating order $order_id status to $status");
            $order->update_status($status, __('Partial.ly payment plan created.', 'woo_partially'));
            if (function_exists('wc_reduce_stock_levels')) {
              wc_reduce_stock_levels($order_id);
            }
            else {
              $order->reduce_order_stock();
            }
          }
        }
        else {
          self::log("order not found or payment method not partially order $order_id in gateway notification for plan $payment_plan_id");
        }

        return $order_id;
    }

    public function handlePartiallyNotification() {
      // validate request is from partially
      $key = $this->get_option('api-key');
      $raw_body = file_get_contents('php://input');
      $recv_sig = isset($_SERVER['HTTP_PARTIALLY_SIGNATURE']) ? $_SERVER['HTTP_PARTIALLY_SIGNATURE'] : '';
      $calc_sig = hash_hmac('sha256', $raw_body, $key);
      if ($calc_sig == $recv_sig) {
        // parse the json
        $json = json_decode($raw_body);
        if ($json->event == 'gateway_checkout_complete') {
          // get order and update status
          $order_id = $json->payment_plan->integration_id;
          $this->partially_payment_callback($json->payment_plan);
          echo 'ok';
        }
        else {
          echo 'unsupported event received';
        }
      }
      else {
        header('HTTP/1.1 401 Unauthorized', true, 401);
        echo 'invalid signature received';
        $this->log('received notification with invalid signature');
      }
    }

    function get_base_url() {
        if ($this->get_option('test_mode') == 'yes') return 'https://demo.partial.ly';
        else return 'https://partial.ly';
    }


    function get_create_checkout_url() {
      return $this->get_base_url() . '/api/v1/gateway_purchase_url';
    }

    function get_offers() {
      $result = array('success' => false, 'error' => 'unknown error');
      $req = array(
          'headers' => array(
              'Content-Type' 	=> 'application/json',
              'Authorization' => 'Bearer ' . $this->get_option('api-key')
          )
      );
      $url = $this->get_base_url() . '/api/offer';
      $res = wp_remote_get($url, $req);
      if (is_array($res)) {

        $status = wp_remote_retrieve_response_code($res);

        $parsedBody = json_decode( wp_remote_retrieve_body($res), true );
        if ($status == 200) {
          $result['success'] = true;
          $result['data'] = $parsedBody;
          unset($result['error']);
        }
        else if (isset($parsedBody['error'])) $result['error'] = $parsedBody['error'];
        else if (isset($parsedBody['message'])) $result['error'] = $parsedBody['message'];
      }

      return $result;
    }

    function showError() {
      echo '<div class="error notice"><p>Error from Partial.ly API: '.$this->_lastErrorMessage.'</p></div>';
    }

    function hasSetPerProductOptions() {
      global $wpdb;
      $table = _get_meta_table('post');
      // are any products marked as disabled for partially
      // do any products have a custom offer set
      $n = $wpdb->query("select * from $table where meta_key in ('partially_offer', 'partially_disabled')");

      return $n > 0;
    }

    public static function currentLanguage() {
      $locale = get_locale();
      $locale_parts = explode('_', $locale);
      return $locale_parts[0];
    }

}
