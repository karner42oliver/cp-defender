<div class="dev-box">
    <form method="post" id="settings-frm" class="ip-frm">
        <div class="box-title">
            <h3><?php esc_html_e( "404 DETECTION", cp_defender()->domain ) ?></h3>
            <div class="side float-r">
                <div>
                    <span tooltip="<?php esc_attr_e( "Deactivate 404 Detection", cp_defender()->domain ) ?>" class="toggle">
                        <input type="hidden" name="detect_404" value="0"/>
                        <input type="checkbox" checked="checked" class="toggle-checkbox"
                               id="toggle_404_detection" name="detect_404" value="1"/>
                        <label class="toggle-label" for="toggle_404_detection"></label>
                    </span>
                </div>
            </div>
        </div>
        <div class="box-content">
			<?php if ( ( $count = ( \CP_Defender\Module\IP_Lockout\Component\Login_Protection_Api::get404Lockouts( strtotime( '-24 hours', current_time( 'timestamp' ) ) ) ) ) > 0 ): ?>
                <div class="well well-yellow">
					<?php echo sprintf( __( "There have been %d lockouts in the last 24 hours. <a href=\"%s\"><strong>View log</strong></a>.", cp_defender()->domain ), $count, Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'logs' ) ) ) ?>
                </div>
			<?php else: ?>
                <div class="well well-blue">
					<?php esc_html_e( "404 Detection is enabled. There are no lockouts logged yet.", cp_defender()->domain ) ?>
                </div>
			<?php endif; ?>
            <div class="columns">
                <div class="column is-one-third">
                    <label for="detect_404_threshold">
						<?php esc_html_e( "Lockout threshold", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                                        <?php esc_html_e( "Specify how many 404 errors within a specific time period will trigger a lockout.", cp_defender()->domain ) ?>
                                    </span>
                </div>
                <div class="column">
                    <input size="8" value="<?php echo $settings->detect_404_threshold ?>" id="detect_404_threshold"
                           name="detect_404_threshold" type="text" class="inline">
                    <span class=""><?php esc_html_e( "404 errors within", cp_defender()->domain ) ?></span>&nbsp;
                    <input size="8" value="<?php echo $settings->detect_404_timeframe ?>" id="detect_404_timeframe"
                           name="detect_404_timeframe" type="text" class="inline">
                    <span class=""><?php esc_html_e( "seconds", cp_defender()->domain ) ?></span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="login_protection_lockout_timeframe">
						<?php esc_html_e( "Lockout time", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                                        <?php esc_html_e( "Choose how long you’d like to ban the locked out user for.", cp_defender()->domain ) ?>
                                    </span>
                </div>
                <div class="column">
                    <input value="<?php echo $settings->detect_404_lockout_duration ?>" size="8"
                           name="detect_404_lockout_duration"
                           id="detect_404_lockout_duration" type="text" class="inline"/>
                    <span class=""><?php esc_html_e( "seconds", cp_defender()->domain ) ?></span>
                    <div class="clearfix"></div>
                    <input type="hidden" name="detect_404_lockout_ban" value="0"/>
                    <input id="detect_404_lockout_ban" <?php checked( 1, $settings->detect_404_lockout_ban ) ?>
                           type="checkbox"
                           name="detect_404_lockout_ban" value="1">
                    <label for="detect_404_lockout_ban"
                           class="inline form-help is-marginless"><?php esc_html_e( 'Permanently ban 404 lockouts.', cp_defender()->domain ) ?></label>
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
						<textarea name="detect_404_lockout_message"
                                  id="detect_404_lockout_message"><?php echo $settings->detect_404_lockout_message ?></textarea>
                    <span class="form-help">
                                        <?php echo sprintf( __( "This message will be displayed across your website during the lockout period. See a quick preview <a href=\"%s\">here</a>.", cp_defender()->domain ), add_query_arg( array(
	                                        'def-lockout-demo' => 1,
	                                        'type'             => '404'
                                        ), network_site_url() ) ) ?>
                                    </span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="detect_404_whitelist">
						<?php esc_html_e( "Whitelist", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                                        <?php esc_html_e( "If you know a common file on your website is missing, you can record it here so it doesn't count towards a lockout record.", cp_defender()->domain ) ?>
                                    </span>
                </div>
                <div class="column">
					<textarea id="detect_404_whitelist" name="detect_404_whitelist"
                              rows="8"><?php echo $settings->detect_404_whitelist ?></textarea>
                    <span class="form-help">
                                        <?php esc_html_e( "You must list the full path beginning with a /.", cp_defender()->domain ) ?>
                                    </span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="detect_404_ignored_filetypes">
						<?php esc_html_e( "Ignore file types", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                                        <?php esc_html_e( "Choose which types of files you want to log errors for but not trigger a lockout.", cp_defender()->domain ) ?>
                                    </span>
                </div>
                <div class="column">
					<textarea id="detect_404_ignored_filetypes" name="detect_404_ignored_filetypes"
                              rows="8"><?php echo $settings->detect_404_ignored_filetypes ?></textarea>
                    <span class="form-help">
                                        <?php esc_html_e( "Defender will log the 404 error, but won’t lockout the user for these filetypes.", cp_defender()->domain ) ?>
                                    </span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label>
						<?php esc_html_e( "Exclusions", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                                        <?php esc_html_e( "By default, Defender will monitor all interactions with your website but you can choose to disable 404 detection for specific areas of your site.", cp_defender()->domain ) ?>
                                    </span>
                </div>
                <div class="column">
                    <input type="hidden" name="detect_404_logged" value="0"/>
                    <input id="detect_404_logged" <?php checked( 1, $settings->detect_404_logged ) ?>
                           type="checkbox"
                           name="detect_404_logged" value="1">
                    <label for="detect_404_logged"
                           class="inline form-help is-marginless"><?php esc_html_e( 'Monitor 404s from logged in users', cp_defender()->domain ) ?></label>
                </div>
            </div>
            <div class="clear line"></div>
			<?php wp_nonce_field( 'saveLockoutSettings' ) ?>
            <input type="hidden" name="action" value="saveLockoutSettings"/>
            <button type="submit" class="button button-primary float-r">
				<?php esc_html_e( "UPDATE SETTINGS", cp_defender()->domain ) ?>
            </button>
            <div class="clear"></div>
        </div>
    </form>
</div>