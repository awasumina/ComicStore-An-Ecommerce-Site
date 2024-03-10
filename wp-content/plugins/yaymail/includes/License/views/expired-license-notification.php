<div class="update-message notice inline notice-warning notice-alt">
	<p class="license_expired_text">
		<span><?php echo esc_html( 'Your license has expired, please ' ); ?></span>
		<a target="_blank" href="<?php echo esc_url( $license->get_renewal_url() ); ?>"><?php echo esc_html( 'renew this license' ); ?></a>
		<span><?php echo esc_html( ' to download this update. ' ); ?></span>
	</p>
</div>
