<?php

defined( 'ABSPATH' ) || exit;
use YayMail\Page\Source\CustomPostType;
use YayMail\Helper\Helper;

$is_preview                = Helper::isPreview( $this->preview_mail );
$postID                    = CustomPostType::postIDByTemplate( $this->template );
$order_item_download_title = get_post_meta( $postID, '_yaymail_email_order_item_download_title', true );
$itemsDownloadTitle        = $is_preview ? '{{items_download_header_title}}' : ( false != $order_item_download_title ? $order_item_download_title['items_download_header_title'] : 'Downloads' );
$titleColor                = isset( $atts['titlecolor'] ) && $atts['titlecolor'] ? 'color:' . html_entity_decode( $atts['titlecolor'], ENT_QUOTES, 'UTF-8' ) : 'color:inherit';
?>

<!-- Table Items has Border -->
<?php
if ( isset( $downloads ) && ! empty( $downloads ) || null === $order ) {
	?>
<h2 style="margin: 13px 0px;<?php echo esc_attr( $titleColor ); ?>" class="woocommerce-order-downloads__title"><?php esc_html_e( $itemsDownloadTitle, 'woocommerce' ); ?></h2>
	<?php
}
?>
