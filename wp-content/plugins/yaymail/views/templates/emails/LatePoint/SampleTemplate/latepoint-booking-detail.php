<?php

defined( 'ABSPATH' ) || exit;
use YayMail\Helper\Helper;

$text_align       = is_rtl() ? 'right' : 'left';
$yaymail_settings = get_option( 'yaymail_settings' );

?>
<h2 class="yaymail_woo_latepiont_booking_title" style="padding-bottom: 10px;">
	<?php esc_html_e( 'Booking Number:', 'yaymail' ); ?>
	<span class="yaymail_woo_latepiont_booking_id"><?php echo wp_kses_post( '#1' ); ?></span>
</h2>
<table class="yaymail_woo_latepiont_booking_detail" cellspacing="0" cellpadding="6" border="1" style="text-align: center;border-collapse: separate;width: 100% !important;color: inherit;flex-direction:column;border: 1px solid;border-color: #e5e5e5;" width="100%">
	<thead>
		<tr style="word-break: normal; text-align: center">
			<th class="td" scope="col" style="border: 1px solid;border-color: #e5e5e5;">
				<?php esc_html_e( 'Date/Tanggal', 'yaymail' ); ?>
			</th>
			<th class="td" scope="col" style="border: 1px solid;border-color: #e5e5e5;">
				<?php esc_html_e( 'Time', 'yaymail' ); ?>
			</th>
			<th class="td" scope="col" style="border: 1px solid;border-color: #e5e5e5;">
				<?php esc_html_e( 'Name/Nama', 'yaymail' ); ?>
			</th>
			<th class="td" scope="col" style="border: 1px solid;border-color: #e5e5e5;">
				<?php esc_html_e( 'Adult', 'yaymail' ); ?>
			</th>
			
			<th class="td" scope="col" style="border: 1px solid;border-color: #e5e5e5;">
				<?php esc_html_e( 'Child', 'yaymail' ); ?>
			</th>
			
		</tr>
	</thead>
	<tbody>
		<tr style="word-break: normal; text-align: center;">
			<td class="td" scope="row" style="border: 1px solid;border-color: #e5e5e5;">
				<?php echo wp_kses_post( date_i18n( wc_date_format(), current_time( 'Y-m-d' ) ) ); ?>
			</td>
			<td class="td" scope="row" style="border: 1px solid;border-color: #e5e5e5;">
				<?php echo wp_kses_post( '10:00' ); ?>
			</td>
			<td class="td" scope="row" style="border: 1px solid;border-color: #e5e5e5;">
				<?php echo wp_kses_post( 'YayMail' ); ?>
			</td>
			<td class="td" scope="row" style="border: 1px solid;border-color: #e5e5e5;">
				<?php echo wp_kses_post( '10' ); ?>
			</td>
			<td class="td" scope="row" style="border: 1px solid;border-color: #e5e5e5;">
				<?php echo wp_kses_post( '5' ); ?>
			</td>
		</tr>
	</tbody>
</table>
