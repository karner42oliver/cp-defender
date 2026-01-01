<div class="rule closed" id="login-duration">
    <div class="rule-title">
		<?php if ( $controller->check() == false ): ?>
            <i class="def-icon icon-warning" aria-hidden="true"></i>
		<?php else: ?>
            <i class="def-icon icon-tick" aria-hidden="true"></i>
		<?php endif; ?>
		<?php echo $controller->getTitle() ?>
    </div>
    <div class="rule-content">
        <h3><?php _e( "Overview", cp_defender()->domain ) ?></h3>
        <div class="line end">
			<?php _e( "By default, users who select the 'remember me' option stay logged in for 14 days", cp_defender()->domain ) ?>
        </div>
        <h3>
			<?php _e( "How to fix", cp_defender()->domain ) ?>
        </h3>
        <div class="well">
            <?php
                $setting = \CP_Defender\Module\Hardener\Model\Settings::instance();

                if ( $controller->check() ):
                    ?>
                    <p class="line"><?php esc_attr_e( sprintf( __('Login Duration is locked down. Current duration is %d days', cp_defender()->domain ), $controller->getService()->getDuration() ) ); ?></p>
                    <form method="post" class="hardener-frm rule-process">
                        <?php $controller->createNonceField(); ?>
                        <input type="hidden" name="action" value="processRevert"/>
                        <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                        <button class="button button-small button-grey" type="submit"><?php _e( "Revert", cp_defender()->domain ) ?></button>
                    </form>
                    <?php
                else:
                    ?>
                        <div class="line">
                            <p><?php _e( "Please change the number of days a user can stay logged in", cp_defender()->domain ) ?></p>
                        </div>
                        <form method="post" class="hardener-frm rule-process">
                            <?php $controller->createNonceField(); ?>
                            <input type="hidden" name="action" value="processHardener"/>
                            <input type="text" placeholder="<?php esc_attr_e( "Enter number of days", cp_defender()->domain ) ?>"
                                name="duration" class="block defender-login-duration" />
                            <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                            <button class="button float-r"
                                    type="submit"><?php _e( "Update", cp_defender()->domain ) ?></button>
                        </form>
                        <?php $controller->showIgnoreForm() ?>
                        <div class="clear"></div>
                    <?php
                endif;
            ?>
        </div>
    </div>
</div>