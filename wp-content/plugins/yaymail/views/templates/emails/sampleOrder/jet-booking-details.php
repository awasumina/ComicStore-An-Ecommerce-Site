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

			<th colspan="1" class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
				<?php esc_html_e( 'Check In', 'yaymail' ); ?>
			</th>
			<th colspan="1" class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
				<?php esc_html_e( 'adults', 'yaymail' ); ?>
			</th>
			<th colspan="1" class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
				<?php esc_html_e( 'children', 'yaymail' ); ?>
			</th>
			<th colspan="1" class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
				<?php esc_html_e( 'name', 'yaymail' ); ?>
			</th>
			<th colspan="1" class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
				<?php esc_html_e( 'Time', 'yaymail' ); ?>
			</th>
		
		</tr>
	</thead>
	<tbody class="yaymail_element_body_order_item">
		<tr>
			<th colspan="1" class="td" scope="row" style="font-weight: normal;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
				<?php echo esc_html( wc_format_datetime( new WC_DateTime() ) ); ?>
			</th>
			<th colspan="1" class="td" scope="row" style="font-weight: normal;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
				<?php echo esc_html( '1' ); ?>
			</th>
			<th colspan="1" class="td" scope="row" style="font-weight: normal;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
				<?php echo esc_html( '1' ); ?>
			</th>
			<th colspan="1" class="td" scope="row" style="font-weight: normal;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
				<?php echo esc_html( 'YayMail' ); ?>
			</th>
			<th colspan="1" class="td" scope="row" style="font-weight: normal;text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>;<?php echo esc_attr( $textColor ); ?>">
				<?php echo esc_html( '18:00' ); ?>
			</th>
		</tr>
	</tbody>
</table>
