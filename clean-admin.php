<?php
/**
 * Plugin Name:       Admin Clean – Hide Dashboard Ads
 * Plugin URI:        https://github.com/EneaCodes/clean-admin/
 * Description:       Hides most wp-admin ads, review nags, and promo banners, with simple controls.
 * Version:           1.1.0
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Author:            Enea
 * Author URI:        https://github.com/EneaCodes/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       admin-clean
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function caa_admin_notice_activation() {
	if ( get_transient( 'caa_activation_notice' ) ) {
		$url = esc_url( admin_url( 'options-general.php?page=caa-settings' ) );

		$message = sprintf(
			/* translators: %s: URL to the Admin Clean settings page. */
			__( '🎉 Admin Clean is now active! Go to <a href="%s">Settings → Admin Clean</a> to configure what to hide.', 'admin-clean' ),
			$url
		);

		echo wp_kses(
			'<div class="notice notice-info is-dismissible"><p>' . $message . '</p></div>',
			array(
				'div' => array(
					'class' => array(),
				),
				'p'   => array(),
				'a'   => array(
					'href' => array(),
				),
			)
		);
		delete_transient( 'caa_activation_notice' );
	}
}
add_action( 'admin_notices', 'caa_admin_notice_activation' );

function caa_default_options() {
	return array(
		'hide_dashboard_ads'      => 1,
		'hide_review_nags'        => 1,
		'hide_plugin_promos'      => 1,
		'custom_promo_keywords'   => '',
		'custom_review_keywords'  => '',
	);
}

function caa_get_options() {
	$o = get_option( 'caa_options' );
	if ( ! is_array( $o ) ) {
		$o = caa_default_options();
		update_option( 'caa_options', $o );
	}
	return $o;
}

function caa_activate() {
	if ( ! get_option( 'caa_options' ) ) {
		update_option( 'caa_options', caa_default_options() );
	}
	set_transient( 'caa_activation_notice', 1, 60 );
}
register_activation_hook( __FILE__, 'caa_activate' );

function caa_admin_menu() {
	add_options_page(
		__( 'Admin Clean', 'admin-clean' ),
		__( 'Admin Clean', 'admin-clean' ),
		'manage_options',
		'caa-settings',
		'caa_render_settings_page'
	);
}
add_action( 'admin_menu', 'caa_admin_menu' );

function caa_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$o = caa_get_options();

	if ( isset( $_POST['caa_reset'] ) && check_admin_referer( 'caa_reset_options', 'caa_nonce' ) ) {
		delete_option( 'caa_options' );
		$o = caa_default_options();
		update_option( 'caa_options', $o );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings reset to defaults.', 'admin-clean' ) . '</p></div>';
	} elseif ( isset( $_POST['caa_save'] ) && check_admin_referer( 'caa_save_options', 'caa_nonce' ) ) {
		$o['hide_dashboard_ads']     = isset( $_POST['hide_dashboard_ads'] ) ? 1 : 0;
		$o['hide_review_nags']       = isset( $_POST['hide_review_nags'] ) ? 1 : 0;
		$o['hide_plugin_promos']     = isset( $_POST['hide_plugin_promos'] ) ? 1 : 0;
		$o['custom_promo_keywords']  = isset( $_POST['custom_promo_keywords'] ) ? wp_kses_post( wp_unslash( $_POST['custom_promo_keywords'] ) ) : '';
		$o['custom_review_keywords'] = isset( $_POST['custom_review_keywords'] ) ? wp_kses_post( wp_unslash( $_POST['custom_review_keywords'] ) ) : '';
		update_option( 'caa_options', $o );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Admin Clean settings saved.', 'admin-clean' ) . '</p></div>';
	}

	?>
	<div class="wrap">
		<h1><?php esc_html_e( '🧹 Admin Clean', 'admin-clean' ); ?></h1>
		<p><?php esc_html_e( 'Keep your wp-admin focused. We hide marketing / promo clutter but keep real errors and core notices.', 'admin-clean' ); ?></p>

		<form method="post">
			<?php wp_nonce_field( 'caa_save_options', 'caa_nonce' ); ?>

			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="hide_dashboard_ads"><?php esc_html_e( 'Dashboard Ads', 'admin-clean' ); ?></label>
						</th>
						<td>
							<label>
								<input type="checkbox" name="hide_dashboard_ads" id="hide_dashboard_ads" value="1" <?php checked( $o['hide_dashboard_ads'], 1 ); ?> />
								<?php esc_html_e( 'Hide promo widgets and sale banners on the main Dashboard.', 'admin-clean' ); ?>
							</label>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="hide_review_nags"><?php esc_html_e( 'Review Nags', 'admin-clean' ); ?></label>
						</th>
						<td>
							<label>
								<input type="checkbox" name="hide_review_nags" id="hide_review_nags" value="1" <?php checked( $o['hide_review_nags'], 1 ); ?> />
								<?php esc_html_e( 'Hide “please rate us” and similar review request notices.', 'admin-clean' ); ?>
							</label>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="hide_plugin_promos"><?php esc_html_e( 'Plugin / Theme Promo Notices', 'admin-clean' ); ?></label>
						</th>
						<td>
							<label>
								<input type="checkbox" name="hide_plugin_promos" id="hide_plugin_promos" value="1" <?php checked( $o['hide_plugin_promos'], 1 ); ?> />
								<?php esc_html_e( 'Hide most upsell / upgrade banners from plugins and themes.', 'admin-clean' ); ?>
							</label>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="custom_promo_keywords"><?php esc_html_e( 'Custom Promo Keywords', 'admin-clean' ); ?></label>
						</th>
						<td>
							<textarea name="custom_promo_keywords" id="custom_promo_keywords" class="large-text" rows="3"><?php echo esc_textarea( $o['custom_promo_keywords'] ); ?></textarea>
							<p class="description">
								<?php esc_html_e( 'Comma-separated list of extra words/phrases that mark a notice as promotional.', 'admin-clean' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="custom_review_keywords"><?php esc_html_e( 'Custom Review Keywords', 'admin-clean' ); ?></label>
						</th>
						<td>
							<textarea name="custom_review_keywords" id="custom_review_keywords" class="large-text" rows="3"><?php echo esc_textarea( $o['custom_review_keywords'] ); ?></textarea>
							<p class="description">
								<?php esc_html_e( 'Comma-separated list of extra words/phrases that mark a notice as a review request.', 'admin-clean' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>

			<p class="submit">
				<button type="submit" name="caa_save" class="button-primary"><?php esc_html_e( 'Save Changes', 'admin-clean' ); ?></button>
				<button type="submit" name="caa_reset" class="button-secondary" onclick="return confirm('Reset Admin Clean settings to defaults?');"><?php esc_html_e( 'Reset to Defaults', 'admin-clean' ); ?></button>
			</p>
		</form>
	</div>
	<?php
}

function caa_admin_init() {
	$o = caa_get_options();

	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	// Only run in admin, not on core update screens.
	if ( 'update-core' === $screen->id ) {
		return;
	}

	$custom_promo   = array();
	$custom_review  = array();

	if ( ! empty( $o['custom_promo_keywords'] ) ) {
		$custom_promo = array_filter(
			array_map(
				'trim',
				explode( ',', $o['custom_promo_keywords'] )
			)
		);
	}

	if ( ! empty( $o['custom_review_keywords'] ) ) {
		$custom_review = array_filter(
			array_map(
				'trim',
				explode( ',', $o['custom_review_keywords'] )
			)
		);
	}

	$promo_words = array(
		'upgrade',
		'go pro',
		'pro version',
		'pro plan',
		'unlock all features',
		'sale',
		'black friday',
		'cyber monday',
		'limited time',
		'discount',
		'bundle deal',
		'best price',
		'lifetime deal',
		'special offer',
		'premium version',
		'exclusive deal',
		'buy now',
		'get it now',
		'upgrade today',
		'pro features',
		'premium features',
		'bluehost',
	);

	$review_words = array(
		'rate us',
		'leave a review',
		'please review',
		'enjoying this plugin',
		'enjoying our plugin',
		'if you like this plugin',
		'if you enjoy this plugin',
		'review this plugin',
		'leave us a review',
		'give us a review',
		'star rating',
	);

	$promo_words  = apply_filters( 'caa_promo_words', $promo_words );
	$review_words = apply_filters( 'caa_review_words', $review_words );

	$promo_words  = array_unique( array_merge( $promo_words, $custom_promo ) );
	$review_words = array_unique( array_merge( $review_words, $custom_review ) );

	wp_register_script(
		'caa-admin-clean',
		plugins_url( 'caa-admin-clean.js', __FILE__ ),
		array(),
		'1.1.0',
		true
	);

	wp_localize_script(
		'caa-admin-clean',
		'CAA_OPTIONS',
		array(
			'hide_dashboard_ads' => (bool) $o['hide_dashboard_ads'],
			'hide_review_nags'   => (bool) $o['hide_review_nags'],
			'hide_plugin_promos' => (bool) $o['hide_plugin_promos'],
			'promoWords'         => array_values( $promo_words ),
			'reviewWords'        => array_values( $review_words ),
		)
	);

	wp_enqueue_script( 'caa-admin-clean' );
}
add_action( 'admin_enqueue_scripts', 'caa_admin_init' );

function caa_plugin_action_links( $links ) {
	$url = esc_url( admin_url( 'options-general.php?page=caa-settings' ) );
	$settings_link = '<a href="' . $url . '">' . esc_html__( 'Settings', 'admin-clean' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'caa_plugin_action_links' );
