<?php
defined( 'ABSPATH' ) || exit;
use YayMail\Page\Source\CustomPostType;
use YayMail\Helper\Helper;
$is_preview           = Helper::isPreview( $this->preview_mail );
$text_align           = is_rtl() ? 'right' : 'left';
$postID               = CustomPostType::postIDByTemplate( $this->template );
$yaymail_template     = get_post_meta( $postID, '_yaymail_template', true );
$order_item_title     = get_post_meta( $postID, '_yaymail_email_order_item_title', true );
$product_title        = $is_preview ? '{{product_title}}' : ( false != $order_item_title && isset( $order_item_title['product_title'] ) ? $order_item_title['product_title'] : 'Product' );
$cost_title           = $is_preview ? '{{cost_title}}' : ( false != $order_item_title && isset( $order_item_title['cost_title'] ) ? $order_item_title['cost_title'] : 'Cost' );
$quantity_title       = $is_preview ? '{{quantity_title}}' : ( false != $order_item_title && isset( $order_item_title['quantity_title'] ) ? $order_item_title['quantity_title'] : 'Quantity' );
$price_title          = $is_preview ? '{{price_title}}' : ( false != $order_item_title && isset( $order_item_title['price_title'] ) ? $order_item_title['price_title'] : 'Price' );
$subtoltal_title      = $is_preview ? '{{subtoltal_title}}' : ( false != $order_item_title && isset( $order_item_title['subtoltal_title'] ) ? $order_item_title['subtoltal_title'] : 'Subtotal:' );
$payment_method_title = $is_preview ? '{{payment_method_title}}' : ( false != $order_item_title && isset( $order_item_title['payment_method_title'] ) ? $order_item_title['payment_method_title'] : 'Payment method:' );
$total_title          = $is_preview ? '{{total_title}}' : ( false != $order_item_title && isset( $order_item_title['total_title'] ) ? $order_item_title['total_title'] : 'Total:' );
$borderColor          = isset( $atts['bordercolor'] ) && $atts['bordercolor'] ? 'border-color:' . html_entity_decode( $atts['bordercolor'], ENT_QUOTES, 'UTF-8' ) : 'border-color:inherit';
$textColor            = isset( $atts['textcolor'] ) && $atts['textcolor'] ? 'color:' . html_entity_decode( $atts['textcolor'], ENT_QUOTES, 'UTF-8' ) : 'color:inherit';
$yaymail_settings     = get_option( 'yaymail_settings' );
$productItemCost      = isset( $yaymail_settings['product_item_cost'] ) ? $yaymail_settings['product_item_cost'] : 0;
?>
<thead class="yaymail_element_head_order_item">
	<tr style="word-break: normal">
		<th colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_product_title_colspan', 1, $yaymail_template ) ); ?>" class="td yaymail_item_product_title" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
			<?php echo esc_html( $product_title ); ?>
		</th>
		<?php if ( $productItemCost ) { ?>
			<th  colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_cost_colspan', 1, $yaymail_template ) ); ?>" class="td yaymail_item_price_per_item" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
				<?php echo esc_html( $cost_title ); ?>
			</th>
		<?php } ?>
		<th colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_quantity_colspan', 1, $yaymail_template ) ); ?>" class="td yaymail_item_quantity_title" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
			<?php echo esc_html( $quantity_title ); ?>
		</th>
		<th colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_price_colspan', 1, $yaymail_template ) ); ?>" class="td yaymail_item_price_title" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
			<?php echo esc_html( $price_title ); ?>
		</th>
	</tr>
</thead>
<tbody class="yaymail_element_body_order_item">
	<tr class="order_item">
		<th colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_product_title_colspan', 1, $yaymail_template ) ); ?>" class="td yaymail_item_product_content" scope="row" style="font-weight: normal;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
			<?php esc_html_e( 'Happy YayCommerce', 'yaymail' ); ?>
		</th>
		<?php if ( $productItemCost ) { ?>
			<th colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_cost_colspan', 1, $yaymail_template ) ); ?>" class="td yaymail_item_cost_content" scope="row" style="font-weight: normal;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
				<?php echo wp_kses_post( wc_price( 9 ) ); ?>
			</th>
		<?php } ?>
		<th colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_quantity_colspan', 1, $yaymail_template ) ); ?>" class="td yaymail_item_quantity_content" scope="row" style="font-weight: normal;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
			<?php esc_html_e( 2, 'yaymail' ); ?>
		<th colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_price_colspan', 1, $yaymail_template ) ); ?>" class="td yaymail_item_price_content" scope="row" style="font-weight: normal;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
			<?php echo wp_kses_post( wc_price( 18 ) ); ?>
		</th>
	</tr>
</tbody>
<tfoot class="yaymail_element_foot_order_item">	
	<tr class="yaymail_item_subtoltal_title_row">
		<th class="td yaymail_item_subtoltal_title" scope="row" colspan="<?php echo esc_attr( $productItemCost ? 3 : 2 ); ?>" style="text-align:<?php echo esc_attr( $text_align ); ?>;font-weight: bold;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?> ;border-top-width: 4px;">
			<?php echo esc_html( $subtoltal_title ); ?>
		</th>
		<th class="td yaymail_item_subtoltal_content" scope="row" colspan="1" style="font-weight: normal;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>; border-top-width: 4px;">
			<?php echo wp_kses_post( wc_price( 18 ) ); ?>
		</th>
	</tr>
	<tr class="yaymail_item_payment_method_title_row">
		<th class="td yaymail_item_payment_method_title" scope="row" colspan="<?php echo esc_attr( $productItemCost ? 3 : 2 ); ?>" style="text-align:<?php echo esc_attr( $text_align ); ?>; font-weight: bold;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
			<?php echo esc_html( $payment_method_title ); ?>
		</th>
		<th class="td yaymail_item_payment_method_content" scope="row" colspan="1" style="font-weight: normal;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
			<?php echo esc_html( 'Direct bank transfer' ); ?>
		</th>
	</tr>
	<tr class="yaymail_item_total_title_row">
		<th class="td yaymail_item_total_title" scope="row" colspan="<?php echo esc_attr( $productItemCost ? 3 : 2 ); ?>" style="text-align:<?php echo esc_attr( $text_align ); ?>; font-weight: bold;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
			<?php echo esc_html( $total_title ); ?>
		</th>
		<th class="td yaymail_item_total_content" scope="row" colspan="1" style="font-weight: normal;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
			<?php echo wp_kses_post( wc_price( 18 ) ); ?>
		</th>
	</tr>
</tfoot>
