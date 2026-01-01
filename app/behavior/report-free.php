<?php
/**
 * Author: Hoang Ngo
 */

namespace CP_Defender\Behavior;

use Hammer\Base\Behavior;
use CP_Defender\Module\Scan\Model\Settings;

class Report_Free extends Behavior {
	public function renderReportWidget() {
		?>
        <div class="dev-box reporting-sale">
            <div class="box-title">
                <span class="span-icon icon-report"></span>
                <h3><?php _e( "REPORTING", cp_defender()->domain ) ?></h3>
            </div>
            <div class="box-content">
                <div class="line">
					<?php _e( "Get tailored security reports delivered to your inbox so you don’t have to worry
                    about checking in.", cp_defender()->domain ) ?>
                </div>
                <div class="row is_multiline">
                    <div class="col-half">
                        <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-scan', array( 'view' => 'reporting' ) ) ?>">
                            <div class="report-status with-corner">
                                <img src="<?php echo cp_defender()->getPluginUrl() ?>assets/img/scanning-pre.svg">
                                <strong><?php _e( "FILE SCANNING", cp_defender()->domain ) ?></strong>
                                <div class="corner">
                                    Pro
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-half">
                        <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-logging', array( 'view' => 'report' ) ) ?>">
                            <div class="report-status with-corner">
                                <img src="<?php echo cp_defender()->getPluginUrl() ?>assets/img/audit-pre.svg">
                                <strong><?php _e( "AUDIT LOGGING", cp_defender()->domain ) ?></strong>
                                <div class="corner">
                                    Pro
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-half">
                        <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'reporting' ) ) ?>">
                            <div class="report-status with-corner">
                                <img src="<?php echo cp_defender()->getPluginUrl() ?>assets/img/lockout-pre.svg">
                                <strong><?php _e( "IP LOCKOUTS", cp_defender()->domain ) ?></strong>
                                <div class="corner">
                                    Pro
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="presale-text">
                    <div>
						<?php printf( __( " Automated reports are included in a PSOURCE membership along with 100+ plugins
                        & themes, 24/7 support and lots of handy site management tools – <a target='_blank' href=\"%s\">Try
                            it all absolutely FREE</a>", cp_defender()->domain ), \CP_Defender\Behavior\Utils::instance()->campaignURL( 'defender_dash_reports_upsell_link' ) ) ?>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
		<?php
	}

	/**
	 * @return null|string
	 */
	private function getScanToolTip() {
		$isPre    = \CP_Defender\Behavior\Utils::instance()->getAPIKey();
		$settings = Settings::instance();
		$active   = $settings->notification;
		if ( ! $isPre || ! $active ) {
			return null;
		}

		$toolstip = sprintf( __( "Scan reports are active scheduled to send %s", cp_defender()->domain ),
			$settings->frequency == 1 ? $this->frequencyToText( $settings->frequency ) . '/' . strftime( '%I:%M %p', strtotime( $settings->time ) ) : $this->frequencyToText( $settings->frequency ) . '/' . $settings->day . '/' . strftime( '%I:%M %p', strtotime( $settings->time ) ) );
		$toolstip = strlen( $toolstip ) ? ' tooltip="' . esc_attr( $toolstip ) . '" ' : null;

		return $toolstip;
	}

	private function getAuditToolTip() {
		$isPre    = \CP_Defender\Behavior\Utils::instance()->getAPIKey();
		$settings = \CP_Defender\Module\Audit\Model\Settings::instance();
		$active   = $settings->notification;
		if ( ! $isPre || ! $active ) {
			return null;
		}

		$toolstip = sprintf( __( "Audit reports are active scheduled to send %s", cp_defender()->domain ),
			$settings->frequency == 1 ? $this->frequencyToText( $settings->frequency ) . '/' . strftime( '%I:%M %p', strtotime( $settings->time ) ) : $this->frequencyToText( $settings->frequency ) . '/' . $settings->day . '/' . strftime( '%I:%M %p', strtotime( $settings->time ) ) );
		$toolstip = strlen( $toolstip ) ? ' tooltip="' . esc_attr( $toolstip ) . '" ' : null;

		return $toolstip;
	}

	private function getLockoutTooltips() {
		$isPre    = \CP_Defender\Behavior\Utils::instance()->getAPIKey();
		$settings = \CP_Defender\Module\IP_Lockout\Model\Settings::instance();
		$active   = $settings->report;
		if ( ! $isPre || ! $active ) {
			return null;
		}

		$toolstip = sprintf( __( "Lockout reports are active scheduled to send %s", cp_defender()->domain ),
			$settings->report_frequency == 1 ? $this->frequencyToText( $settings->report_frequency ) . '/' . strftime( '%I:%M %p', strtotime( $settings->report_time ) ) : $this->frequencyToText( $settings->report_frequency ) . '/' . $settings->report_day . '/' . strftime( '%I:%M %p', strtotime( $settings->report_time ) ) );
		$toolstip = strlen( $toolstip ) ? ' tooltip="' . esc_attr( $toolstip ) . '" ' : null;

		return $toolstip;
	}

	/**
	 * @param $freq
	 *
	 * @return string
	 */
	private function frequencyToText( $freq ) {
		$text = '';
		switch ( $freq ) {
			case 1:
				$text = __( "daily", cp_defender()->domain );
				break;
			case 7:
				$text = __( "weekly", cp_defender()->domain );
				break;
			case 30:
				$text = __( "monthly", cp_defender()->domain );
				break;
		}

		return $text;
	}
}