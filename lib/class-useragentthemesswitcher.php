<?php
/**
 * UserAgent Themes Switcher
 *
 * @package    UserAgent Themes Switcher
 * @subpackage UserAgentThemesSwitcher Main Functions
/*
	Copyright (c) 2014- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

$useragentthemesswitcher = new UserAgentThemesSwitcher();

/** ==================================================
 * Main Functions
 */
class UserAgentThemesSwitcher {

	/** ==================================================
	 * Construct
	 *
	 * @since 1.59
	 */
	public function __construct() {

		add_filter( 'stylesheet', array( $this, 'load_style_theme' ) );
		add_filter( 'template', array( $this, 'load_template_theme' ) );
	}

	/** ==================================================
	 * Agent check
	 *
	 * @return string $value['theme'] or bool false
	 * @since 1.00
	 */
	private function agent_check() {

		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$user_agent = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
		} else {
			return false;
		}

		if ( get_option( 'uat_switcher' ) ) {
			$ua_switch = get_option( 'uat_switcher' );
			if ( ! empty( $ua_switch ) ) {
				foreach ( $ua_switch as $key => $value ) {
					if ( preg_match( '{' . $value['useragent'] . '}', $user_agent ) ) {
						return $value['theme'];
					}
				}
			}
		}

		return false;
	}

	/** ==================================================
	 * Load Style Theme
	 *
	 * @since 1.00
	 */
	public function load_style_theme() {

		$theme_name = $this->agent_check();

		if ( ! $theme_name ) {
			return get_option( 'stylesheet' );
		} else {
			$themes = wp_get_themes();
			foreach ( $themes as $theme ) {
				if ( $theme['Name'] === $theme_name ) {
					return $theme['Stylesheet'];
				}
			}
		}
	}

	/** ==================================================
	 * Load Template Theme
	 *
	 * @since 1.00
	 */
	public function load_template_theme() {

		$themes = wp_get_themes();
		$nowtheme_template = get_option( 'template' );
		foreach ( $themes as $theme ) {
			if ( $theme['Template'] === $nowtheme_template ) {
				$nowtheme_template = $theme['Template'];
			}
		}

		$theme_name = $this->agent_check();

		if ( ! $theme_name ) {
			return $nowtheme_template;
		} else {
			foreach ( $themes as $theme ) {
				if ( $theme['Name'] === $theme_name ) {
					return $theme['Template'];
				}
			}
		}
	}
}
