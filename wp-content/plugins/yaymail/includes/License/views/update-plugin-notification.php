<?php
	$changelog_link = self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $file . '&section=changelog&TB_iframe=true&width=600&height=800' );
?>
<div class="update-message notice inline notice-warning notice-alt">
	<p>
		<span><?php echo esc_html( "There is a new version of {$plugin['name']} available." ); ?></span>
		<a target="_blank" class="thickbox open-plugin-details-modal" href="<?php echo esc_url( $changelog_link ); ?>"><?php echo esc_html( 'View version ' . $plugin_version_info['new_version'] . ' details' ); ?></a>
	</p>
</div>
