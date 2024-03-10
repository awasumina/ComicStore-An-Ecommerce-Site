<?php

use YayMail\License\License;

defined( 'ABSPATH' ) || exit;

$class             = 'notice notice-error';
$renew_text        = 'Please click here to renew your license key and continue receiving automatic updates.';
$licensing_plugins = $this->get_licensing_plugins();
foreach ( $licensing_plugins as $_plugin ) {
	$license = new License( $_plugin['slug'] );
	if ( $license->is_expired() ) {
		$message = "Your license key for {$_plugin['name']} has expired.";
		?>
			<div class="<?php echo esc_attr( $class ); ?>">
				<p>
					<?php echo esc_html( $message ); ?>
					<a href="<?php echo esc_url( $license->get_renewal_url() ); ?>" target="_blank">
						<?php echo esc_html( $renew_text ); ?>
					</a>
				</p>
			</div>
		<?php
	}
}
