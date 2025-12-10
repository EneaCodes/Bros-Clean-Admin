<?php
/**
 * Plugin Name:       Clean Admin – Hide Dashboard Ads
 * Plugin URI:        https://github.com/EneaCodes/clean-admin/
 * Description:       Hides most wp-admin ads, review nags, and promo banners, with simple controls.
 * Version:           1.1.0
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Author:            Enea
 * Author URI:        https://github.com/EneaCodes/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       clean-admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function caa_load_textdomain() {
	load_plugin_textdomain( 'clean-admin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'caa_load_textdomain' );

function caa_admin_notice_activation() {
	if ( get_transient( 'caa_activation_notice' ) ) {
		$url     = esc_url( admin_url( 'options-general.php?page=caa-settings' ) );
		$message = sprintf(
			__( '🎉 Clean Admin is now active! Go to <a href="%s">Settings → Clean Admin</a> to configure what to hide.', 'clean-admin' ),
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
	$defaults = caa_default_options();
	$saved    = get_option( 'caa_options', array() );
	return wp_parse_args( $saved, $defaults );
}

register_activation_hook(
	__FILE__,
	function () {
		if ( ! get_option( 'caa_options' ) ) {
			add_option( 'caa_options', caa_default_options() );
		}
		set_transient( 'caa_activation_notice', true, 5 );
	}
);

register_deactivation_hook(
	__FILE__,
	function () {
	}
);

register_uninstall_hook( __FILE__, 'caa_uninstall' );

function caa_uninstall() {
	delete_option( 'caa_options' );
}

add_action(
	'admin_menu',
	function () {
		add_options_page(
			__( 'Clean Admin', 'clean-admin' ),
			__( 'Clean Admin', 'clean-admin' ),
			'manage_options',
			'caa-settings',
			'caa_render_settings_page'
		);
	}
);

function caa_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$o = caa_get_options();

	if ( isset( $_POST['caa_reset'] ) && check_admin_referer( 'caa_save_options', 'caa_nonce' ) ) {
		delete_option( 'caa_options' );
		$o = caa_default_options();
		update_option( 'caa_options', $o );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings reset to defaults.', 'clean-admin' ) . '</p></div>';
	} elseif ( isset( $_POST['caa_save'] ) && check_admin_referer( 'caa_save_options', 'caa_nonce' ) ) {
		$o['hide_dashboard_ads']     = isset( $_POST['hide_dashboard_ads'] ) ? 1 : 0;
		$o['hide_review_nags']       = isset( $_POST['hide_review_nags'] ) ? 1 : 0;
		$o['hide_plugin_promos']     = isset( $_POST['hide_plugin_promos'] ) ? 1 : 0;
		$o['custom_promo_keywords']  = isset( $_POST['custom_promo_keywords'] ) ? sanitize_textarea_field( wp_unslash( $_POST['custom_promo_keywords'] ) ) : '';
		$o['custom_review_keywords'] = isset( $_POST['custom_review_keywords'] ) ? sanitize_textarea_field( wp_unslash( $_POST['custom_review_keywords'] ) ) : '';
		update_option( 'caa_options', $o );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Clean Admin settings saved.', 'clean-admin' ) . '</p></div>';
	}

	?>
	<div class="wrap caa-wrap">
		<h1><?php esc_html_e( '🧹 Clean Admin', 'clean-admin' ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'Keep your wp-admin focused. We hide marketing / promo clutter but keep real errors and core notices.', 'clean-admin' ); ?>
		</p>

		<form method="post">
			<?php wp_nonce_field( 'caa_save_options', 'caa_nonce' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Dashboard Ads', 'clean-admin' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="hide_dashboard_ads" <?php checked( $o['hide_dashboard_ads'], 1 ); ?> />
							<?php esc_html_e( 'Hide promo widgets and sale banners on the main Dashboard.', 'clean-admin' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Review Nags', 'clean-admin' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="hide_review_nags" <?php checked( $o['hide_review_nags'], 1 ); ?> />
							<?php esc_html_e( 'Hide “please rate us / leave a review” messages.', 'clean-admin' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Plugin Promo Boxes', 'clean-admin' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="hide_plugin_promos" <?php checked( $o['hide_plugin_promos'], 1 ); ?> />
							<?php esc_html_e( 'Hide “Go Pro / Upgrade / Sale” boxes on plugin & settings pages.', 'clean-admin' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Custom Promo Keywords', 'clean-admin' ); ?></th>
					<td>
						<textarea name="custom_promo_keywords" rows="4" cols="50"><?php echo esc_textarea( $o['custom_promo_keywords'] ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Comma-separated list of extra promo keywords to hide (e.g. upsell, sponsor, partner offer).', 'clean-admin' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Custom Review Keywords', 'clean-admin' ); ?></th>
					<td>
						<textarea name="custom_review_keywords" rows="4" cols="50"><?php echo esc_textarea( $o['custom_review_keywords'] ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Comma-separated list of extra review/feedback keywords to hide.', 'clean-admin' ); ?>
						</p>
					</td>
				</tr>
			</table>

			<p>
				<button type="submit" name="caa_reset" class="button button-secondary" onclick="return confirm('<?php echo esc_attr__( 'Reset all settings to defaults?', 'clean-admin' ); ?>');">
					<?php esc_html_e( 'Reset to Defaults', 'clean-admin' ); ?>
				</button>
			</p>

			<p class="submit">
				<button type="submit" name="caa_save" class="button button-primary">
					<?php esc_html_e( 'Save Changes', 'clean-admin' ); ?>
				</button>
			</p>
		</form>
	</div>
	<style>
		.caa-wrap h1 {
			display: flex;
			align-items: center;
			gap: 10px;
		}
		.caa-wrap .description {
			max-width: 600px;
			font-size: 13px;
			opacity: 0.8;
		}
	</style>
	<?php
}

add_action(
	'admin_enqueue_scripts',
	function () {
		$opts = caa_get_options();

		$custom_promo  = array();
		$custom_review = array();

		if ( ! empty( $opts['custom_promo_keywords'] ) ) {
			$custom_promo = array_filter(
				array_map(
					'trim',
					explode( ',', $opts['custom_promo_keywords'] )
				)
			);
		}

		if ( ! empty( $opts['custom_review_keywords'] ) ) {
			$custom_review = array_filter(
				array_map(
					'trim',
					explode( ',', $opts['custom_review_keywords'] )
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
		);

		$review_words = array(
			'rate us',
			'leave a review',
			'please review',
			'enjoying this plugin',
			'if you like this plugin',
			'give us a 5-star',
			'rate this plugin',
			'review us',
			'love this plugin?',
			'share your feedback',
			'feedback',
		);

		$promo_words  = apply_filters( 'caa_promo_words', $promo_words );
		$review_words = apply_filters( 'caa_review_words', $review_words );

		$promo_words  = array_unique( array_merge( $promo_words, $custom_promo ) );
		$review_words = array_unique( array_merge( $review_words, $custom_review ) );

		wp_register_script(
			'caa-admin-clean',
			plugin_dir_url( __FILE__ ) . 'caa-admin-clean.js',
			array(),
			'1.1.0',
			true
		);

		wp_localize_script(
			'caa-admin-clean',
			'CAA_OPTIONS',
			array(
				'hide_dashboard_ads'   => (int) $opts['hide_dashboard_ads'],
				'hide_review_nags'     => (int) $opts['hide_review_nags'],
				'hide_plugin_promos'   => (int) $opts['hide_plugin_promos'],
				'promoWords'           => array_values( $promo_words ),
				'reviewWords'          => array_values( $review_words ),
			)
		);

		wp_enqueue_script( 'caa-admin-clean' );
	},
	99
);

function caa_plugin_action_links( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=caa-settings' ) ) . '">' . esc_html__( 'Settings', 'clean-admin' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'caa_plugin_action_links' );
