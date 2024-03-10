<?php

defined( 'ABSPATH' ) || exit;
use YayMail\Helper\Helper;

$text_align       = is_rtl() ? 'right' : 'left';
$yaymail_settings = get_option( 'yaymail_settings' );

?>
<?php
foreach ( $order_items as $item ) {
	$data                       = wc_get_order_item_meta( $item->get_id(), TECHXELA_WOOCOMMERCE_LATEPOINT_ORDER_ITEM_META_KEY );
	$booking_id                 = isset( $data['booking'] ) && isset( $data['booking']['id'] ) ? $data['booking']['id'] : null;
	$booking_total_attendies    = isset( $data['booking'] ) && isset( $data['booking']['total_attendies'] ) ? $data['booking']['total_attendies'] : null;
	$booking_service_id         = isset( $data['booking'] ) && isset( $data['booking']['service_id'] ) ? $data['booking']['service_id'] : null;
	$booking_duration           = isset( $data['booking'] ) && isset( $data['booking']['duration'] ) ? $data['booking']['duration'] : null;
	$booking_service_extras_ids = isset( $data['booking'] ) && isset( $data['booking']['service_extras_ids'] ) ? $data['booking']['service_extras_ids'] : null;
	$booking_start_date         = isset( $data['booking'] ) && isset( $data['booking']['start_date'] ) ? $data['booking']['start_date'] : null;
	$booking_start_time         = isset( $data['booking'] ) && isset( $data['booking']['start_time'] ) ? $data['booking']['start_time'] : null;
	$booking_payment_method     = isset( $data['booking'] ) && isset( $data['booking']['payment_method'] ) ? $data['booking']['payment_method'] : null;
	$booking_agent_id           = isset( $data['booking'] ) && isset( $data['booking']['agent_id'] ) ? $data['booking']['agent_id'] : null;

	$customer_name              = isset( $data['customer'] ) && isset( $data['customer']['first_name'] ) && isset( $data['customer']['last_name'] ) ? $data['customer']['first_name'] . ' ' . $data['customer']['last_name'] : null;
	$customer_phone             = isset( $data['customer'] ) && isset( $data['customer']['phone'] ) ? $data['customer']['phone'] : null;
	$customer_note              = isset( $data['customer'] ) && isset( $data['customer']['notes'] ) ? $data['customer']['notes'] : null;
	$customer_id                = isset( $data['customer'] ) && isset( $data['customer']['id'] ) ? $data['customer']['id'] : null;
	$customer_wordpress_user_id = isset( $data['customer'] ) && isset( $data['customer']['wordpress_user_id'] ) ? $data['customer']['wordpress_user_id'] : null;
	//do_action('latepoint_includes');
	//$booking_service_extra           = new \OsBookingServiceExtraModel();
	// $booking_service_extras          = $booking_service_extra->where( array( 'booking_id' => 377 ) )->get_results_as_models();
	// $booking_service_extras_quantity = ! empty( $booking_service_extras > 0 ) ? $booking_service_extras[0]->quantity : null;

	$booking_service_extras_quantity = null;
	if ( ! empty( $booking_service_extras_ids ) ) {
		$cleanServiceExtras = OsServiceExtrasHelper::extract_service_extras( $booking_service_extras_ids );
		foreach ( $cleanServiceExtras as $serviceExtraId => $serviceExtraQty ) {
			$serviceExtra = new OsServiceExtraModel( $serviceExtraId );
			if ( $serviceExtra->exists() ) {
				$booking_service_extras_quantity = $serviceExtraQty;
			}
		}
	}
	?>
<h2 class="yaymail_woo_latepiont_booking_title" style="padding-bottom: 10px;">
	<?php esc_html_e( 'Booking Detail', 'yaymail' ); ?>
</h2>
<table class="yaymail_woo_latepiont_booking_detail" cellspacing="0" cellpadding="6" border="1" style="border-collapse: separate;width: 100% !important;color: inherit;flex-direction:column;border: 1px solid;border-color: #e5e5e5;" width="100%">
	<thead>
		<tr style="word-break: normal; text-align: center;">
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
			<?php if ( $booking_service_extras_quantity ) { ?>
				<th class="td" scope="col" style="border: 1px solid;border-color: #e5e5e5;">
					<?php esc_html_e( 'Child', 'yaymail' ); ?>
				</th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<tr style="word-break: normal; text-align: center;">
			<td class="td" scope="row" style="border: 1px solid;border-color: #e5e5e5;">
				<?php echo wp_kses_post( date_i18n( wc_date_format(), strtotime( $booking_start_date ) ) ); ?>
			</td>

			<td class="td" scope="row" style="border: 1px solid;border-color: #e5e5e5;">
				<?php
					$hours   = floor( $booking_start_time / 60 );
					$minutes = $booking_start_time % 60;
					echo wp_kses_post( date_i18n( 'H:i', strtotime( "$hours:$minutes" ) ) );
				?>
			</td>

			<td class="td" scope="row" style="border: 1px solid;border-color: #e5e5e5;">
				<?php echo wp_kses_post( $customer_name ); ?>
			</td>

			<td class="td" scope="row" style="border: 1px solid;border-color: #e5e5e5;">
				<?php echo wp_kses_post( $booking_total_attendies ); ?>
			</td>
			<?php if ( $booking_service_extras_quantity ) { ?>
				<td class="td" scope="row" style="border: 1px solid;border-color: #e5e5e5;">
					<?php echo wp_kses_post( $booking_service_extras_quantity ); ?>
				</td>
			<?php } ?>
		</tr>
	</tbody>
</table>

<?php } ?>
