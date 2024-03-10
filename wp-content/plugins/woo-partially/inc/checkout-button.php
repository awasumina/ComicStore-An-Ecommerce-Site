<?php
if( ! defined('ABSPATH') ) die('Not Allowed');

add_action('woocommerce_after_add_to_cart_form', 'maybe_add_partially_product_button');
add_action('woocommerce_after_cart_totals', 'maybe_add_partially_cart_button');

function maybe_add_partially_cart_button() {
  $gateway = WC_Gateway_Partially::instance();
  $show = $gateway->get_option('button-enabled-cart');

  // option not set, deprecated for new installs
  if (empty($show)) return;

  // see if any products in the cart have been disabled
  foreach (WC()->cart->get_cart() as $cart_item_key => $item) {
    if (get_post_meta( $item['product_id'], 'partially_disabled', true ) == 'yes') {
      $show = 'no';
    }
  }

  // check for a global Partially min
  $cartTotal = WC()->cart->get_total(null);
  $gateway = WC_Gateway_Partially::instance();
  $globalMin = $gateway->get_option('min_amount');
  if ( ! empty($globalMin) && $globalMin > 0 && $cartTotal < $globalMin) $show = 'no';

  // check for a global Partially max
  $globalMax = $gateway->get_option('max_amount');
  if ( ! empty($globalMax) && $globalMax > 0 && $cartTotal > $globalMax) $show = 'no';

  if ($show == 'no') {
      return;
  }

  $defaultOfferId = $gateway->get_option('offer-id');

  // see if any products override the offer
  foreach (WC()->cart->get_cart() as $cart_item_key => $item) {
    $customOffer = get_post_meta( $item['product_id'], 'partially_offer', true );
    if ( ! empty($customOffer)) $defaultOfferId = $customOffer;
  }

  $partially_offer = apply_filters('partially_button_cart_offer', $defaultOfferId);

  // check if they have a custom legacy template
  $legacy_template_name = 'partially-checkout.php';
  $custom_legacy_template = locate_template($legacy_template_name);
  if ($custom_legacy_template) {
    include $custom_legacy_template;
  }
  else {
      // new version
      global $woocommerce;

      $button_config = array(
        'offer' => $partially_offer,
        'amount' => WC()->cart->total,
        'currency' => get_woocommerce_currency(),
        'returnUrl' => get_site_url(null, '/cart'),
        'renderSelector'=>'#partiallyCartButtonContainer',
        'meta' => array(
          'source' => 'woocommerce',
          'items' => array()
        )
      );

      $lang = WC_Gateway_Partially::currentLanguage();
      if ($lang != 'en') $button_config['language'] = $lang;

      // check for a custom image
      $customImage = $gateway->get_option('checkout_button_image');
      if ( ! empty($customImage)) $button_config['imageUrl'] = $customImage;

      if (WC()->cart->shipping_total > 0 || (WC()->cart->tax_total + WC()->cart->shipping_tax_total) > 0) {
          $button_config['meta']['subtotal'] = WC()->cart->subtotal_ex_tax;
      }
      if (WC()->cart->tax_total + WC()->cart->shipping_tax_total > 0) {
        $button_config['meta']['tax'] = WC()->cart->tax_total + WC()->cart->shipping_tax_total;
      }
      if (WC()->cart->shipping_total > 0) {
        $button_config['meta']['shipping'] = WC()->cart->shipping_total;
      }

      foreach (WC()->cart->get_cart() as $cart_item_key => $item) {
          $price = $item['data']->get_price();
          if ( ! $price) {
            $price = round($item['line_subtotal'] / $item['quantity'], 2);
          }
          $data = array(
            'id' => $cart_item_key,
            'name' => addslashes($item['data']->get_name()),
            'price' => $price,
            'quantity' => $item['quantity'],
            'total' => $item['line_total'],
            'product_id' => $item['product_id']
          );

          if ($item['data']->get_sku()) {
            $data['sku'] = $item['data']->get_sku();
          }

          $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($item['product_id']));
          if ($thumb && $thumb[0]) {
            $data['image'] = $thumb[0];
          }
          if ($item['variation_id']) {
            $data['variant_id'] = $item['variation_id'];
          }

          $button_config['meta']['items'] []= $data;
      }

      $button_config_json = json_encode( apply_filters('partially_button_cart_config', $button_config) );

      $default_container_html = '<div id="partiallyCartButtonContainer"></div>';
      $container_html = apply_filters('partially_cart_button_container_html', $default_container_html);

      $asset_base = apply_filters('partially_asset_base', 'https://partial.ly/');
      $js_url = $asset_base . 'js/partially-checkout-button.js';

      echo "
        $container_html
        <script type=\"text/javascript\">
        var cartButtonConfig = $button_config_json;
        if (window.PartiallyButton) {
          var btn = new window.PartiallyButton(cartButtonConfig);
          btn.init();
        }
        else {
          document.partiallyButtonConfig = cartButtonConfig;
          (function() {
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = '$js_url';
            script.async = true;
            document.head.appendChild(script);
          })();
        }
        </script>
      ";

  }
}

function maybe_add_partially_product_button() {
  $gateway = WC_Gateway_Partially::instance();
  global $product;
  $amount = $product->get_price();

  // global enabled
  $show = $gateway->get_option('button-enabled-product');

  // option not set, deprecated for new installs
  if (empty($show)) return;

  // see if specific product is disabled
  if (get_post_meta( get_the_ID(), 'partially_disabled', true ) == 'yes') $show = 'no';

  // check for a global Partially min
  $gateway = WC_Gateway_Partially::instance();
  $globalMin = $gateway->get_option('min_amount');
  if ( ! empty($globalMin) && $globalMin > 0 && $amount < $globalMin) $show = 'no';

  // check for a global Partially max
  $globalMax = $gateway->get_option('max_amount');
  if ( ! empty($globalMax) && $globalMax > 0 && $amount > $globalMax) $show = 'no';

  if ($show == 'no') {
      return;
  }

  // default offer configured in gateway settings
  $defaultOffer = $gateway->get_option('offer-id');

  // check for overridden product offer
  $productOffer = get_post_meta( get_the_ID(), 'partially_offer', true );
  if ( ! empty($productOffer)) $defaultOffer = $productOffer;

  $partially_offer = apply_filters('partially_button_product_offer', $defaultOffer);

  $product_data = $product->get_data();
  $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()));
  if ($thumb && $thumb[0]) {
    $product_data['image'] = $thumb[0];
  }

  $button_config = array(
    'offer' => $partially_offer,
    'quantity' => 1,
    'currency' => get_woocommerce_currency(),
    'returnUrl' => get_permalink( $product->get_id() ),
    'woocommerceProduct' => $product_data,
    'quantitySelector'=>'.quantity input',
    'renderSelector'=>'#partiallyProductButtonContainer'
  );

  $lang = WC_Gateway_Partially::currentLanguage();
  if ($lang != 'en') $button_config['language'] = $lang;

  // check for a custom image
  $customImage = $gateway->get_option('checkout_button_image');
  if ( ! empty($customImage)) $button_config['imageUrl'] = $customImage;

  $button_config_json = json_encode( apply_filters('partially_button_product_config', $button_config) );

  $default_container_html = '<div id="partiallyProductButtonContainer"></div>';
  $container_html = apply_filters('partially_product_button_container_html', $default_container_html);

  $asset_base = apply_filters('partially_asset_base', 'https://partial.ly/');
  $js_url = $asset_base . 'js/partially-checkout-button.js';

  echo "
    $container_html
    <script type=\"text/javascript\">
    document.partiallyButtonConfig = $button_config_json;
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
