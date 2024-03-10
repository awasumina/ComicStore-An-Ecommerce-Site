<?php

defined( 'ABSPATH' ) || exit;
use YayMail\Page\Source\CustomPostType;
use YayMail\Helper\Helper;
$is_preview            = Helper::isPreview( $this->preview_mail );
$text_align            = is_rtl() ? 'right' : 'left';
$sent_to_admin         = ( isset( $sent_to_admin ) ? $sent_to_admin : false );
$plain_text            = ( isset( $plain_text ) ? $plain_text : '' );
$email                 = ( isset( $email ) ? $email : '' );
$postID                = CustomPostType::postIDByTemplate( $this->template );
$yaymail_settings      = get_option( 'yaymail_settings' );
$yaymail_template      = get_post_meta( $postID, '_yaymail_template', true );
$order_item_title      = get_post_meta( $postID, '_yaymail_email_order_item_title', true );
$product_title         = $is_preview ? '{{product_title}}' : ( false != $order_item_title && isset( $order_item_title['product_title'] ) ? $order_item_title['product_title'] : 'Product' );
$cost_title            = $is_preview ? '{{cost_title}}' : ( false != $order_item_title && isset( $order_item_title['cost_title'] ) ? $order_item_title['cost_title'] : 'Cost' );
$quantity_title        = $is_preview ? '{{quantity_title}}' : ( false != $order_item_title && isset( $order_item_title['quantity_title'] ) ? $order_item_title['quantity_title'] : 'Quantity' );
$price_title           = $is_preview ? '{{price_title}}' : ( false != $order_item_title && isset( $order_item_title['price_title'] ) ? $order_item_title['price_title'] : 'Price' );
$subtoltal_title       = $is_preview ? '{{subtoltal_title}}' : ( false != $order_item_title && isset( $order_item_title['subtoltal_title'] ) ? $order_item_title['subtoltal_title'] : 'Subtotal:' );
$discount_title        = $is_preview ? '{{discount_title}}' : ( false != $order_item_title && isset( $order_item_title['discount_title'] ) ? $order_item_title['discount_title'] : 'Discount:' );
$shipping_title        = $is_preview ? '{{shipping_title}}' : ( false != $order_item_title && isset( $order_item_title['shipping_title'] ) ? $order_item_title['shipping_title'] : 'Shipping:' );
$payment_method_title  = $is_preview ? '{{payment_method_title}}' : ( false != $order_item_title && isset( $order_item_title['payment_method_title'] ) ? $order_item_title['payment_method_title'] : 'Payment method:' );
$total_title           = $is_preview ? '{{total_title}}' : ( false != $order_item_title && isset( $order_item_title['total_title'] ) ? $order_item_title['total_title'] : 'Total:' );
$fully_refunded        = $is_preview ? '{{fully_refunded}}' : ( false != $order_item_title && isset( $order_item_title['fully_refunded'] ) ? $order_item_title['fully_refunded'] : 'Order fully refunded.' );
$customer_note         = $is_preview ? '{{customer_note}}' : ( false != $order_item_title && isset( $order_item_title['customer_note'] ) ? $order_item_title['customer_note'] : 'Note:' );
$get_order_item_totals = array(
	'cart_subtotal'  => $subtoltal_title,
	'discount'       => $discount_title,
	'shipping'       => $shipping_title,
	'payment_method' => $payment_method_title,
	'order_total'    => $total_title,
	'refund_0'       => $fully_refunded,
	'customer_note'  => $customer_note,
);

$get_order_item_totals_class = array(
	'cart_subtotal'  => 'yaymail_item_subtoltal_title',
	'discount'       => 'yaymail_item_discount_title',
	'payment_method' => 'yaymail_item_payment_method_title',
	'order_total'    => 'yaymail_item_total_title',
	'shipping'       => 'yaymail_item_shipping_title',
	'refund_0'       => 'yaymail_item_fully_refunded',
	'tax'            => 'yaymail_item_tax',
	'customer_note'  => 'yaymail_item_customer_note',
);
$item_content_class          = array(
	'cart_subtotal'  => 'yaymail_item_subtoltal_content',
	'payment_method' => 'yaymail_item_payment_method_content',
	'order_total'    => 'yaymail_item_total_content',
	'shipping'       => 'yaymail_item_shipping_content',
	'refund_0'       => 'yaymail_item_fully_refunded_content',
	'customer_note'  => 'yaymail_item_customer_note_content',
);
$borderColor                 = isset( $atts['bordercolor'] ) && $atts['bordercolor'] ? 'border-color:' . html_entity_decode( $atts['bordercolor'], ENT_QUOTES, 'UTF-8' ) : 'border-color:inherit';
$textColor                   = isset( $atts['textcolor'] ) && $atts['textcolor'] ? 'color:' . html_entity_decode( $atts['textcolor'], ENT_QUOTES, 'UTF-8' ) : 'color:inherit';
$productItemCost             = isset( $yaymail_settings['product_item_cost'] ) ? $yaymail_settings['product_item_cost'] : 0;
?>

<thead class="yaymail_element_head_order_item">
	<tr style="word-break: normal">
		<th colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_product_title_colspan', 1, $yaymail_template ) ); ?>" class="td yaymail_item_product_title" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;">
			<?php esc_html_e( $product_title, 'woocommerce' ); ?>
		</th>
		<?php if ( $productItemCost ) { ?>
			<th class="td yaymail_item_price_per_item" colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_cost_colspan', 1, $yaymail_template ) ); ?>" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;">
				<?php esc_html_e( $cost_title, 'woocommerce' ); ?>
			</th>
		<?php } ?>
		<th colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_quantity_colspan', 1, $yaymail_template ) ); ?>" class="td yaymail_item_quantity_title" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;">
			<?php esc_html_e( $quantity_title, 'woocommerce' ); ?>
		</th>
		<th colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_price_colspan', 1, $yaymail_template ) ); ?>" class="td yaymail_item_price_title" scope="col" style="width: 30%;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;">
			<?php esc_html_e( $price_title, 'woocommerce' ); ?>
		</th>
	</tr>
</thead>
<tbody class="yaymail_element_body_order_item">
	<?php
	echo wp_kses_post(
		$this->ordetItemTables(
			$order,
			array(
				'show_sku'      => $sent_to_admin,
				'show_image'    => false,
				'image_size'    => array( 32, 32 ),
				'plain_text'    => $plain_text,
				'sent_to_admin' => $sent_to_admin,
				'border_color'  => $borderColor,
				'text_color'    => $textColor,
			)
		)
	);

	?>
</tbody>
<tfoot class="yaymail_element_foot_order_item">	
	<?php
	$totalItem = $order->get_order_item_totals();
	$i         = 0;
	foreach ( $totalItem as $key => $total ) {
		$i++;
		$tr_class = '';
		$th_class = '';
		if ( array_key_exists( $key, $get_order_item_totals_class ) ) {
			if ( 'refund_0' == $key ) {
				if ( __( 'Order fully refunded.', 'woocommerce' ) == $total['label'] ) {
					$tr_class = esc_attr( $get_order_item_totals_class[ $key ] . '_row' );
				}
			} else {
				$tr_class = esc_attr( $get_order_item_totals_class[ $key ] . '_row' );
			}
		}
		if ( array_key_exists( $key, $get_order_item_totals_class ) ) {
			if ( 'refund_0' == $key ) {
				if ( __( 'Order fully refunded.', 'woocommerce' ) == $total['label'] ) {
					$th_class = esc_attr( $get_order_item_totals_class[ $key ] );
				}
			} else {
				$th_class = esc_attr( $get_order_item_totals_class[ $key ] );
			}
		}
		?>

	<tr class="<?php echo esc_attr( $tr_class ); ?>">
		<th class="td <?php echo esc_attr( $th_class ); ?>" scope="row" colspan="<?php echo esc_attr( $productItemCost ? 3 : 2 ); ?>" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>; <?php echo esc_attr( ( 1 === $i ) ? 'border-top-width: 4px;' : '' ); ?>">
			<?php
			if ( array_key_exists( $key, $get_order_item_totals ) ) {
				if ( 'refund_0' == $key ) {
					if ( __( 'Order fully refunded.', 'woocommerce' ) == $total['label'] ) {
						echo esc_html_e( $get_order_item_totals[ $key ], 'woocommerce' );
					} else {
						echo wp_kses_post( $total['label'] );
					}
				} else {
					echo esc_html_e( $get_order_item_totals[ $key ], 'woocommerce' );
				}
			} else {
				echo wp_kses_post( $total['label'] );
			}
			?>
		</th>
		<th class="td 
		<?php
		if ( array_key_exists( $key, $item_content_class ) ) {
			if ( 'refund_0' == $key ) {
				if ( __( 'Order fully refunded.', 'woocommerce' ) == $total['label'] ) {
					echo esc_html( $item_content_class[ $key ] );
				}
			} else {
				echo esc_html( $item_content_class[ $key ] );
			}
		}
		?>
		" style="font-weight: normal;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>; <?php echo esc_attr( ( 1 === $i ) ? 'border-top-width: 4px;' : '' ); ?>">
		<?php echo wp_kses_post( $total['value'] ); ?>
		</th>
	</tr>

		<?php
	}

	if ( ! empty( $order->get_customer_note() ) ) {
		$note = $order->get_customer_note();
		?>

		<tr class="yaymail_item_note_title_row">
			<th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>; <?php echo esc_attr( ( 1 === $i ) ? 'border-top-width: 4px;' : '' ); ?>">
				<?php esc_html_e( $get_order_item_totals['customer_note'], 'woocommerce' ); ?>
			</th>
			<th class="td" style="font-weight: normal;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>; <?php echo esc_attr( ( 1 === $i ) ? 'border-top-width: 4px;' : '' ); ?>">
				<?php echo esc_html( $note ); ?>
			</th>
		</tr>

	<?php } ?>
</tfoot>
