<?php
/**
 * Main Grow page
 *
 * @since  1.0
 * @author VanboDevelops
 */
?>

<div class="wrap woocommerce_grow">
	<h1><?php _e( 'Grow with WooCommerce', 'woocommerce-grow' ); ?> <?php echo __( 'by' ); ?>
	<a href="#" class="wc-grow-logo"><?php echo __( 'Raison' ) ?></a></h1>

	<?php
	// TODO: May Be add a wrapper actions or some way to load either form or clean page
	if (0 === strpos( $current_tab, 'settings' )) { ?>
	<form method="<?php echo esc_attr( apply_filters( 'woocommerce_settings_form_method_tab_' . $current_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">
		<?php } ?>

		<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
			<?php
			foreach ( $tabs as $name => $label ) {
				/**
				 * Filter woocommerce_grow_tabs_link_html - Allows for html modification on the tabs link.
				 */
				echo apply_filters( 'woocommerce_grow_tabs_link_html', '<a href="' . admin_url( 'admin.php?page=woocommerce-grow&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>', $name, $label );
			}

			// Allow for tabs extended html
			do_action( 'woocommerce_grow_page_tabs' );
			?>
		</h2>

		<?php
		do_action( 'woocommerce_grow_page_content_' . $current_tab );
		?>

	<?php if (0 === strpos( $current_tab, 'settings' )) { ?>
		<p class="submit">
			<?php if ( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
				<input name="save" class="button-primary" type="submit" value="<?php esc_attr( _e( 'Save changes', 'woocommerce-grow' ) ); ?>" />
			<?php endif; ?>
			<input type="hidden" name="subtab" id="last_tab" />
			<?php wp_nonce_field( 'woocommerce-grow-settings' ); ?>
		</p>
	</form>
	<?php } ?>
</div>