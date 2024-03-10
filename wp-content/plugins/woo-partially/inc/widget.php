<?php
if( ! defined('ABSPATH') ) die('Not Allowed');

add_filter('woocommerce_single_product_summary', 'add_partially_widget', 12);

function add_partially_widget() {
    $gateway = WC_Gateway_Partially::instance();

    global $product;
    $amount = $product->get_price();

    // global widget enabled
    $showWidget = $gateway->get_option('widget-enabled');

    // check if this specific product is disabled
    if (get_post_meta( get_the_ID(), 'partially_disabled', true ) == 'yes') $showWidget = 'no';

    // check for a global min
    $globalMin = $gateway->get_option('min_amount');
    if ( ! empty($globalMin) && $globalMin > 0 && $amount < $globalMin) $showWidget = 'no';

    // check for a global max
    $globalMax = $gateway->get_option('max_amount');
    if ( ! empty($globalMax) && $globalMax > 0 && $amount > $globalMax) $showWidget = 'no';

    if ($showWidget == 'no') {
        return;
    }

    // default offer configured in gateway settings
    $offer = $gateway->get_option('offer-id');

    // check for overridden product offer
    $productOffer = get_post_meta( get_the_ID(), 'partially_offer', true );
    if ( ! empty($productOffer)) $offer = $productOffer;

    $style = $gateway->get_option('widget-style');
    $title = $gateway->get_option('widget-title');
    $trigger_text = $gateway->get_option('widget-trigger-text');
    $popup_details = $gateway->get_option('widget-popup-details');

    $widget_config = array(
      'amount' => $amount,
      'offer' => $offer,
      'quantity' => 1,
      'currency' => get_woocommerce_currency(),
      'targetSelector' => '#widgetContainer',
      'style' => $style,
      'title' => $title,
      'actionText' => $trigger_text,
      'popupDetails' => $popup_details,
      'quantitySelector'=>'.quantity input',
      'source' => 'woocommerce',
      'partiallyUrl' => $gateway->get_base_url()
    );

    $lang = WC_Gateway_Partially::currentLanguage();
    if ($lang != 'en') $widget_config['language'] = $lang;

    if ($gateway->get_option('widget-body')) {
      $widget_config['body'] = $gateway->get_option('widget-body');
    }

    // check for checkout button config
    if ($gateway->get_option('widget-checkout-enabled') == 'yes') {
      $widget_config['includeCheckout'] = true;
      $widget_config['checkoutButtonText'] = $gateway->get_option('widget-checkout-button-text');
      $product_data = $product->get_data();
      $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()));
      if ($thumb && $thumb[0]) {
        $product_data['image'] = $thumb[0];
      }
      $widget_config['checkoutButtonConfig'] = array(
        'amount' => $amount,
        'offer' => $offer,
        'quantity' => 1,
        'currency' => get_woocommerce_currency(),
        'returnUrl' => get_permalink( $product->get_id() ),
        'woocommerceProduct' => $product_data,
        'quantitySelector'=>'.quantity input'
      );

      if ($lang != 'en') $widget_config['checkoutButtonConfig']['language'] = $lang;
    }

    $widget_config_json = json_encode( apply_filters('partially_widget_config', $widget_config) );

    $default_container_html = '<div id="widgetContainer"></div>';
    $container_html = apply_filters('partially_widget_container_html', $default_container_html);

    $asset_base = apply_filters('partially_asset_base', $gateway->get_base_url());
    $js_url = $asset_base . '/js/partially-widget.js';

    echo "
      $container_html
      <script type=\"text/javascript\">
      document.partiallyWidgetConfig = $widget_config_json;
      (function() {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = '$js_url';
        script.async = true;
        document.head.appendChild(script);
      })();
      </script>
    ";
}
