<?php

defined( 'ABSPATH' ) || exit;
use YayMail\Page\Source\CustomPostType;
use YayMail\Helper\Helper;
$is_preview       = Helper::isPreview( $this->preview_mail );
$postID           = CustomPostType::postIDByTemplate( $this->template );
$order_item_title = get_post_meta( $postID, '_yaymail_email_order_item_title', true );
$order_title      = false != $order_item_title && isset( $order_item_title['order_title'] ) ? $order_item_title['order_title'] : __( '[Order #', 'yaymail' ) . wp_kses_post( do_shortcode( '[yaymail_order_id]' ) . ']' ) . wp_kses_post( do_shortcode( '([yaymail_order_date])' ) );
$text_link_color  = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#7f54b3';
$titleColor       = isset( $atts['titlecolor'] ) && $atts['titlecolor'] ? 'color:' . html_entity_decode( $atts['titlecolor'], ENT_QUOTES, 'UTF-8' ) : 'color:inherit';
?>

<?php
if ( $is_preview ) {
	$before            = '<h2 style="font-weight: normal;' . esc_attr( $titleColor ) . '" class="yaymail_builder_link" href="">';
	$after             = '</h2>';
	$allowed_html_tags = Helper::customAllowedHTMLTags( array( 'v-html' => true ) );
	echo wp_kses( $before . '<span v-html="order_title"></span>' . $after, $allowed_html_tags );
} else {
	$before                   = '<h2 style="font-weight: normal;font-size: 18px;' . esc_attr( $titleColor ) . '" class="yaymail_builder_link" >';
	$after                    = '</h2>';
	$do_shortcode_order_title = wp_kses_post( do_shortcode( $order_title ) );
	echo wp_kses_post( $before . $do_shortcode_order_title . $after );
}
