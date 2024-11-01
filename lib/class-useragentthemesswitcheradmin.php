<?php
/**
 * UserAgent Themes Switcher
 *
 * @package    UserAgent Themes Switcher
 * @subpackage UserAgentThemesSwitcherAdmin Management screen
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

$useragentthemesswitcheradmin = new UserAgentThemesSwitcherAdmin();

/** ==================================================
 * Management screen
 *
 * @since 1.00
 */
class UserAgentThemesSwitcherAdmin {

	/** ==================================================
	 * Construct
	 *
	 * @since 1.59
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_settings' ) );

		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
		add_filter( 'plugin_action_links', array( $this, 'settings_link' ), 10, 2 );
	}

	/** ==================================================
	 * Add a "Settings" link to the plugins page
	 *
	 * @param  array  $links  links array.
	 * @param  string $file   file.
	 * @return array  $links  links array.
	 * @since 1.00
	 */
	public function settings_link( $links, $file ) {
		static $this_plugin;
		if ( empty( $this_plugin ) ) {
			$this_plugin = 'useragent-themes-switcher/useragentthemesswitcher.php';
		}
		if ( $file == $this_plugin ) {
			$links[] = '<a href="' . admin_url( 'options-general.php?page=UserAgentThemesSwitcher' ) . '">' . __( 'Settings' ) . '</a>';
		}
		return $links;
	}

	/** ==================================================
	 * Settings page
	 *
	 * @since 1.00
	 */
	public function plugin_menu() {
		add_options_page( 'UserAgentThemesSwitcher Options', 'UserAgentThemesSwitcher', 'manage_options', 'UserAgentThemesSwitcher', array( $this, 'plugin_options' ) );
	}

	/** ==================================================
	 * Settings page
	 *
	 * @since 1.00
	 */
	public function plugin_options() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}

		$this->options_updated();

		$scriptname = admin_url( 'options-general.php?page=UserAgentThemesSwitcher' );

		$ua_switch = get_option( 'uat_switcher' );

		$themes = wp_get_themes();

		?>
		<div class="wrap">
			<h2>UserAgent Themes Switcher</h2>
			<details>
			<summary><strong><?php esc_html_e( 'Various links of this plugin', 'useragent-themes-switcher' ); ?></strong></summary>
			<?php $this->credit(); ?>
			</details>

			<details style="margin-bottom: 5px;" open>
			<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><?php esc_html_e( 'Settings' ); ?></summary>
				<h3><?php esc_html_e( 'Type editing', 'useragent-themes-switcher' ); ?></h3>
				<form method="post" action="<?php echo esc_url( $scriptname ); ?>">
				<?php
				wp_nonce_field( 'uat_set', 'uat_switcher_set' );
				foreach ( $ua_switch as $key => $value ) {
					?>
					<details style="margin-bottom: 5px;">
					<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;">
					<input type="checkbox" name="del_uas[<?php echo esc_attr( $key ); ?>]">
					<?php echo esc_html( $value['description'] ); ?>
					</summary>
					<div style="display: block; padding: 20px 0;">
					<?php esc_html_e( 'Themes' ); ?>
						<select name="uat_selecttheme<?php echo esc_attr( $key ); ?>">
						<?php
						foreach ( $themes as $theme ) {
							if ( $ua_switch[ $key ]['theme'] === $theme['Name'] ) {
								?>
								<option value="<?php echo esc_attr( $theme['Name'] ); ?>" selected><?php echo esc_html( $theme['Name'] ); ?></option>
								<?php

							} else {
								?>
								<option value="<?php echo esc_attr( $theme['Name'] ); ?>"><?php echo esc_html( $theme['Name'] ); ?></option>
								<?php
							}
						}
						?>
						</select>
					</div>
					<div>
					<?php esc_html_e( 'User Agent[Regular expression is possible.]', 'useragent-themes-switcher' ); ?>
					</div>
					<div>
					<textarea name="uat_useragent<?php echo esc_attr( $key ); ?>" rows="4" style="width: 100%;"><?php echo esc_textarea( $ua_switch[ $key ]['useragent'] ); ?></textarea>
					</div>
					<div style="display:block;padding:20px 0">
					<?php esc_html_e( 'Description' ); ?>
					<input type="text" name="uat_description<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $ua_switch[ $key ]['description'] ); ?>">
					</div>
					</details>
					<?php
				}
				?>
				<?php submit_button( __( 'Save Changes' ), 'large', 'Manageset', false ); ?>
				&nbsp;
				<?php submit_button( __( 'Default' ), 'large', 'Default', false ); ?>
				&nbsp;
				<?php submit_button( __( 'Delete checked types', 'useragent-themes-switcher' ), 'large', 'Deletetype', false ); ?>
				<p class="description">
				<?php esc_html_e( 'If you delete them all, they will return to their default state.', 'useragent-themes-switcher' ); ?>
				</p>
				</form>
				<hr>
				<h3><?php esc_html_e( 'Add Type', 'useragent-themes-switcher' ); ?></h3>
				<form method="post" action="<?php echo esc_url( $scriptname ); ?>">
				<?php wp_nonce_field( 'uat_add', 'uat_switcher_add' ); ?>
				<div style="display: block; padding: 20px 0;">
				<?php esc_html_e( 'Themes' ); ?>
				<select name="uat_addtheme">
				<?php
				foreach ( $themes as $theme ) {
					?>
					<option value="<?php echo esc_attr( $theme['Name'] ); ?>"><?php echo esc_html( $theme['Name'] ); ?></option>
					<?php
				}
				?>
				</select>
				</div>
				<div><?php esc_html_e( 'User Agent[Regular expression is possible.]', 'useragent-themes-switcher' ); ?></div>
				<div style="display:block">
				<textarea name="uat_useragent_new_type" rows="4" style="width: 100%;"></textarea>
				</div>
				<div style="display:block;padding:20px 0">
				<?php esc_html_e( 'Description' ); ?> : <input type="text" name="uat_description">
				</div>
				<div>
				<?php submit_button( __( 'Add type', 'useragent-themes-switcher' ), 'large', 'Addtype', false ); ?>
				</div>
				</form>
			</details>

		</div>
		<?php
	}

	/** ==================================================
	 * Credit
	 *
	 * @since 1.00
	 */
	private function credit() {

		$plugin_name    = null;
		$plugin_ver_num = null;
		$plugin_path    = plugin_dir_path( __DIR__ );
		$plugin_dir     = untrailingslashit( wp_normalize_path( $plugin_path ) );
		$slugs          = explode( '/', $plugin_dir );
		$slug           = end( $slugs );
		$files          = scandir( $plugin_dir );
		foreach ( $files as $file ) {
			if ( '.' === $file || '..' === $file || is_dir( $plugin_path . $file ) ) {
				continue;
			} else {
				$exts = explode( '.', $file );
				$ext  = strtolower( end( $exts ) );
				if ( 'php' === $ext ) {
					$plugin_datas = get_file_data(
						$plugin_path . $file,
						array(
							'name'    => 'Plugin Name',
							'version' => 'Version',
						)
					);
					if ( array_key_exists( 'name', $plugin_datas ) && ! empty( $plugin_datas['name'] ) && array_key_exists( 'version', $plugin_datas ) && ! empty( $plugin_datas['version'] ) ) {
						$plugin_name    = $plugin_datas['name'];
						$plugin_ver_num = $plugin_datas['version'];
						break;
					}
				}
			}
		}
		$plugin_version = __( 'Version:' ) . ' ' . $plugin_ver_num;
		/* translators: FAQ Link & Slug */
		$faq       = sprintf( __( 'https://wordpress.org/plugins/%s/faq', 'useragent-themes-switcher' ), $slug );
		$support   = 'https://wordpress.org/support/plugin/' . $slug;
		$review    = 'https://wordpress.org/support/view/plugin-reviews/' . $slug;
		$translate = 'https://translate.wordpress.org/projects/wp-plugins/' . $slug;
		$facebook  = 'https://www.facebook.com/katsushikawamori/';
		$twitter   = 'https://twitter.com/dodesyo312';
		$youtube   = 'https://www.youtube.com/channel/UC5zTLeyROkvZm86OgNRcb_w';
		$donate    = __( 'https://shop.riverforest-wp.info/donate/', 'useragent-themes-switcher' );

		?>
		<span style="font-weight: bold;">
		<div>
		<?php echo esc_html( $plugin_version ); ?> | 
		<a style="text-decoration: none;" href="<?php echo esc_url( $faq ); ?>" target="_blank" rel="noopener noreferrer">FAQ</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $support ); ?>" target="_blank" rel="noopener noreferrer">Support Forums</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $review ); ?>" target="_blank" rel="noopener noreferrer">Reviews</a>
		</div>
		<div>
		<a style="text-decoration: none;" href="<?php echo esc_url( $translate ); ?>" target="_blank" rel="noopener noreferrer">
		<?php
		/* translators: Plugin translation link */
		echo esc_html( sprintf( __( 'Translations for %s' ), $plugin_name ) );
		?>
		</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-facebook"></span></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $twitter ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-twitter"></span></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $youtube ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-video-alt3"></span></a>
		</div>
		</span>

		<div style="width: 250px; height: 180px; margin: 5px; padding: 5px; border: #CCC 2px solid;">
		<h3><?php esc_html_e( 'Please make a donation if you like my work or would like to further the development of this plugin.', 'useragent-themes-switcher' ); ?></h3>
		<div style="text-align: right; margin: 5px; padding: 5px;"><span style="padding: 3px; color: #ffffff; background-color: #008000">Plugin Author</span> <span style="font-weight: bold;">Katsushi Kawamori</span></div>
		<button type="button" style="margin: 5px; padding: 5px;" onclick="window.open('<?php echo esc_url( $donate ); ?>')"><?php esc_html_e( 'Donate to this plugin &#187;' ); ?></button>
		</div>

		<?php
	}

	/** ==================================================
	 * Update wp_options table.
	 *
	 * @since 1.00
	 */
	public function options_updated() {

		if ( isset( $_POST['Manageset'] ) && ! empty( $_POST['Manageset'] ) ) {
			if ( check_admin_referer( 'uat_set', 'uat_switcher_set' ) ) {
				$ua_switch = get_option( 'uat_switcher' );
				foreach ( $ua_switch as $key => $value ) {
					if ( isset( $_POST[ 'uat_selecttheme' . $key ] ) && ! empty( $_POST[ 'uat_selecttheme' . $key ] ) ) {
						$ua_switch[ $key ]['theme'] = sanitize_text_field( wp_unslash( $_POST[ 'uat_selecttheme' . $key ] ) );
					}
					if ( isset( $_POST[ 'uat_useragent' . $key ] ) && ! empty( $_POST[ 'uat_useragent' . $key ] ) ) {
						$ua_switch[ $key ]['useragent'] = sanitize_textarea_field( wp_unslash( $_POST[ 'uat_useragent' . $key ] ) );
					}
					if ( isset( $_POST[ 'uat_description' . $key ] ) && ! empty( $_POST[ 'uat_description' . $key ] ) ) {
						$ua_switch[ $key ]['description'] = sanitize_text_field( wp_unslash( $_POST[ 'uat_description' . $key ] ) );
					}
				}
				update_option( 'uat_switcher', $ua_switch );
				echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html__( 'Settings saved.' ) . '</li></ul></div>';
			}
		}

		if ( isset( $_POST['Default'] ) && ! empty( $_POST['Default'] ) ) {
			if ( check_admin_referer( 'uat_set', 'uat_switcher_set' ) ) {
				$themes = wp_get_themes();
				$nowtheme = get_option( 'template' );
				foreach ( $themes as $theme ) {
					if ( $nowtheme === $theme['Template'] ) {
						$nowtheme_name = $theme['Name'];
					}
				}
				$ua_switch = array(
					0 => array(
						'theme' => $nowtheme_name,
						'useragent' => 'iPad',
						'description' => 'iPad',
					),
					1 => array(
						'theme' => $nowtheme_name,
						'useragent' => '^.*Android.*Nexus(((?:(?!Mobile))|(?:(\s(7|10).+))).)*$|Kindle|Silk.*Accelerated|Sony.*Tablet|Xperia Tablet|Sony Tablet S|SAMSUNG.*Tablet|Galaxy.*Tab|SC-01C|SC-01D|SC-01E|SC-02D',
						'description' => 'Andoroid Tablet',
					),
					2 => array(
						'theme' => $nowtheme_name,
						'useragent' => 'iPhone|iPod',
						'description' => 'iPhone iPod',
					),
					3 => array(
						'theme' => $nowtheme_name,
						'useragent' => 'Android.*Mobile',
						'description' => 'Andoroid Smartphone',
					),
					4 => array(
						'theme' => $nowtheme_name,
						'useragent' => 'IEMobile',
						'description' => 'Microsoft Mobile',
					),
					5 => array(
						'theme' => $nowtheme_name,
						'useragent' => 'BlackBerry',
						'description' => 'BlackBerry',
					),
					6 => array(
						'theme' => $nowtheme_name,
						'useragent' => 'DoCoMo\/|KDDI-|UP\.Browser|SoftBank|Vodafone|J-PHONE|MOT-|WILLCOM|DDIPOCKET|PDXGW|emobile|ASTEL|L-mode',
						'description' => 'Japanese Mobile Phone',
					),
				);
				update_option( 'uat_switcher', $ua_switch );
				echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html__( 'Settings' ) . ' --> ' . esc_html__( 'Default' ) . '</li></ul></div>';
			}
		}

		if ( isset( $_POST['Deletetype'] ) && ! empty( $_POST['Deletetype'] ) ) {
			if ( check_admin_referer( 'uat_set', 'uat_switcher_set' ) ) {
				$delete_uas = array();
				if ( isset( $_POST['del_uas'] ) && ! empty( $_POST['del_uas'] ) ) {
					$tmps = filter_var(
						wp_unslash( $_POST['del_uas'] ),
						FILTER_CALLBACK,
						array(
							'options' => function ( $value ) {
								return sanitize_text_field( $value );
							},
						)
					);
					$del_names = array();
					$ua_switch = get_option( 'uat_switcher' );
					foreach ( $tmps as $key => $value ) {
						$del_names[] = $ua_switch[ $key ]['description'];
						unset( $ua_switch[ $key ] );
					}
					$del_name = implode( ',', $del_names );
					$ua_switch = array_values( $ua_switch );
					update_option( 'uat_switcher', $ua_switch );
					echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html__( 'Delete' ) . ' --> ' . esc_html( $del_name ) . '</li></ul></div>';
				}
			}
		}

		if ( isset( $_POST['Addtype'] ) && ! empty( $_POST['Addtype'] ) ) {
			if ( check_admin_referer( 'uat_add', 'uat_switcher_add' ) ) {
				$ua_switch = get_option( 'uat_switcher' );
				if ( isset( $_POST['uat_addtheme'] ) && ! empty( $_POST['uat_addtheme'] ) ) {
					$uat_theme = sanitize_text_field( wp_unslash( $_POST['uat_addtheme'] ) );
				}
				if ( isset( $_POST['uat_useragent_new_type'] ) && ! empty( $_POST['uat_useragent_new_type'] ) ) {
					$uat_agent = sanitize_textarea_field( wp_unslash( $_POST['uat_useragent_new_type'] ) );
				} else {
					echo '<div class="notice notice-error is-dismissible"><ul><li>' . esc_html__( 'There are some unentered items.', 'useragent-content-switcher' ) . '</li></ul></div>';
					return;
				}
				if ( isset( $_POST['uat_description'] ) && ! empty( $_POST['uat_description'] ) ) {
					$uat_description = sanitize_text_field( wp_unslash( $_POST['uat_description'] ) );
					foreach ( $ua_switch as $key => $value ) {
						if ( $uat_description === $value['description'] ) {
							echo '<div class="notice notice-error is-dismissible"><ul><li>' . esc_html__( 'The same name cannot be used.', 'useragent-content-switcher' ) . '</li></ul></div>';
							return;
						}
					}
				} else {
					echo '<div class="notice notice-error is-dismissible"><ul><li>' . esc_html__( 'There are some unentered items.', 'useragent-content-switcher' ) . '</li></ul></div>';
					return;
				}
				$next_count = count( $ua_switch );
				$ua_switch[ $next_count ]['theme'] = $uat_theme;
				$ua_switch[ $next_count ]['useragent'] = $uat_agent;
				$ua_switch[ $next_count ]['description'] = $uat_description;
				update_option( 'uat_switcher', $ua_switch );
				echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html__( 'Settings' ) . ' --> ' . esc_html__( 'Settings saved.' ) . '</li></ul></div>';
			}
		}
	}

	/** ==================================================
	 * Settings register
	 *
	 * @since 1.00
	 */
	public function register_settings() {

		/* old -> new ver 2.00 */
		if ( get_option( 'useragentthemesswitcher_settings' ) ) {
			$uat_settings_old = get_option( 'useragentthemesswitcher_settings' );
			$count = 0;
			$ua_switch = array();
			foreach ( $uat_settings_old as $key => $value ) {
				$ua_switch[ $count ] = $value;
				++$count;
			}
			delete_option( 'useragentthemesswitcher_settings' );
			update_option( 'uat_switcher', $ua_switch );
		}

		if ( ! get_option( 'uat_switcher' ) ) {
			$themes = wp_get_themes();
			$nowtheme = get_option( 'template' );
			foreach ( $themes as $theme ) {
				if ( $nowtheme === $theme['Template'] ) {
					$nowtheme_name = $theme['Name'];
				}
			}
			$ua_switch = array(
				0 => array(
					'theme' => $nowtheme_name,
					'useragent' => 'iPad',
					'description' => 'iPad',
				),
				1 => array(
					'theme' => $nowtheme_name,
					'useragent' => '^.*Android.*Nexus(((?:(?!Mobile))|(?:(\s(7|10).+))).)*$|Kindle|Silk.*Accelerated|Sony.*Tablet|Xperia Tablet|Sony Tablet S|SAMSUNG.*Tablet|Galaxy.*Tab|SC-01C|SC-01D|SC-01E|SC-02D',
					'description' => 'Andoroid Tablet',
				),
				2 => array(
					'theme' => $nowtheme_name,
					'useragent' => 'iPhone|iPod',
					'description' => 'iPhone iPod',
				),
				3 => array(
					'theme' => $nowtheme_name,
					'useragent' => 'Android.*Mobile',
					'description' => 'Andoroid Smartphone',
				),
				4 => array(
					'theme' => $nowtheme_name,
					'useragent' => 'IEMobile',
					'description' => 'Microsoft Mobile',
				),
				5 => array(
					'theme' => $nowtheme_name,
					'useragent' => 'BlackBerry',
					'description' => 'BlackBerry',
				),
				6 => array(
					'theme' => $nowtheme_name,
					'useragent' => 'DoCoMo\/|KDDI-|UP\.Browser|SoftBank|Vodafone|J-PHONE|MOT-|WILLCOM|DDIPOCKET|PDXGW|emobile|ASTEL|L-mode',
					'description' => 'Japanese Mobile Phone',
				),
			);
			update_option( 'uat_switcher', $ua_switch );
		}
	}
}


