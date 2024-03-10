<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! empty( $billing_address ) && ! empty( $shipping_address ) ) {
	$width = '50%';
} else {
	$width = '100%';
}

$borderColor = isset( $atts['bordercolor'] ) && $atts['bordercolor'] ? 'border-color:' . html_entity_decode( $atts['bordercolor'], ENT_QUOTES, 'UTF-8' ) : 'border-color:inherit';
$textColor   = isset( $atts['textcolor'] ) && $atts['textcolor'] ? 'color:' . html_entity_decode( $atts['textcolor'], ENT_QUOTES, 'UTF-8' ) : 'color:inherit';
$fontFamily  = isset( $atts['fontfamily'] ) && $atts['fontfamily'] ? 'font-family:' . html_entity_decode( $atts['fontfamily'], ENT_QUOTES, 'UTF-8' ) : 'font-family:inherit';


?>
<?php if ( null !== $order ) { ?>
<table style="width: 100%; 
	<?php
	echo esc_attr( $textColor );
	echo esc_attr( ';' . $borderColor );
	?>
">
	<tr>
		<?php
		if ( ! empty( $billing_address ) ) {
			;
			?>
			<td class="yaymail_billing_address_content" style="width: 
			<?php
			echo esc_attr( $width );
			echo esc_attr( ';' . $borderColor );
			?>
			 " valign="top">
				<table style="width: 100%; height: 18px; border-collapse: collapse; line-height: 22px;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>" border="0">
					<tbody>
					<tr style="height: 18px;">
						<td style="height: 18px ;
						<?php
						echo esc_attr( $textColor );
						echo esc_attr( ';' . $borderColor );
						?>
						" valign="top" data-textcolor>
						<address style="padding: 12px;border-style:solid; border-width: 1px;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $fontFamily ); ?>" data-bordercolor>
							<span style="font-size: 14px;">
							<?php echo wp_kses_post( $billing_address ); ?>
								<?php
								if ( $order->get_billing_phone() ) {
									;
									?>
									<br/>
									<a  href='tel:<?php echo esc_html( $order->get_billing_phone() ); ?>' style='font-weight: normal; text-decoration: underline;' :style='{color:emailTextLinkColor}'><?php echo esc_html( $order->get_billing_phone() ); ?></a>
								<?php }; ?> 
								<?php
								if ( $order->get_billing_phone() ) {
									;
									?>
									<br/>
									<a  href='tel:<?php echo esc_html( $order->get_billing_email() ); ?>' style='font-weight: normal; text-decoration: underline;' :style='{color:emailTextLinkColor}'><?php echo esc_html( $order->get_billing_email() ); ?></a>
								<?php }; ?> 
							</span>
						</address>
						</td>
					</tr>
					</tbody>
				</table>
			</td>
		<?php }; ?>
		<?php
		if ( ! empty( $shipping_address ) && 'Local pickup' !== $order->get_shipping_method() ) {
			;
			?>
			<td class="yaymail_shipping_address_content" style="width: 
			<?php
			echo esc_attr( $width );
			echo esc_attr( ';' . $borderColor );
			?>
			 ;border-color: inherit;" valign="top">
				<table style="width: 100%; height: 18px;border-collapse: collapse;<?php echo esc_attr( $borderColor ); ?>" border="0">
					<tbody>
					<tr style="height: 18px;">
						<td style="height: 18px;
						<?php
						echo esc_attr( $textColor );
						echo esc_attr( ';' . $borderColor );
						?>
						" valign="top">
						<address style="padding: 12px;border-style:solid; border-width: 1px;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $fontFamily ); ?>">
							<span style="font-size: 14px; color: inherit;">
							<?php echo wp_kses_post( $shipping_address ); ?>
								<?php
								if ( method_exists( $order, 'get_shipping_phone' ) && ! empty( $order->get_shipping_phone() ) ) {
									;
									if ( false === strpos( $shipping_address, $order->get_shipping_phone() ) ) {
										?>
									<br/>
									<a  href='tel:<?php echo esc_html( $order->get_shipping_phone() ); ?>' style='font-weight: normal; text-decoration: underline;' :style='{color:emailTextLinkColor}'><?php echo esc_html( $order->get_shipping_phone() ); ?></a><br/>
										<?php
									}
								};
								?>
								
								<?php
								if ( metadata_exists( 'post', $order->get_id(), '_shipping_email' ) ) {
									;
									if ( false === strpos( $shipping_address, get_post_meta( $order->get_id(), '_shipping_email', true ) ) ) {
										?>
											<a  href='tel:<?php echo esc_html( get_post_meta( $order->get_id(), '_shipping_email', true ) ); ?>' style='font-weight: normal; text-decoration: underline;' :style='{color:emailTextLinkColor}'><?php echo esc_html( get_post_meta( $order->get_id(), '_shipping_email', true ) ); ?></a>
										<?php
									}
								};
								?>
							</span>
						</address>
						</td>
					</tr>
					</tbody>
				</table>
			</td>
		<?php }; ?>
	</tr>
</table>
	<?php
} else {
	;
	?>
	<table style="width: 100%; 
	<?php
	echo esc_attr( $textColor );
	echo esc_attr( ';' . $borderColor );
	?>
	">
		<tr>
			<?php
			if ( ! empty( $billing_address ) ) {
				;
				?>
				<td class="yaymail_billing_address_content" style="width: 
				<?php
				echo esc_attr( $width );
				echo esc_attr( ';' . $borderColor );
				?>
				" valign="top">
					<table style="width: 100%; height: 18px; border-collapse: collapse; line-height: 22px;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>" border="0">
						<tbody>
						<tr style="height: 18px;">
							<td style="height: 18px ;
							<?php
							echo esc_attr( $textColor );
							echo esc_attr( ';' . $borderColor );
							?>
							" valign="top" data-textcolor>
							<address style="padding: 12px;border-style:solid; border-width: 1px;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $fontFamily ); ?>" data-bordercolor>
								<span style="font-size: 14px;">
								<?php echo wp_kses_post( $billing_address ); ?>
								<a  href='tel:+18587433828' style='font-weight: normal; text-decoration: underline;' :style='{color:emailTextLinkColor}'>(910) 529-1147</a>	
								</span>
							</address>
							</td>
						</tr>
						</tbody>
					</table>
				</td>
			<?php }; ?>
			<?php
			if ( ! empty( $shipping_address ) ) {
				;
				?>
				<td class="yaymail_shipping_address_content" style="width: 
				<?php
				echo esc_attr( $width );
				echo esc_attr( ';' . $borderColor );
				?>
				;border-color: inherit;" valign="top">
					<table style="width: 100%; height: 18px;border-collapse: collapse;<?php echo esc_attr( $borderColor ); ?>" border="0">
						<tbody>
						<tr style="height: 18px;">
							<td style="height: 18px;
							<?php
							echo esc_attr( $textColor );
							echo esc_attr( ';' . $borderColor );
							?>
							" valign="top">
							<address style="padding: 12px;border-style:solid; border-width: 1px;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $fontFamily ); ?>">
								<span style="font-size: 14px; color: inherit;">
									<?php echo wp_kses_post( $shipping_address ); ?>
									<a href='tel:+18587433828' style='font-weight: normal; text-decoration: underline;' :style='{color:emailTextLinkColor}'>(910) 529-1147</a>
								</span>
							</address>
							</td>
						</tr>
						</tbody>
					</table>
				</td>
			<?php }; ?>
		</tr>
	</table>
<?php }; ?>
