<div class="wrap">
    <div class="wpmud">
        <div class="cp-defender">
            <div class="wdf-scanning">
                <h2 class="title">
				    <?php _e( "File Scanning", cp_defender()->domain ) ?>
                    <span><?php echo $lastScanDate == null ? null : sprintf( __( "Last scan: %s", cp_defender()->domain ), $lastScanDate ) ?>
                        <form id="start-a-scan" method="post" class="scan-frm">
						<?php
						wp_nonce_field( 'startAScan' );
						?>
                            <input type="hidden" name="action" value="startAScan"/>
                        <button type="submit"
                                class="button button-small"><?php _e( "New Scan", cp_defender()->domain ) ?></button>
                </form>
                </span>
                </h2>
            </div>
        </div>
    </div>
</div>
<dialog id="scanning" class="<?php echo cp_defender()->isFree ? 'scanning-free' : null ?>">
    <div class="line">
		<?php _e( "Defender is scanning your files for malicious code. This will take a few minutes depending on the size of your website.", cp_defender()->domain ) ?>
    </div>
    <div class="well mline">
        <div class="scan-progress">
            <div class="scan-progress-text">
                <img aria-hidden="true" src="<?php echo cp_defender()->getPluginUrl() ?>assets/img/loading.gif" width="18"
                     height="18"/>
                <span><?php echo $percent ?>%</span>
            </div>
            <div class="scan-progress-bar">
                <span style="width: <?php echo $percent ?>%"></span>
            </div>
        </div>
    </div>
    <p class="tc sub status-text scan-status"><?php echo $model->statusText ?></p>
    <form method="post" id="process-scan" class="scan-frm">
        <input type="hidden" name="action" value="processScan"/>
		<?php
		wp_nonce_field( 'processScan' );
		?>
    </form>
	<?php if ( cp_defender()->isFree == true ): ?>
        <div class="presale-text">
            <div>
				<?php printf( __( "Did you know the Pro version of Defender comes with advanced full code scanning and automated reporting?
                    Get enhanced security protection as part of a PSOURCE membership including 100+ plugins & themes, 24/7
                    support and lots of handy site management tools – <a target='_blank' href=\"%s\">Try Defender Pro today for FREE</a>", cp_defender()->domain ), \CP_Defender\Behavior\Utils::instance()->campaignURL('defender_filescanning_modal_inprogress_upsell_link') ) ?>
            </div>
        </div>
	<?php endif; ?>
</dialog>