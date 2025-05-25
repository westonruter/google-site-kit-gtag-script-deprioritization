<?php
/**
 * Plugin Name: Site Kit GTag Script Deprioritization
 * Plugin URI: https://github.com/westonruter/google-site-kit-gtag-script-deprioritization
 * Description: Moves the GTag script to the footer and adds `fetchpriority=low` to further deprioritize to prevent it from impacting the critical rendering path.
 * Requires at least: 5.7
 * Requires PHP: 7.2
 * Version: 0.1.0
 * Author: Weston Ruter
 * Author URI: https://weston.ruter.net/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Update URI: https://github.com/westonruter/google-site-kit-gtag-script-deprioritization
 * GitHub Plugin URI: https://github.com/westonruter/google-site-kit-gtag-script-deprioritization
 *
 * @package SiteKitGTagScriptDeprioritization
 */

namespace SiteKitGTagScriptDeprioritization;

const HANDLE = 'google_gtagjs';

// Short-circuit functionality to facilitate benchmarking performance impact.
if ( isset( $_GET['disable_site_kit_gtag_script_deprioritization'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return;
}

// Move the GTag script to the footer.
add_action(
	'wp_enqueue_scripts',
	static function (): void {
		wp_script_add_data( HANDLE, 'group', 1 );
	},
	100
);

// Add fetchpriority=low to the GTag script.
add_filter(
	'wp_script_attributes',
	static function ( $attributes ): array {
		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}

		if (
			isset( $attributes['id'] ) &&
			HANDLE . '-js' === $attributes['id']
		) {
			$attributes['fetchpriority'] = 'low';
		}

		return $attributes;
	}
);
