<?php
use YayMail\Helper\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! empty( $billing_address ) && ! empty( $shipping_address ) ) {
	$width = '50%';
} else {
		$width = '100%';
}

$is_local_pickup = false;

if ( ! empty( $order ) && 'Local pickup' === $order->get_shipping_method() ) {
	$is_local_pickup = true;
}

$is_preview     = Helper::isPreview( $this->preview_mail );
$title_shipping = $is_preview ? '{{titleShipping}}' : ( get_post_meta( $postID, '_email_title_shipping', true ) ? get_post_meta( $postID, '_email_title_shipping', true ) : __( 'Shipping Address', 'woocommerce' ) );
$title_billing  = $is_preview ? '{{titleBilling}}' : ( get_post_meta( $postID, '_email_title_billing', true ) ? get_post_meta( $postID, '_email_title_billing', true ) : __( 'Billing Address', 'woocommerce' ) );
$fontFamily     = isset( $atts['fontfamily'] ) && $atts['fontfamily'] ? 'font-family:' . html_entity_decode( $atts['fontfamily'], ENT_QUOTES, 'UTF-8' ) : 'font-family:inherit';
?>
	<tr>
	<?php if ( ! empty( $billing_address ) ) { ?>
		<td style="width: <?php echo esc_attr( $width ); ?>;">
			<h2 class="title-billing" style="color: inherit; display: block; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 10px 18px 0;<?php echo esc_attr( $fontFamily ); ?>">
				<?php esc_html_e( $title_billing, 'woocommerce' ); ?>
			</h2>
	</td>
		<?php } ?>
		<?php if ( ! empty( $shipping_address ) && ! $is_local_pickup ) { ?>
		<td style="width: <?php echo esc_attr( $width ); ?>;">
			<h2 class="title-shipping" style="color: inherit; display: block; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px;<?php echo esc_attr( $fontFamily ); ?>">
				<?php esc_html_e( $title_shipping, 'woocommerce' ); ?>
			</h2>
		</td>
		<?php } ?>
	</tr>
