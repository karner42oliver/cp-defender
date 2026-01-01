<div class="dev-box">
    <div class="box-title">
        <h3><?php esc_html_e( "NOTIFICATIONS", cp_defender()->domain ) ?></h3>
    </div>
    <div class="box-content">
        <form method="post" id="settings-frm" class="ip-frm">
            <div class="columns">
                <div class="column is-one-third">
                    <label>
						<?php esc_html_e( "Send email notifications", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                        <?php esc_html_e( "Choose which lockout notifications you wish to be notified about. These are sent instantly.", cp_defender()->domain ) ?>
					</span>
                </div>
                <div class="column">
                    <span
                            tooltip="<?php echo esc_attr( __( "Enable Login Protection", cp_defender()->domain ) ) ?>"
                            class="toggle float-l">
                            <input type="hidden" name="login_lockout_notification" value="0"/>
                            <input type="checkbox"
                                   name="login_lockout_notification" <?php checked( 1, $settings->login_lockout_notification ) ?>
                                   value="1" class="toggle-checkbox"
                                   id="toggle_login_protection"/>
                            <label class="toggle-label" for="toggle_login_protection"></label>
                        </span>
                    <label><?php esc_html_e( "Login Protection Lockout", cp_defender()->domain ) ?></label>
                    <span class="sub inpos">
                        <?php esc_html_e( "When a user or IP is locked out for trying to access your login area.", cp_defender()->domain ) ?>
                    </span>
                    <div class="clear mline"></div>
                    <span
                            tooltip="<?php echo esc_attr( __( "Enable 404 Detection", cp_defender()->domain ) ) ?>"
                            class="toggle float-l">
                            <input type="hidden" name="ip_lockout_notification" value="0"/>
                            <input type="checkbox" name="ip_lockout_notification"
                                   value="1" <?php checked( 1, $settings->ip_lockout_notification ) ?>
                                   class="toggle-checkbox" id="toggle_404_detection"/>
                            <label class="toggle-label" for="toggle_404_detection"></label>
                        </span>
                    <label>
						<?php esc_html_e( "404 Detection Lockout", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub inpos"><?php esc_html_e( "When a user or IP is locked out for repeated hits on non-existent files.", cp_defender()->domain ) ?></span>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <label>
						<?php esc_html_e( "Email recipients", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
						<?php esc_html_e( "Choose which of your website's users will receive scan report results via email.", cp_defender()->domain ) ?>
					</span>
                </div>
                <div class="column">
					<?php
					$email_search->renderInput() ?>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <label>
						<?php esc_html_e( "Repeat Lockouts", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                        <?php esc_html_e( "If you’re getting too many emails from IPs who are repeatedly being locked out you can turn them off for a period of time.", cp_defender()->domain ) ?>
					</span>
                </div>
                <div class="column">
                    <span class="toggle float-l">
                            <input type="hidden" name="cooldown_enabled" value="0"/>
                            <input type="checkbox"
                                   name="cooldown_enabled" <?php checked( 1, $settings->cooldown_enabled ) ?>
                                   value="1" class="toggle-checkbox"
                                   id="cooldown_enabled"/>
                            <label class="toggle-label" for="cooldown_enabled"></label>
                        </span>
                    <label><?php _e( "Limit email notifications for repeat lockouts", cp_defender()->domain ) ?></label>
                    <div class="well well-white schedule-box">
                        <label><strong><?php _e( "Threshold", cp_defender()->domain ) ?></strong>
                            - <?php _e( "The number of lockouts before we turn off emails", cp_defender()->domain ) ?>
                        </label>
                        <select name="cooldown_number_lockout">
                            <option <?php selected( '1', $settings->cooldown_number_lockout ) ?> value="1">1
                            </option>
                            <option <?php selected( '3', $settings->cooldown_number_lockout ) ?> value="3">3
                            </option>
                            <option <?php selected( '5', $settings->cooldown_number_lockout ) ?> value="5">5
                            </option>
                            <option <?php selected( '10', $settings->cooldown_number_lockout ) ?> value="10">10
                            </option>
                        </select>
                        <label><strong><?php _e( "Cool Off Period", cp_defender()->domain ) ?></strong>
                            - <?php _e( "For how long should we turn them off?", cp_defender()->domain ) ?>
                        </label>
                        <select name="cooldown_period" class="mline">
                            <option <?php selected( '1', $settings->cooldown_period ) ?>
                                    value="1"><?php _e( "1 hour", cp_defender()->domain ) ?></option>
                            <option <?php selected( '2', $settings->cooldown_period ) ?>
                                    value="2"><?php _e( "2 hours", cp_defender()->domain ) ?></option>
                            <option <?php selected( '6', $settings->cooldown_period ) ?>
                                    value="6"><?php _e( "6 hours", cp_defender()->domain ) ?></option>
                            <option <?php selected( '12', $settings->cooldown_period ) ?>
                                    value="12"><?php _e( "12 hours", cp_defender()->domain ) ?></option>
                            <option <?php selected( '24', $settings->cooldown_period ) ?>
                                    value="24"><?php _e( "24 hours", cp_defender()->domain ) ?></option>
                            <option <?php selected( '36', $settings->cooldown_period ) ?>
                                    value="36"><?php _e( "36 hours", cp_defender()->domain ) ?></option>
                            <option <?php selected( '48', $settings->cooldown_period ) ?>
                                    value="48"><?php _e( "48 hours", cp_defender()->domain ) ?></option>
                            <option <?php selected( '168', $settings->cooldown_period ) ?>
                                    value="168"><?php _e( "7 days", cp_defender()->domain ) ?></option>
                            <option <?php selected( '720', $settings->cooldown_period ) ?>
                                    value="720"><?php _e( "30 days", cp_defender()->domain ) ?></option>
                        </select>
                    </div>
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