<?php if (!function_exists('add_action')) die(); ?>

<div class="wrap">	
	<h1><?php echo HCI_NAME; ?> <small><?php echo 'v'. HCI_VERSION; ?></small></h1>
	<form method="post" action="options.php">
		<?php settings_fields('hci_plugin_options'); ?>
		<div class="metabox-holder">
			<div class="meta-box-sortables ui-sortable">
				<div class="postbox">	
					<h2><?php esc_html_e('Plugin Settings', 'hey-check-it'); ?></h2>
					<div class="toggle">
						<div class="eng-panel-settings">
							<table class="widefat">
								<tr>
									<th>
										<label for="hci_options[hci_id]">
											<?php esc_html_e('Verification Code', 'hey-check-it') ?>
										</label>
									</th>
									<td>
										<input id="hci_options[hci_id]" name="hci_options[hci_id]" type="text" size="40" maxlength="36" value="<?php if (isset($hci_options['hci_id'])) echo esc_attr($hci_options['hci_id']); ?>">
									</td>
								</tr>
							</table>
							<p>
								<?php esc_html_e('If you like this plugin, please', 'hey-check-it'); ?> 
								<a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/support/plugin/hey-check-it/reviews/?rate=5#new-post" title="<?php esc_attr_e('THANK YOU for your support!', 'hey-check-it'); ?>">
									<?php esc_html_e('give it a 5-star rating', 'hey-check-it'); ?>&nbsp;&raquo;
								</a>
							</p>
						</div>
						<input type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'hey-check-it'); ?>" />
					</div>
				</div>
			</div>
		</div>
		<div class="eng-credit-info">
			<a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url(HCI_HOME); ?>" title="<?php esc_attr_e('Plugin Homepage', 'hey-check-it'); ?>"><?php echo HCI_NAME; ?></a> 
		</div>
	</form>
</div>