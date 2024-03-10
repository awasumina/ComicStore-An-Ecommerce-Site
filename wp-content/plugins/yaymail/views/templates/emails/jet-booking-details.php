<?php
defined( 'ABSPATH' ) || exit;
$text_align  = is_rtl() ? 'right' : 'left';
$borderColor = 'border-color:inherit';
$textColor   = 'color:inherit';
?>

<h2 class="yaymail-jet-booking-details-title" style="color:#7f55b3;display:block;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:0 0 18px;text-align:left">
	<?php esc_html_e( 'Booking Details', 'jet-booking' ); ?>
</h2>
<table class="yaymail_builder_table_items_content yaymail-jet-booking-details" cellspacing="0" cellpadding="6" border="1" style="border-collapse: separate; color: #636363; border: 1px solid #e5e5e5; font-family: Helvetica,Roboto,Arial,sans-serif;" width="100%">
	<thead class="yaymail_element_head_order_item">
		<tr style="word-break: normal">
		<?php foreach ( $get_booking_order_details as $value ) { ?>
			<?php if ( ! empty( $value['display'] ) && '&nbsp;' !== $value['display'] ) : ?>
				<th colspan="1" class="td <?php echo esc_attr( str_replace( ' ', '-', 'yaymail-jet-booking-title-' . $value['key'] ) ); ?>" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
					<?php echo esc_html( $value['key'] ); ?>
				</th>
			<?php endif; ?>
		<?php } ?>
		</tr>
	</thead>
	<tbody class="yaymail_element_body_order_item">
		<tr>
		<?php foreach ( $get_booking_order_details as $key => $value ) { ?>
				<?php if ( ! empty( $value['display'] ) && '&nbsp;' !== $value['display'] ) : ?>
					<th colspan="1" class="td <?php echo esc_attr( str_replace( ' ', '-', 'yaymail-jet-booking-item-' . $value['key'] ) ); ?>" scope="row" style="font-weight: normal;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
						<?php echo esc_html( $value['display'], 'yaymail' ); ?>
					</th>
				<?php endif; ?>
			<?php } ?>
		</tr>
	</tbody>
</table>
