<?php

defined( 'ABSPATH' ) || exit;
use YayMail\Page\Source\CustomPostType;
use YayMail\Helper\Helper;

$is_preview                = Helper::isPreview( $this->preview_mail );
$text_align                = is_rtl() ? 'right' : 'left';
$sent_to_admin             = ( isset( $sent_to_admin ) ? $sent_to_admin : false );
$plain_text                = ( isset( $plain_text ) ? $plain_text : '' );
$email                     = ( isset( $email ) ? $email : '' );
$postID                    = CustomPostType::postIDByTemplate( $this->template );
$yaymail_settings          = get_option( 'yaymail_settings' );
$order_image               = isset( $yaymail_settings['product_image'] ) && '0' != $yaymail_settings['product_image'] ? $yaymail_settings['product_image'] : '0';
$text_link_color           = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#7f54b3';
$borderColor               = isset( $atts['bordercolor'] ) && $atts['bordercolor'] ? 'border-color:' . html_entity_decode( $atts['bordercolor'], ENT_QUOTES, 'UTF-8' ) : 'border-color:inherit';
$textColor                 = isset( $atts['textcolor'] ) && $atts['textcolor'] ? 'color:' . html_entity_decode( $atts['textcolor'], ENT_QUOTES, 'UTF-8' ) : 'color:inherit';
$order_item_download_title = get_post_meta( $postID, '_yaymail_email_order_item_download_title', true );
$product_title             = $is_preview ? '{{items_download_product_title}}' : ( false != $order_item_download_title ? $order_item_download_title['items_download_product_title'] : 'Product' );
$expires_title             = $is_preview ? '{{items_download_expires_title}}' : ( false != $order_item_download_title ? $order_item_download_title['items_download_expires_title'] : 'Expires' );
$download_title            = $is_preview ? '{{items_download_download_title}}' : ( false != $order_item_download_title ? $order_item_download_title['items_download_download_title'] : 'Download' );
$columns                   = apply_filters(
	'woocommerce_email_downloads_columns',
	array(
		'download-product' => __( 'Product', 'woocommerce' ),
		'download-expires' => __( 'Expires', 'woocommerce' ),
		'download-file'    => __( 'Download', 'woocommerce' ),
	)
);
?>

<!-- Table Items has Border -->
<table class="yaymail_builder_table_items_border yaymail_builder_table_item_download" cellspacing="0" cellpadding="6" border="1" style="width: 100% !important;<?php echo esc_attr( $borderColor ); ?>;color: inherit;flex-direction:inherit;" width="100%">
	<thead>
		<tr style="word-break: normal;<?php echo esc_attr( $textColor ); ?>">
			<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;<?php echo esc_attr( $borderColor ); ?>;">
				<?php esc_html_e( $product_title, 'woocommerce' ); ?>
			</th>
			<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;<?php echo esc_attr( $borderColor ); ?>;">
				<?php esc_html_e( $expires_title, 'woocommerce' ); ?>
			</th>
			<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;<?php echo esc_attr( $borderColor ); ?>;">
				<?php esc_html_e( $download_title, 'woocommerce' ); ?>
			</th>
		</tr>
	</thead>
		<tfoot>
			<tr style="<?php echo esc_attr( $textColor ); ?>">
				<td class="td" style="<?php echo esc_attr( $borderColor ); ?>;text-align:<?php echo esc_attr( $text_align ); ?>">
				
					<?php
					if ( '1' == $order_image ) :
						$product_image_src = wc_placeholder_img_src();
						$image_width       = isset( $yaymail_settings['image_width'] ) ? str_replace( 'px', '', $yaymail_settings['image_width'] ) : 32;
						$image_height      = isset( $yaymail_settings['image_height'] ) ? str_replace( 'px', '', $yaymail_settings['image_height'] ) : 32;
						$image_size        = isset( $yaymail_settings['image_size'] ) ? $yaymail_settings['image_size'] : 'thumbnail';
						?>
						<div class="yaymail-product-download-image" style="margin-bottom: 5px; float: left">
							<a style="color:<?php echo esc_attr( $text_link_color ); ?>" href="<?php echo esc_url( get_permalink( $download['product_id'] ) ); ?>">
								<img src="<?php echo esc_url( $product_image_src ); ?>" alt="Product image" height="<?php echo esc_attr( $image_height ); ?>" width="<?php echo esc_attr( $image_width ); ?>" style="vertical-align:middle; margin-right: 10px;" />
								<span><?php esc_html_e( 'Downloadable Product', 'yaymail' ); ?></span>
							</a>
						</div>
					<?php else : ?>
						<a href="" style="color:<?php echo esc_attr( $text_link_color ); ?>" > <?php esc_html_e( 'Downloadable Product', 'yaymail' ); ?></a>
					<?php endif; ?>
				</td>
				<td class="td" style="<?php echo esc_attr( $borderColor ); ?>;text-align:<?php echo esc_attr( $text_align ); ?>">
					<time datetime="2021-02-13" title="1613174400"> <?php echo wp_kses_post( wc_format_datetime( new WC_DateTime() ) ); ?></time>
				</td>
				<td class="td" style="<?php echo esc_attr( $borderColor ); ?>;text-align:<?php echo esc_attr( $text_align ); ?>">
					<a href="" style="color:<?php echo esc_attr( $text_link_color ); ?>" ><?php esc_html_e( 'Download.doc', 'yaymail' ); ?></a>
				</td>
			</tr>
		</tfoot>
</table>
