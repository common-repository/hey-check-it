<?php 
/*
	Plugin Name: Hey Check It
	Plugin URI: https://heycheckit.com
	Description: Adds your Hey Check It Tracking Code to your WordPress site.
	Tags: hey, check, it
	Author: tripleNERDscore
	Author URI: https://heycheckit.com/
	Requires at least: 4.1
	Tested up to: 5.9.2
	Stable tag: 2.0.2
	Version: 2.0.2
	Requires PHP: 5.6
	Text Domain: hey-check-it
	Domain Path: /languages
	License: GPL v2 or later
*/

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 
	2 of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	with this program. If not, visit: https://www.gnu.org/licenses/
	
	Copyright 2020 tripleNERDscore. All rights reserved.
*/

if (!defined('ABSPATH')) die();

if (!class_exists('HeyCheckit')) {
	
	class HeyCheckIt {
		
		public function __construct() {
			$this->constants();
			$this->includes();
			add_action('admin_menu',            array($this, 'add_menu'));
			add_filter('admin_init',            array($this, 'add_settings'));
			add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
			add_filter('plugin_action_links',   array($this, 'action_links'), 10, 2);
			add_filter('plugin_row_meta',       array($this, 'plugin_links'), 10, 2);
			add_action('plugins_loaded',        array($this, 'load_i18n'));
			add_action('admin_init',            array($this, 'check_version'));
			add_action('admin_init',            array($this, 'reset_options'));
			add_action('admin_notices',         array($this, 'admin_notices'));
		} 
		
		function constants() {
			if (!defined('HCI_VERSION')) define('HCI_VERSION', '1.0.2');
			if (!defined('HCI_REQUIRE')) define('HCI_REQUIRE', '4.1');
			if (!defined('HCI_AUTHOR'))  define('HCI_AUTHOR',  'tripleNERDscore');
			if (!defined('HCI_NAME'))    define('HCI_NAME',    'Hey Check It');
			if (!defined('HCI_HOME'))    define('HCI_HOME',    'https://heycheckit.com');
			if (!defined('HCI_PATH'))    define('HCI_PATH',    'options-general.php?page=hey-check-it');
			if (!defined('HCI_URL'))     define('HCI_URL',     plugin_dir_url(__FILE__));
			if (!defined('HCI_DIR'))     define('HCI_DIR',     plugin_dir_path(__FILE__));
			if (!defined('HCI_FILE'))    define('HCI_FILE',    plugin_basename(__FILE__));
			if (!defined('HCI_SLUG'))    define('HCI_SLUG',    basename(dirname(__FILE__)));
		}
		
		function includes() {
			require_once HCI_DIR .'inc/plugin-core.php';
		}
		
		function add_menu() {
			$title_page = esc_html__('Hey Check It', 'hey-check-it');
			$title_menu = esc_html__('Hey Check It', 'hey-check-it');
			add_options_page($title_page, $title_menu, 'manage_options', 'hey-check-it', array($this, 'display_settings'));
		}
		
		function add_settings() {
			register_setting('hci_plugin_options', 'hci_options', array($this, 'validate_settings'));
		}
		
		function admin_scripts($hook) {
			if ($hook === 'settings_page_hey-check-it') {
				wp_enqueue_style('hey-check-it', HCI_URL .'css/settings.css', array(), HCI_VERSION);
				wp_enqueue_script('hey-check-it', HCI_URL .'js/settings.js', array('jquery'), HCI_VERSION);
				$this->localize_scripts();
			}
		}
		
		function localize_scripts() {
			$script = [
				'confirm_message' => esc_html__('Are you sure you want to restore all default options?', 'hey-check-it'),
			];			
			wp_localize_script('hey-check-it', 'Hey_Check_It', $script);
		}
		
		function action_links($links, $file) {
			if ($file === HCI_FILE) {
				$hci_links = '<a href="'. admin_url(HCI_PATH) .'">'. esc_html__('Settings', 'hey-check-it') .'</a>';
				array_unshift($links, $hci_links);
			}
			return $links;
		}
		
		function plugin_links($links, $file) {
			if ($file === HCI_FILE) {
				$rate_href  = 'https://wordpress.org/support/plugin/'. HCI_SLUG .'/reviews/?rate=5#new-post';
				$rate_title = esc_attr__('Click here to rate and review this plugin on WordPress.org', 'hey-check-it');
				$rate_text  = esc_html__('Rate this plugin', 'hey-check-it') .'&nbsp;&raquo;';
				$links[]    = '<a target="_blank" rel="noopener noreferrer" href="'. $rate_href .'" title="'. $rate_title .'">'. $rate_text .'</a>';
			}
			return $links;
		}
		
		function check_version() {
			$wp_version = get_bloginfo('version');
			if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
				if (version_compare($wp_version, HCI_REQUIRE, '<')) {
					if (is_plugin_active(HCI_FILE)) {
						deactivate_plugins(HCI_FILE);
						$msg  = '<strong>'. HCI_NAME .'</strong> '. esc_html__('requires WordPress ', 'hey-check-it') . HCI_REQUIRE;
						$msg .= esc_html__(' or higher, and has been deactivated! ', 'hey-check-it');
						$msg .= esc_html__('Please return to the', 'hey-check-it') .' <a href="'. admin_url() .'">';
						$msg .= esc_html__('WP Admin Area', 'hey-check-it') .'</a> '. esc_html__('to upgrade WordPress and try again.', 'hey-check-it');
						wp_die($msg);
					}
				}
			}
		}
		
		function load_i18n() {
			$domain = 'hey-check-it';
			$locale = apply_filters('hci_locale', get_locale(), $domain);
			$dir    = trailingslashit(WP_LANG_DIR);
			$file   = $domain .'-'. $locale .'.mo';
			$path_1 = $dir . $file;
			$path_2 = $dir . $domain .'/'. $file;
			$path_3 = $dir .'plugins/'. $file;
			$path_4 = $dir .'plugins/'. $domain .'/'. $file;
			$paths = array($path_1, $path_2, $path_3, $path_4);
			foreach ($paths as $path) {
				if ($loaded = load_textdomain($domain, $path)) {
					return $loaded;
				} else {
					return load_plugin_textdomain($domain, false, HCI_DIR .'languages/');
				}
			}
		}
		
		function admin_notices() {
			$screen = get_current_screen();
			if (!property_exists($screen, 'id')) return;
			if ($screen->id === 'settings_page_hey-check-it') {
				if (isset($_GET['gap-reset-options'])) {
					if ($_GET['gap-reset-options'] === 'true') : ?>
						<div class="notice notice-success is-dismissible"><p><strong><?php esc_html_e('Default options restored.', 'hey-check-it'); ?></strong></p></div>
					<?php else : ?>
						<div class="notice notice-info is-dismissible"><p><strong><?php esc_html_e('No changes made to options.', 'hey-check-it'); ?></strong></p></div>
					<?php endif;
				}
			}
		}
		
		function reset_options() {
			if (isset($_GET['gap-reset-options']) && wp_verify_nonce($_GET['gap-reset-options'], 'hci_reset_options')) {
				if (!current_user_can('manage_options')) exit;
				$update = update_option('hci_options', $this->default_options());
				$result = $update ? 'true' : 'false';
				$location = add_query_arg(array('gap-reset-options' => $result), admin_url(HCI_PATH));
				wp_redirect(esc_url_raw($location));
				exit;
			}
		}

		function __clone() {
			_doing_it_wrong(__FUNCTION__, esc_html__('Cheatin&rsquo; huh?', 'hey-check-it'), HCI_VERSION);
		}
		
		function __wakeup() {
			_doing_it_wrong(__FUNCTION__, esc_html__('Cheatin&rsquo; huh?', 'hey-check-it'), HCI_VERSION);
		}
		
		function default_options() {
			$options = [
				'hci_id'	=> '',
			];
			
			return apply_filters('hci_default_options', $options);
		}
		
		function validate_settings($input) {
			$input['hci_id'] = wp_filter_nohtml_kses($input['hci_id']);
			return $input;			
		}
		
		function options_locations() {
			return [
				'header' => [
					'value' => 'header',
					'label' => esc_html__('Include tracking code in page head (via', 'hey-check-it') .' <code>wp_head</code>'. esc_html__(')', 'hey-check-it')
				],
			];
		}
		
		function display_settings() {
			$hci_options = get_option('hci_options', $this->default_options());
			require_once HCI_DIR .'inc/settings-display.php';
		}
		
		function select_menu($items, $menu) {
			$options = get_option('hci_options', $this->default_options());
			$universal = isset($options['hci_universal']) ? $options['hci_universal'] : 1;
			$tracking = isset($options['hci_enable']) ? $options['hci_enable'] : 1;
			$checked = '';
			$output = '';
			$class = '';
			foreach ($items as $item) {
				$key = isset($options[$menu]) ? $options[$menu] : '';
				$value = isset($item['value']) ? $item['value'] : '';
				if ($menu === 'hci_enable') {
					if ($tracking == 0) $key = 1;
					if (!$universal && $tracking == 1) $key = 3;
					$class = ' eng-select-method';
				}
				$checked = ($value == $key) ? ' checked="checked"' : '';
				$output .= '<div class="gap-radio-inputs'. esc_attr($class) .'">';
				$output .= '<input type="radio" name="hci_options['. esc_attr($menu) .']" value="'. esc_attr($item['value']) .'"'. $checked .'> ';
				$output .= '<span>'. $item['label'] .'</span>'; //
				$output .= '</div>';
			}
			return $output;
		}
		
		function callback_reset() {
			$nonce = wp_create_nonce('hci_reset_options');
			$href  = add_query_arg(array('eng-reset-options' => $nonce), admin_url(HCI_PATH));
			$label = esc_html__('Restore default plugin options', 'hey-check-it');
			return '<a class="gap-reset-options" href="'. esc_url($href) .'">'. esc_html($label) .'</a>';
		}
		
	}

	$GLOBALS['HeyCheckIt'] = $HeyCheckIt = new HeyCheckIt(); 

	Hci_init($HeyCheckIt);
	
}
