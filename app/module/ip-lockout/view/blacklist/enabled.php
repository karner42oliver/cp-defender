<div class="dev-box">
    <div class="box-title">
        <h3><?php _e( "IP Banning", cp_defender()->domain ) ?></h3>
    </div>
    <div class="box-content">
        <form method="post" id="settings-frm" class="ip-frm">
            <p class="intro">
				<?php _e( "Choose which IP addresses you wish to permanently ban from accessing your website.", cp_defender()->domain ) ?>
            </p>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="ip_blacklist">
						<?php _e( "Blacklist", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
						<?php _e( "Any IP addresses you list here will be completely blocked from accessing our website, including admins.", cp_defender()->domain ) ?>
					</span>
                </div>
                <div class="column">
					<textarea name="ip_blacklist" id="ip_blacklist"
                              rows="8"><?php echo $settings->ip_blacklist ?></textarea>
                    <span class="form-help">
						<?php _e( "One IP address per line and IPv4 format only. IP ranges are accepted in format xxx.xxx.xxx.xxx-xxx.xxx.xxx.xxx", cp_defender()->domain ) ?>
					</span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="detect_404_lockout_message">
						<?php esc_html_e( "Lockout message", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                                        <?php esc_html_e( "Customize the message locked out users will see.", cp_defender()->domain ) ?>
                                    </span>
                </div>
                <div class="column">
						<textarea name="ip_lockout_message"
                                  id="ip_lockout_message"><?php echo $settings->ip_lockout_message ?></textarea>
                    <span class="form-help">
                                         <?php echo sprintf( __( "This message will be displayed across your website for any IP matching your blacklist. See a quick preview <a href=\"%s\">here</a>.", cp_defender()->domain ), add_query_arg( array(
	                                         'def-lockout-demo' => 1,
	                                         'type'             => 'blacklist'
                                         ), network_site_url() ) ) ?>
                                    </span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="ip_whitelist">
						<?php _e( "Whitelist", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
						<?php _e( "Any IP addresses you list here will be exempt from the options you've selected for Login Protect and 404 Detection.", cp_defender()->domain ) ?>
					</span>
                </div>
                <div class="column">
					<textarea name="ip_whitelist" id="ip_whitelist"
                              rows="8"><?php echo $settings->ip_whitelist ?></textarea>
                    <span class="form-help">
						<?php _e( "One IP address per line and IPv4 format only. IP ranges are accepted in format xxx.xxx.xxx.xxx-xxx.xxx.xxx.xxx", cp_defender()->domain ) ?>
					</span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="import">
						<?php _e( "Import", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
						<?php _e( "Import your blacklist and whitelist from another website (CSV file).", cp_defender()->domain ) ?>
					</span>
                </div>
                <div class="column">
                    <div class="upload-input">
                        <input disabled="disabled" type="text" id="import">
                        <input type="hidden" name="file_import" id="file_import">
                        <button type="button" class="button button-light file-picker">
							<?php _e( "Select", cp_defender()->domain ) ?></button>
                        <button type="button" class="button button-grey btn-import-ip">
							<?php _e( "Import", cp_defender()->domain ) ?>
                        </button>
                    </div>
                    <span class="form-help">
                        <?php _e( "Upload your exported blacklist. Note: Existing IP addresses will not be removed, only new IP addresses added.", cp_defender()->domain ) ?>
                    </span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="import">
						<?php _e( "Export", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
						<?php _e( "Export both your blacklist and whitelist as a CSV file to use on another website.", cp_defender()->domain ) ?>
					</span>
                </div>
                <div class="column">
                    <p>
                        <a href="<?php echo Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'export', '_wpnonce' => wp_create_nonce( 'defipexport' ) ) ) ?>"
                           class="button button-secondary export">
							<?php _e( "Export", cp_defender()->domain ) ?></a>
                    </p>
                </div>
            </div>
            <div class="clear line"></div>
			<?php wp_nonce_field( 'saveLockoutSettings' ) ?>
            <input type="hidden" name="action" value="saveLockoutSettings"/>
            <button type="submit" class="button button-primary float-r">
				<?php esc_html_e( "UPDATE SETTINGS", cp_defender()->domain ) ?>
            </button>
            <div class="clear"></div>
        </form>
    </div>
</div>