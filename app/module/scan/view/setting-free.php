<div class="dev-box">
    <div class="box-title">
        <h3><?php _e( "Settings", cp_defender()->domain ) ?></h3>
    </div>
    <div class="box-content">
        <form method="post" class="scan-frm scan-settings">
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "Scan Types", cp_defender()->domain ) ?></strong>
                    <span class="sub">
                        <?php _e( "Choose the scan types you would like to include in your default scan. It's recommended you enable all types.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <span class="toggle">
                        <input type="hidden" name="scan_core" value="0"/>
                        <input type="checkbox" name="scan_core" class="toggle-checkbox" id="core-scan" value="1"
	                        <?php checked( true, $setting->scan_core ) ?>/>
                        <label class="toggle-label" for="core-scan"></label>
                    </span>
                    <label for="core-scan"><?php _e( "WordPress Core", cp_defender()->domain ) ?></label>
                    <span class="sub inpos">
                        <?php _e( "Defender checks for any modifications or additions to WordPress core files.", cp_defender()->domain ) ?>
                    </span>
                    <div class="clear mline"></div>
                    <div class="feature-pre-require">
                        <div></div>
                        <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->campaignURL('defender_filescanning_settings_pro_tag') ?>" role="button" target="_blank" class="button button-small button-pre"
						tooltip="<?php esc_attr_e( "Try Defender Pro free today", cp_defender()->domain ) ?>" >
						<?php _e( "Pro Feature", cp_defender()->domain ) ?></a>
                        <span class="toggle" aria-hidden="true" role="presentation">
                        	<input role="presentation" type="checkbox" class="toggle-checkbox" value="1" id="scan-vuln"/>
                        	<label class="toggle-label" aria-hidden="true" for="scan-vuln"></label>
                        </span>
                        <label for="scan-vuln"><?php _e( "Plugins & Themes", cp_defender()->domain ) ?></label>
                        <span class="sub inpos">
                        <?php _e( "Defender looks for publicly reported vulnerabilities in your installed plugins and themes.", cp_defender()->domain ) ?>
                    </span>
                    </div>
                    <div class="clear mline"></div>
                    <div class="feature-pre-require">
                        <div></div>
                        <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->campaignURL('defender_filescanning_settings_pro_tag') ?>" role="button" target="_blank" class="button button-small button-pre"
						tooltip="<?php esc_attr_e( "Try Defender Pro free today", cp_defender()->domain ) ?>">
							<?php _e( "Pro Feature", cp_defender()->domain ) ?></a>
                        <span class="toggle" aria-hidden="true" role="presentation">
							<input role="presentation" type="checkbox" class="toggle-checkbox" value="1" id="scan-content"/>
							<label class="toggle-label" aria-hidden="true" for="scan-content"></label>
                   		 </span>
                        <label for="scan-content"><?php _e( "Suspicious Code", cp_defender()->domain ) ?></label>
                        <span class="sub inpos">
                        <?php _e( "Defender looks inside all of your files for suspicious and potentially harmful code.", cp_defender()->domain ) ?>
                    </span>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="presale-text">
                    <div>
			            <?php printf( __( "Defenders scans through every line of code on your website, searching for anything suspicious. This feature is included when you join PSOURCE, along with 100+ plugins and themes, 24/7 support and lots of handy site management tools.  – <a href=\"%s\" target='_blank' role='button'>Try it all FREE today!
                        </a>", cp_defender()->domain ), \CP_Defender\Behavior\Utils::instance()->campaignURL('defender_filescanning_settings_upsell_link') ) ?>
                    </div>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "Maximum included file size", cp_defender()->domain ) ?></strong>
                    <span class="sub">
                        <?php _e( "Defender will skip any files larger than this size. The smaller the number, the faster Defender will scan your website.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <input type="text" size="4" value="<?php echo esc_attr( $setting->max_filesize ) ?>"
                           name="max_filesize"> <?php _e( "MB", cp_defender()->domain ) ?>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "Optional emails", cp_defender()->domain ) ?></strong>
                    <span class="sub">
                        <?php _e( "By default, you'll only get email reports when your site runs into trouble. Turn this option on to get reports even when your site is running smoothly.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <span class="toggle" aria-hidden="true" role="presentation">
                        <input type="hidden" name="always_send" value="0"/>
                        <input type="checkbox" role="presentation" name="always_send" class="toggle-checkbox" value="1"
                               id="always_send" <?php checked( true, $setting->always_send ) ?>/>
                        <label class="toggle-label" aria-hidden="true" for="always_send"></label>
                    </span>
                    <label><?php _e( "Send all scan report emails", cp_defender()->domain ) ?></label>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "Email subject", cp_defender()->domain ) ?></strong>
                </div>
                <div class="column">
                    <input type="text" name="email_subject" value="<?php echo esc_attr( $setting->email_subject ) ?>"/>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "Email templates", cp_defender()->domain ) ?></strong>
                    <span class="sub">
                        <?php _e( "When Defender scans your website, a report will be generated with any issues that have been found. You can choose to have reports emailed to you.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <ul class="dev-list">
                        <li>
                            <div>
                                <span class="list-label"><?php _e( "When an issue is found", cp_defender()->domain ) ?></span>
                                <span class="list-detail tr">
                                    <a href="#issue-found" rel="dialog" role='button'><?php _e( "Edit", cp_defender()->domain ) ?></a></span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span class="list-label"><?php _e( "When no issues are found", cp_defender()->domain ) ?></span>
                                <span class="list-detail tr">
                                    <a href="#all-ok"
                                       rel="dialog" role='button'><?php _e( "Edit", cp_defender()->domain ) ?></a></span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="clear line"></div>
            <input type="hidden" name="action" value="saveScanSettings"/>
			<?php wp_nonce_field( 'saveScanSettings' ) ?>
            <button class="button float-r"><?php _e( "Update Settings", cp_defender()->domain ) ?></button>
            <div class="clear"></div>
        </form>
    </div>
</div>
<dialog id="issue-found" title="<?php esc_attr_e( "Issues found", cp_defender()->domain ) ?>">
    <form method="post" class="scan-frm scan-settings">
        <textarea rows="12" name="email_has_issue"><?php echo $setting->email_has_issue ?></textarea>
        <input type="hidden" name="action" value="saveScanSettings"/>
		<?php wp_nonce_field( 'saveScanSettings' ) ?>
        <button class="button"><?php _e( "Save", cp_defender()->domain ) ?></button>
    </form>
</dialog>
<dialog id="all-ok" title="<?php esc_attr_e( 'All OK', cp_defender()->domain ) ?>">
    <form method="post" class="scan-frm scan-settings">
        <input type="hidden" name="action" value="saveScanSettings"/>
		<?php wp_nonce_field( 'saveScanSettings' ) ?>
        <textarea rows="12" name="email_all_ok"><?php echo $setting->email_all_ok ?></textarea>
        <button class="button"><?php _e( "Save", cp_defender()->domain ) ?></button>
    </form>
</dialog>