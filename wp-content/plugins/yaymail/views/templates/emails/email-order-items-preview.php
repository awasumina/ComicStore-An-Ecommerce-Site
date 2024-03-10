<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$text_align          = is_rtl() ? 'right' : 'left';
$yaymail_settings    = get_option( 'yaymail_settings' );
$yaymail_template    = get_post_meta( $postID, '_yaymail_template', true );
$orderImagePostions  = isset( $yaymail_settings['image_position'] ) && ! empty( $yaymail_settings['image_position'] ) ? $yaymail_settings['image_position'] : 'Top';
$orderImage          = isset( $yaymail_settings['product_image'] ) && '0' != $yaymail_settings['product_image'] ? $yaymail_settings['product_image'] : '0';
$productHyperLinks   = isset( $yaymail_settings['product_hyper_links'] ) ? $yaymail_settings['product_hyper_links'] : 0;
$productRegularPrice = isset( $yaymail_settings['product_regular_price'] ) ? $yaymail_settings['product_regular_price'] : 0;
$productItemCost     = isset( $yaymail_settings['product_item_cost'] ) ? $yaymail_settings['product_item_cost'] : 0;

if ( ! function_exists( 'yaymail_get_global_taxonomy_attribute_data' ) ) :
	function yaymail_get_global_taxonomy_attribute_data( $name, $product, $single_product = null ) {
		$out = array();

		$product_id = is_numeric( $product ) ? $product : $product->get_id();
		$terms      = wp_get_post_terms( $product_id, $name, 'all' );

		if ( ! empty( $terms ) ) {
			if ( ! is_wp_error( $terms ) ) {
				$tax        = $terms[0]->taxonomy;
				$tax_object = get_taxonomy( $tax );
				if ( isset( $tax_object->labels->singular_name ) ) {
					$out['label'] = $tax_object->labels->singular_name;
				} elseif ( isset( $tax_object->label ) ) {
					$out['label'] = $tax_object->label;
					$label_prefix = __( 'Product', 'woocommerce-show-attributes' ) . ' ';
					if ( 0 === strpos( $out['label'], $label_prefix ) ) {
						$out['label'] = substr( $out['label'], strlen( $label_prefix ) );
					}
				}
				$tax_terms = array();
				foreach ( $terms as $term ) {
					$single_term = esc_html( $term->name );

					// Show terms as links?
					if ( $single_product ) {
						if ( get_option( 'wcsa_terms_as_links' ) == 'yes' ) {
							$term_link = get_term_link( $term );
							if ( ! is_wp_error( $term_link ) ) {
								$single_term = '<a href="' . esc_url( $term_link ) . '">' . esc_html( $term->name ) . '</a>';
							}
						}
					}
					array_push( $tax_terms, $single_term );
				}
				$out['value'] = implode( ', ', $tax_terms );
			}
		}

		return $out;
	}
endif;

foreach ( $items as $item_id => $item ) :
	if ( apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
						$product           = $item->get_product();
						$result_attributes = array();
						$product_id        = ( $item['variation_id'] ? $item['variation_id'] : $item['product_id'] );
		?>
		<?php
		// For Woo Show Attributes Plugins
		if ( class_exists( 'WooCommerce_Show_Attributes' ) ) {

			if ( is_a( $product, 'WC_Product_Variation' ) ) {
				$attributes = get_post_meta( $product->get_parent_id(), '_product_attributes', true );
				if ( ! $attributes ) {
					echo '';
				}
				foreach ( $attributes as $attribute ) {
					if ( $attribute['is_visible'] && empty( $attribute['is_variation'] ) ) {
						if ( $attribute['is_taxonomy'] ) {
							$result_attributes[] = yaymail_get_global_taxonomy_attribute_data( $attribute['name'], $product->get_parent_id() );
						} else {
							$result_attributes[] = array(
								'label' => $attribute['name'],
								'value' => $attribute['value'],
							);
						}
					}
				}
			} else {

				$attributes = $product->get_attributes();
				if ( ! $attributes ) {
					echo '';
				}
				foreach ( $attributes as $attribute ) {
					if ( ! is_a( $attribute, 'WC_Product_Attribute' ) ) {
						continue;
					}
					if ( $attribute->get_variation() ) {
						continue;
					}
					if ( ! $attribute->get_visible() ) {
						continue;
					}
					$name = $attribute->get_name();
					if ( $attribute->is_taxonomy() ) {
						$result_attributes[] = yaymail_get_global_taxonomy_attribute_data( $name, $product );
					} else {
						$result_attributes[] = array(
							'label' => $name,
							'value' => esc_html( implode( ', ', $attribute->get_options() ) ),
						);

					}
				}
			}
		}
		?>
		<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
		<th colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_product_title_colspan', 1, $yaymail_template ) ); ?>" class="td yaymail_item_product_content" style="text-align:<?php echo esc_attr( $text_align ); ?>;font-weight: normal;word-wrap:break-word;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( isset( $default_args['border_color'] ) ? $default_args['border_color'] : '' ); ?>;">
		<?php

		if ( 'Bottom' == $orderImagePostions && '1' == $orderImage ) {
			echo ( '<div class="yaymail-product-texts preview1" style="padding: 5px 0;">' );
			// Product name
			if ( $productHyperLinks ) {
				if ( method_exists( $product, 'get_permalink' ) ) {
					echo wp_kses_post( ' <a style="color:' . $text_link_color . '" target="_blank" href="' . $product->get_permalink() . '"><span class="yaymail-product-name">' . wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ) . '</span></a>' );
				} else {
					echo wp_kses_post( '<span class="yaymail-product-name">' . wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ) . '</span>' );
				}
			} else {
				echo wp_kses_post( '<span class="yaymail-product-name">' . wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ) . '</span>' );
			}

			// SKU
			if ( $args['show_sku'] && is_object( $product ) && $product->get_sku() && $product ) {
				echo wp_kses_post( '<span class="yaymail-product-sku"> (#' . $product->get_sku() . ')</span>' );
			}

			if ( $args['show_des'] && is_object( $product ) && $product->get_short_description() && $product ) {
				echo wp_kses_post( '<div class="yaymail-product-short-descript"> (#' . $product->get_short_description() . ')</div>' );
			}
			if ( $args['show_des'] && is_object( $product ) && 'variation' === $product->get_type() ) {
				echo wp_kses_post( '<div class="yaymail-product-description"> (#' . $product->get_description() . ')</div>' );
			}

			// Woo Show Attributes
			if ( class_exists( 'WooCommerce_Show_Attributes' ) ) :
				foreach ( $result_attributes as $key1 => $result_attribute ) {
					$attribute_output = $result_attribute['label'] . ': ' . $result_attribute['value'];
					echo '<br>' . wp_kses_post( $attribute_output );
				}
			endif;
				// allow other plugins to add additional product information here
				do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $args['plain_text'] );
			if ( class_exists( 'YITH_Barcode' ) ) {
				if ( YITH_YWBC()->show_product_barcode_on_email ) {
					echo '<div class="ywbc-email-product-barcode-container">' . do_shortcode( '[yith_render_barcode id="' . $product_id . '"]' ) . '</div>';
				}
			}

				// Display item meta data.
				wc_display_item_meta( $item );

				echo ( '</div>' );
			// Show title/image etc
			if ( $args['show_image'] && is_object( $product ) ) {
				echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', '<div class="yaymail-product-image" style="margin-bottom: 5px"><img src="' . ( $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), $args['image_size'][2] ) ) : wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Product image', 'woocommerce' ) . '" height="' . esc_attr( str_replace( 'px', '', $args['image_size'][1] ) ) . '" width="' . esc_attr( str_replace( 'px', '', $args['image_size'][0] ) ) . '" style="vertical-align:middle; margin-' . ( is_rtl() ? 'left' : 'right' ) . ': 10px;" /></div>', $item ) );
			}
		} else {
			// Show title/image etc
			if ( $args['show_image'] && is_object( $product ) ) {
				if ( 'Top' == $orderImagePostions && $args['show_image'] && is_object( $product ) ) {
					echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', '<div class="yaymail-product-image" style="margin-bottom: 5px; float: unset"><img src="' . ( $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), $args['image_size'][2] ) ) : wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Product image', 'woocommerce' ) . '" height="' . esc_attr( str_replace( 'px', '', $args['image_size'][1] ) ) . '" width="' . esc_attr( str_replace( 'px', '', $args['image_size'][0] ) ) . '" style="vertical-align:middle; margin-' . ( is_rtl() ? 'left' : 'right' ) . ': 10px;" /></div>', $item ) );
				} else {
					echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', '<div class="yaymail-product-image" style="margin-bottom: 5px; float: left"><img src="' . ( $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), $args['image_size'][2] ) ) : wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Product image', 'woocommerce' ) . '" height="' . esc_attr( str_replace( 'px', '', $args['image_size'][1] ) ) . '" width="' . esc_attr( str_replace( 'px', '', $args['image_size'][0] ) ) . '" style="vertical-align:middle; margin-' . ( is_rtl() ? 'left' : 'right' ) . ': 10px;" /></div>', $item ) );
				}
			}
			echo ( '<div class="yaymail-product-texts" style="padding: 5px 0;">' );
			// Product name
			if ( $productHyperLinks ) {
				if ( method_exists( $product, 'get_permalink' ) ) {
					echo wp_kses_post( ' <a style="color:' . $text_link_color . '" target="_blank" href="' . $product->get_permalink() . '"><span class="yaymail-product-name">' . wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ) . '</span></a>' );
				} else {
					echo wp_kses_post( '<span class="yaymail-product-name">' . wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ) . '</span>' );
				}
			} else {
				echo wp_kses_post( '<span class="yaymail-product-name">' . wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ) . '</span>' );
			}


			// SKU
			if ( $args['show_sku'] && is_object( $product ) && $product->get_sku() && $product ) {
				echo wp_kses_post( '<span class="yaymail-product-sku"> (#' . $product->get_sku() . ')</span>' );
			}

			if ( $args['show_des'] && is_object( $product ) && $product->get_short_description() && $product ) {
				echo wp_kses_post( '<div class="yaymail-product-short-descript"> (#' . $product->get_short_description() . ')</div>' );
			}
			if ( $args['show_des'] && is_object( $product ) && 'variation' === $product->get_type() ) {
				echo wp_kses_post( '<div class="yaymail-product-description"> (#' . $product->get_description() . ')</div>' );
			}
			if ( class_exists( 'WooCommerce_Show_Attributes' ) ) :
				foreach ( $result_attributes as $key1 => $result_attribute ) {
					$attribute_output = $result_attribute['label'] . ': ' . $result_attribute['value'];
					echo '<br>' . wp_kses_post( $attribute_output );
				}
			endif;
				// allow other plugins to add additional product information here
				do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $args['plain_text'] );
			if ( class_exists( 'YITH_Barcode' ) ) {
				if ( YITH_YWBC()->show_product_barcode_on_email ) {
					echo '<div class="ywbc-email-product-barcode-container">' . do_shortcode( '[yith_render_barcode id="' . $product_id . '"]' ) . '</div>';
				}
			}

				// Display item meta data.
				wc_display_item_meta( $item );

				echo ( '</div>' );
		}


		// Display item download links.
		if ( $args['show_download_links'] ) {
			wc_display_item_downloads( $item );
		}

		// allow other plugins to add additional product information here
		do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $args['plain_text'] );
		?>

			</th>
			<?php if ( $productItemCost && is_object( $product ) ) { ?>
				<th colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_cost_colspan', 1, $yaymail_template ) ); ?>" class="td yaymail_item_cost_content" style="text-align:<?php echo esc_attr( $text_align ); ?>;font-weight: normal; vertical-align:middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( isset( $default_args['border_color'] ) ? $default_args['border_color'] : '' ); ?>;">
					<?php echo wp_kses_post( wc_price( $order->get_item_subtotal( $item, false, true ), array( 'currency' => $order->get_currency() ) ) ); ?>
				</th>
			<?php } ?>
			<th colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_quantity_colspan', 1, $yaymail_template ) ); ?>" class="td yaymail_item_quantity_content" style="text-align:<?php echo esc_attr( $text_align ); ?>;font-weight: normal; vertical-align:middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( isset( $default_args['border_color'] ) ? $default_args['border_color'] : '' ); ?>;">
				<?php
				$qty          = $item->get_quantity();
				$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

				if ( $refunded_qty && 'customer_refunded_order' == $yaymail_template ) {
					$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
				} else {
					$qty_display = esc_html( $qty );
				}
				echo wp_kses_post( apply_filters( 'woocommerce_email_order_item_quantity', $qty_display, $item ) );
				?>
			</th>
			<th colspan="<?php echo wp_kses_post( apply_filters( 'yaymail_order_item_price_colspan', 1, $yaymail_template ) ); ?>" class="td yaymail_item_price_content" style="text-align:<?php echo esc_attr( $text_align ); ?>;font-weight: normal;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( isset( $default_args['border_color'] ) ? $default_args['border_color'] : '' ); ?>; word-break: break-all;">
				<?php
				if ( $productRegularPrice && is_object( $product ) ) {
					$product_regular_price = isset( $product->get_data()['regular_price'] ) ? (float) $product->get_data()['regular_price'] : null;
					if ( $order->get_currency() !== get_option( 'woocommerce_currency' ) ) {
						foreach ( $item->get_meta_data() as $product_meta ) {
							if ( '_wcpdf_regular_price' !== $product_meta->key ) {
								continue;
							}
							$product_regular_price = $product_meta->value[ get_option( 'woocommerce_tax_display_cart' ) ? get_option( 'woocommerce_tax_display_cart' ) : 'excl' ];
							break;
						}
					}
					if ( (float) $product_regular_price !== $order->get_line_subtotal( $item, true ) ) {
						echo wp_kses_post( '<del>' . wc_price( $product_regular_price, array( 'currency' => $order->get_currency() ) ) . '</del>  ' );
					}
				} echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) );
				?>
			</th>
		</tr>
		<?php
	}

	// Show purchase note
	$purchase_note = '';
	if ( $product && $product->get_purchase_note() ) {
		$purchase_note = $product->get_purchase_note();
	}

	if ( ( 'customer_on_hold_order' === $this->template
		|| 'customer_processing_order' === $this->template
		|| 'customer_completed_order' === $this->template
		|| 'customer_refunded_order' === $this->template
		|| 'customer_invoice' === $this->template
		|| 'customer_note' === $this->template
		|| 'new_order' === $this->template )
		&& isset( $args['show_purchase_note'] )
		&& is_object( $product )
		&& ! empty( $purchase_note )
	) {
		?>

		<tr class="yaymail-purchase-note">
		<th colspan="3" style="text-align:<?php echo esc_attr( $text_align ); ?>;font-weight: normal;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( isset( $default_args['border_color'] ) ? $default_args['border_color'] : '' ); ?>;">
		<?php echo wp_kses_post( wpautop( do_shortcode( $purchase_note ) ) ); ?>
			</th>
		</tr>
		 
	<?php } ?>

<?php endforeach; ?>
