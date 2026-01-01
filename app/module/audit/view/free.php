<div class="wrap">
    <div class="cp-defender">
        <div class="auditing">
            <h2 class="title">
				<?php _e( "AUDIT LOGGING", cp_defender()->domain ) ?>
            </h2>
            <div class="dev-box">
                <div class="box-title">
                    <h3><?php _e( "Upgrade", cp_defender()->domain ) ?></h3>
                </div>
                <div class="box-content tc">
                    <img class="mline" src="<?php echo cp_defender()->getPluginUrl() ?>assets/img/audit-free.svg"/>
                    <div class="line max-600">
				        <?php _e( "Track and log each and every event when changes are made to your website and get details reports on everything from what your users are doing to hacking attempts. This is a pro feature that requires an active PSOURCE membership. Try it free today!", cp_defender()->domain ) ?>
                    </div>
                    <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->campaignURL('defender_auditlogging_upgrade_button') ?>" target="_blank"
                       class="button button-green"><?php esc_html_e( "Upgrade to Pro", cp_defender()->domain ) ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $controller->renderPartial('pro-feature') ?>