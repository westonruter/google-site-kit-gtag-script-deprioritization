<?php
/**
 * Plugin Name: Site Kit GTag Script Deprioritization
 * Plugin URI: https://github.com/westonruter/google-site-kit-gtag-script-deprioritization
 * Description: Moves the GTag script to the footer, adds <code>fetchpriority=low</code>, and eliminates the dns-prefetch resource hint to deprioritize to prevent it from impacting the critical rendering path.
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

use _WP_Dependency;

// Short-circuit functionality to facilitate benchmarking performance impact.
if ( isset( $_GET['disable_site_kit_gtag_script_deprioritization'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return;
}

const HANDLE = 'google_gtagjs';

// Move the GTag script to the footer.
add_action(
	'wp_enqueue_scripts',
	static function (): void {
		$dep = wp_scripts()->query( HANDLE, 'registered' );
		if ( ! ( $dep instanceof _WP_Dependency ) || ! wp_script_is( $dep->handle, 'enqueued' ) ) {
			return;
		}

		// Move the script to the footer.
		$dep->add_data( 'group', 1 );

		// Remove the inline script to move it to wp_head.
		$inline_script = null;
		foreach ( array( 'before', 'after' ) as $key ) {
			if ( ! isset( $dep->extra[ $key ] ) || ! is_array( $dep->extra[ $key ] ) ) {
				continue;
			}
			for ( $i = 0, $len = count( $dep->extra[ $key ] ); $i < $len; $i++ ) {
				if ( is_string( $dep->extra[ $key ][ $i ] ) && str_contains( $dep->extra[ $key ][ $i ], 'function gtag(' ) ) {
					$inline_script = $dep->extra[ $key ][ $i ];
					array_splice( $dep->extra[ $key ], $i, 1 );
					break 2;
				}
			}
		}

		// Make sure the dataLayer object continues to be defined in the wp_head in case it is used before the footer.
		if ( isset( $inline_script ) ) {
			$handle = 'google_site_kit_gtag_deprioritization_data_layer';
			wp_register_script( $handle, false, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter, WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Intentionally in head and version is irrelevant.
			wp_add_inline_script( $handle, $inline_script );
			wp_enqueue_script( $handle );
		}
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

// Undo wp_dependencies_unique_hosts() for GTag.
// TODO: wp_dependencies_unique_hosts() could omit adding any resource hints for dependencies with low priority.
add_filter(
	'wp_resource_hints',
	static function ( $urls, $relation_type ): array {
		if ( ! is_array( $urls ) ) {
			$urls = array();
		}
		if ( 'dns-prefetch' === $relation_type ) {
			$urls = array_filter(
				$urls,
				static function ( $url ): bool {
					return is_string( $url ) && ! str_contains( $url, 'www.googletagmanager.com' ); // Note that wp_dependencies_unique_hosts() does not prefix with '//' but Site Kit does when filtering.
				}
			);
		}
		return $urls;
	},
	100,
	2
);
