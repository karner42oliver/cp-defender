<?php
/**
 * Author: Hoang Ngo
 */

namespace CP_Defender\Behavior;

use Hammer\Base\Behavior;
use CP_Defender\Component\Error_Code;

class Blacklist_Free extends Behavior {
	public function renderBlacklistWidget() {
		$this->_renderFree();
	}

	private function _renderFree() {
		?>
        <div class="dev-box">
            <div class="box-title">
                <span class="span-icon icon-blacklist"></span>
                <h3><?php _e( "BLACKLIST MONITOR", cp_defender()->domain ) ?></h3>
                <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->campaignURL( 'defender_dash_blacklist_pro_tag' ) ?>"
                   target="_blank"
                   class="button button-small button-pre"
                   tooltip="<?php esc_attr_e( "Try Defender Pro free today", cp_defender()->domain ) ?>">
					<?php _e( "PRO FEATURE", cp_defender()->domain ) ?></a>
            </div>
            <div class="box-content">
                <div class="line">
					<?php _e( "Automatically check if you’re on Google’s blacklist every 6 hours. If something’s
                    wrong, we’ll let you know via email.", cp_defender()->domain ) ?>
                </div>
                <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->campaignURL( 'defender_dash_blacklist_upgrade_button' ) ?>"
                   target="_blank"
                   class="button button-green button-small"><?php esc_html_e( "Upgrade to Pro", cp_defender()->domain ) ?></a>
            </div>
        </div>
		<?php
	}
}