<?php
	$plugin_slug = $_plugin['slug'];
	$plugin_name = $_plugin['name'];
	$is_addon    = false !== strpos( $plugin_slug, 'addon' );
	$license     = new \YayMail\License\License( 'yaymail' );
?>
<div class="yaycommerce-license-card" id="<?php echo esc_attr( "{$plugin_slug}_license_card" ); ?>" style="<?php echo esc_attr( $is_addon ? 'order: 999;' : 'order: ' . YAYMAIL_MENU_ORDER . ';' ); ?>">
	<div class="yaycommerce-license-card-header">
		<div class="yaycommerce-license-card-title-wrapper">
			<h3 class="yaycommerce-license-card-title yaycommerce-license-card-header-item">
				<?php echo esc_html( "{$plugin_name} activation" ); ?>
			</h3>
		</div>
	</div>
	<div class="yaycommerce-license-card-body">
		<label for="<?php echo esc_attr( "{$plugin_slug}_license_input" ); ?>"><?php echo esc_html( 'Your license key:' ); ?></label>
		<div class="yaycommerce-license-input-row">
			<input autocomplete="new-password" class="yaycommerce-license-input" type="password" id="<?php echo esc_attr( "{$plugin_slug}_license_input" ); ?>" value="" />
			<button class="button-primary yaycommerce-activate-license-button"  id="<?php echo esc_attr( "{$plugin_slug}_activate_button" ); ?>" data-plugin="<?php echo esc_attr( $plugin_slug ); ?>">
				<span>Activate License</span>
				<span class="activate-loading sync-loading">
					<svg
					data-v-7957300f=""
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 24 24"
					>
						<path
						data-v-7957300f=""
						d="M21.66 10.37a.62.62 0 00.07-.19l.75-4a1 1 0 00-2-.36l-.37 2a9.22 9.22 0 00-16.58.84 1 1 0 00.55 1.3 1 1 0 001.31-.55A7.08 7.08 0 0112.07 5a7.17 7.17 0 016.24 3.58l-1.65-.27a1 1 0 10-.32 2l4.25.71h.16a.93.93 0 00.34-.06.33.33 0 00.1-.06.78.78 0 00.2-.11l.08-.1a1.07 1.07 0 00.14-.16.58.58 0 00.05-.16zM19.88 14.07a1 1 0 00-1.31.56A7.08 7.08 0 0111.93 19a7.17 7.17 0 01-6.24-3.58l1.65.27h.16a1 1 0 00.16-2L3.41 13a.91.91 0 00-.33 0H3a1.15 1.15 0 00-.32.14 1 1 0 00-.18.18l-.09.1a.84.84 0 00-.07.19.44.44 0 00-.07.17l-.75 4a1 1 0 00.8 1.22h.18a1 1 0 001-.82l.37-2a9.22 9.22 0 0016.58-.83 1 1 0 00-.57-1.28z"
						></path>
					</svg>
				</span>
			</button>
		</div>
		<div class="<?php echo esc_attr( $plugin_slug ); ?>-license-message yaycommerce-license-message"></div> 
	</div>
	<div class="yaycommerce-license-card-footer">
	</div>
</div>
