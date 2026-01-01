<?php
/**
 * Author: Hoang Ngo
 */

namespace CP_Defender\Module\Audit\Controller;

use Hammer\Helper\HTTP_Helper;
use Hammer\Helper\Log_Helper;
use Hammer\Helper\WP_Helper;
use CP_Defender\Behavior\Utils;
use CP_Defender\Module\Audit\Behavior\Audit;
use CP_Defender\Module\Audit\Component\Audit_API;
use CP_Defender\Module\Audit\Component\Audit_Table;
use CP_Defender\Module\Audit\Model\Settings;
use CP_Defender\Vendor\Email_Search;

class Main extends \CP_Defender\Controller {
	protected $slug = 'wdf-logging';
	public $layout = 'layout';
	public $email_search;

	/**
	 * @return array
	 */
	public function behaviors() {
		return array(
			'utils' => '\CP_Defender\Behavior\Utils'
		);
	}

	public function __construct() {
		if ( $this->is_network_activate( cp_defender()->plugin_slug ) ) {
			$this->add_action( 'network_admin_menu', 'adminMenu' );
		} else {
			$this->add_action( 'admin_menu', 'adminMenu' );
		}

		if ( $this->isInPage() || $this->isDashboard() ) {
			$this->add_action( 'defender_enqueue_assets', 'scripts', 11 );
		}

		$this->add_ajax_action( 'activeAudit', 'activeAudit' );
		$this->add_ajax_action( 'auditLoadLogs', 'auditLoadLogs' );
		$this->add_ajax_action( 'saveAuditSettings', 'saveAuditSettings' );
		$this->add_ajax_action( 'auditOnCloud', 'auditOnCloud', true, true );
		$this->add_ajax_action( 'dashboardSummary', 'dashboardSummary' );
		$this->add_ajax_action( 'exportAsCvs', 'exportAsCvs' );

		if ( Settings::instance()->enabled == 1 ) {
			$this->add_action( 'wp_loaded', 'setupEvents', 1 );
			$this->add_action( 'shutdown', 'triggerEventSubmit' );
		}
		$this->email_search = new Email_Search();
		if ( ( HTTP_Helper::retrieve_get( 'view' ) == ''
		       && HTTP_Helper::retrieve_get( 'page' ) == 'wdf-logging' )
		     || ( ( defined( 'DOING_AJAX' ) && DOING_AJAX == true )
		          && HTTP_Helper::retrieve_post( 'id' ) == 'audit_lite' )
		) {
			//load the lite version of user search on main page & when using ajax, for using the
			//ajax hooks
			$this->email_search->lite      = true;
			$this->email_search->eId       = 'audit_lite';
			$this->email_search->noExclude = true;
		} else {
			$this->email_search->eId = 'audit';
		}
		$this->email_search->settings = Settings::instance();
		$this->email_search->add_hooks();
		//report cron
		$this->add_action( 'auditReportCron', 'auditReportCron' );
	}

	public function exportAsCvs() {
		if ( ! $this->checkPermission() ) {
			return;
		}

		$params  = $this->prepareAuditParams();
		$data    = Audit_API::pullLogs( $params, 'timestamp', 'desc', true );
		$logs    = $data['data'];
		$fp      = fopen( 'php://memory', 'w' );
		$headers = array(
			__( "Summary", cp_defender()->domain ),
			__( "Date / Time", cp_defender()->domain ),
			__( "Context", cp_defender()->domain ),
			__( "Type", cp_defender()->domain ),
			__( "IP address", cp_defender()->domain ),
			__( "User", cp_defender()->domain )
		);
		fputcsv( $fp, $headers );
		foreach ( $logs as $fields ) {
			$vars = array(
				$fields['msg'],
				is_array( $fields['timestamp'] )
					? $this->formatDateTime( date( 'Y-m-d H:i:s', $fields['timestamp'][0] ) )
					: $this->formatDateTime( date( 'Y-m-d H:i:s', $fields['timestamp'] ) ),
				ucwords( Audit_API::get_action_text( $fields['context'] ) ),
				ucwords( Audit_API::get_action_text( $fields['action_type'] ) ),
				$fields['ip'],
				$this->getDisplayName( $fields['user_id'] )
			);
			fputcsv( $fp, $vars );
		}
		$filename = 'wdf-audit-logs-export-' . date( 'ymdHis' ) . '.csv';
		fseek( $fp, 0 );
		header( 'Content-Type: text/csv' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '";' );
		// make php send the generated csv lines to the browser
		fpassthru( $fp );
		exit();
	}

	public function dashboardSummary() {
		if ( ! $this->checkPermission() ) {
			return;
		}

		if ( ! wp_verify_nonce( HTTP_Helper::retrieve_post( '_wpnonce' ), 'dashboardSummary' ) ) {
			return;
		}

		if ( HTTP_Helper::retrieve_post( 'weekly' ) == '1' ) {
			$weekCount = Audit_API::pullLogs( array(
				'date_from' => date( 'Y-m-d', strtotime( '-7 days' ) ) . ' 00:00:00',
				'date_to'   => date( 'Y-m-d' ) . ' 23:59:59'
			) );
			wp_send_json_success( array(
				'eventWeek' => is_wp_error( $weekCount ) ? '-' : $weekCount['total_items']
			) );
		}

		$eventsInMonth = Audit_API::pullLogs( array(
			'date_from' => date( 'Y-m-d', strtotime( 'first day of this month', current_time( 'timestamp' ) ) ) . ' 00:00:00',
			'date_to'   => date( 'Y-m-d' ) . ' 23:59:59'
		) );

		if ( is_wp_error( $eventsInMonth ) ) {
			wp_send_json_error( array(
				'message' => $eventsInMonth->get_error_message()
			) );
		}

		$lastEventDate   = __( "Never", cp_defender()->domain );
		$dailyEventCount = 0;

		if ( $eventsInMonth['total_items'] > 0 ) {
			$request = Audit_API::pullLogsSummary();
			if ( is_wp_error( $request ) ) {
				wp_send_json_error( array(
					'message' => $request->get_error_message()
				) );
			}
			$dailyEventCount = $request['count'];
			$lastEventDate   = $eventsInMonth['data'][0]['timestamp'];
			if ( is_array( $lastEventDate ) ) {
				$lastEventDate = $lastEventDate[0];
			}
			$lastEventDate = $this->formatDateTime( date( 'Y-m-d H:i:s', $lastEventDate ) );
		}
		$content = $this->renderPartial( 'widget', array(
			'eventMonth' => $eventsInMonth['total_items'],
			'eventDay'   => $dailyEventCount,
			'lastEvent'  => $lastEventDate
		), false );

		wp_send_json_success( array(
			'html' => $content
		) );
	}

	public function sort_email_data( $a, $b ) {
		return $a['count'] < $b['count'];
	}

	/**
	 * process scan settings
	 */
	public function saveAuditSettings() {
		if ( ! $this->checkPermission() ) {
			return;
		}

		if ( ! wp_verify_nonce( HTTP_Helper::retrieve_post( '_wpnonce' ), 'saveAuditSettings' ) ) {
			return;
		}

		$settings = Settings::instance();
		$data     = array_map( 'sanitize_text_field', $_POST );
		$settings->import( $data );
		$settings->save();
		$cronTime = $this->reportCronTimestamp( $settings->time, 'auditReportCron' );
		if ( $settings->notification == true ) {
			wp_schedule_event( $cronTime, 'daily', 'auditReportCron' );
		}
		$res = array(
			'message' => __( "Your settings have been updated.", cp_defender()->domain )
		);

		if ( $settings->notification == true ) {
			$res['notification'] = 1;
			$res['frequency']    = ucfirst( \CP_Defender\Behavior\Utils::instance()->frequencyToText( $settings->frequency ) );
			if ( $settings->frequency == 1 ) {
				$res['schedule'] = sprintf( __( "at %s", cp_defender()->domain ), strftime( '%I:%M %p', strtotime( $settings->time ) ) );
			} else {
				$res['schedule'] = sprintf( __( "%s at %s", cp_defender()->domain ), ucfirst( $settings->day ), strftime( '%I:%M %p', strtotime( $settings->time ) ) );
			}
		} else {
			$res['notification'] = 0;
			$res['text']         = '-';
		}
		if ( $settings->enabled == 0 ) {
			$res['reload'] = 1;
		}
		Utils::instance()->submitStatsToDev();
		wp_send_json_success( $res );
	}

	/**
	 * Ajax for loading audit table html
	 */
	public function auditLoadLogs() {
		if ( ! $this->checkPermission() ) {
			return;
		}

		$lite = HTTP_Helper::retrieve_get( 'lite', false );
		if ( $lite == 1 ) {

		} else {
			$params = $this->prepareAuditParams();
			$data   = Audit_API::pullLogs( $params );
			$table  = $this->_renderTable( $data );
			wp_send_json_success( array(
				'html'  => $table,
				'count' => is_array( $data ) ? $data['total_items'] : 0
			) );
		}
	}

	/**
	 * hook all the action for listening on events
	 */
	public function setupEvents() {
		Audit_API::setupEvents();
	}

	public function triggerEventSubmit() {
		$data = WP_Helper::getArrayCache()->get( 'events_queue', array() );
		if ( is_array( $data ) && count( $data ) ) {
			Audit_API::onCloud( $data );
		}
	}

	/**
	 * Sending report email by cron
	 */
	public function auditReportCron() {
		if ( cp_defender()->isFree ) {
			return;
		}

		$settings = Settings::instance();

		if ( $settings->notification == false ) {
			return;
		}

		$lastReportSent = $settings->lastReportSent;
		if ( $lastReportSent == null ) {
			//no sent, so just assume last 30 days, as this only for monthly
			$lastReportSent = strtotime( '-31 days', current_time( 'timestamp' ) );
		}

		if ( ! $this->isReportTime( $settings->frequency, $settings->day, $lastReportSent ) ) {
			return false;
		}

		switch ( $settings->frequency ) {
			case 1:
				$date_from = strtotime( '-24 hours' );
				$date_to   = time();
				break;
			case 7:
				$date_from = strtotime( '-7 days' );
				$date_to   = time();
				break;
			case 30:
				$date_from = strtotime( '-30 days' );
				$date_to   = time();
				break;
		}

		if ( ! isset( $date_from ) && ! isset( $date_to ) ) {
			//something wrong
			return;
		}

		$date_from = date( 'Y-m-d', $date_from );
		$date_to   = date( 'Y-m-d', $date_to );

		$logs = Audit_API::pullLogs( array(
			'date_from' => $date_from . ' 0:00:00',
			'date_to'   => $date_to . ' 23:59:59',
			//no paging
			'paged'     => - 1,
			//'no_group_item' => 1
		) );

		$data       = $logs['data'];
		$email_data = array();
		foreach ( $data as $row => $val ) {
			if ( ! isset( $email_data[ $val['event_type'] ] ) ) {
				$email_data[ $val['event_type'] ] = array(
					'count' => 0
				);
			}

			if ( ! isset( $email_data[ $val['event_type'] ][ $val['action_type'] ] ) ) {
				$email_data[ $val['event_type'] ][ $val['action_type'] ] = 1;
			} else {
				$email_data[ $val['event_type'] ][ $val['action_type'] ] += 1;
			}
			$email_data[ $val['event_type'] ]['count'] += 1;
		}

		uasort( $email_data, array( &$this, 'sort_email_data' ) );

		//now we create a table
		if ( count( $email_data ) ) {
			ob_start();
			?>
            <table class="wrapper main" align="center"
                   style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                <tbody>
                <tr style="padding: 0; text-align: left; vertical-align: top;">
                    <td class="wrapper-inner main-inner"
                        style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #555555; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: normal; hyphens: auto; line-height: 26px; margin: 0; padding: 40px; text-align: left; vertical-align: top; word-wrap: break-word;">

                        <table class="main-intro"
                               style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top;">
                            <tbody>
                            <tr style="padding: 0; text-align: left; vertical-align: top;">
                                <td class="main-intro-content"
                                    style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #555555; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: normal; hyphens: auto; line-height: 26px; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                                    <h3 style="Margin: 0; Margin-bottom: 0; color: #555555; font-family: Helvetica, Arial, sans-serif; font-size: 32px; font-weight: normal; line-height: 32px; margin: 0; margin-bottom: 0; padding: 0 0 28px; text-align: left; word-wrap: normal;"><?php _e( "Hi {USER_NAME},", cp_defender()->domain ) ?></h3>
                                    <p style="Margin: 0; Margin-bottom: 0; color: #555555; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: normal; line-height: 26px; margin: 0; margin-bottom: 0; padding: 0 0 24px; text-align: left;">
										<?php printf( __( "It’s WP Defender here, reporting from the frontline with a quick update on what’s been happening at <a href=\"%s\">%s</a>.", cp_defender()->domain ), site_url(), site_url() ) ?></p>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="results-list"
                               style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top;">
                            <thead class="results-list-header" style="border-bottom: 2px solid #ff5c28;">
                            <tr style="padding: 0; text-align: left; vertical-align: top;">
                                <th class="result-list-label-title"
                                    style="Margin: 0; color: #ff5c28; font-family: Helvetica, Arial, sans-serif; font-size: 22px; font-weight: 700; line-height: 48px; margin: 0; padding: 0; text-align: left; width: 35%;">
									<?php _e( "Event Type", cp_defender()->domain ) ?>
                                </th>
                                <th class="result-list-data-title"
                                    style="Margin: 0; color: #ff5c28; font-family: Helvetica, Arial, sans-serif; font-size: 22px; font-weight: 700; line-height: 48px; margin: 0; padding: 0; text-align: left;">
									<?php _e( "Action Summaries", cp_defender()->domain ) ?>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="results-list-content">
							<?php $count = 0; ?>
							<?php foreach ( $email_data as $key => $row ): ?>
                                <tr style="padding: 0; text-align: left; vertical-align: top;">
									<?php if ( $count == 0 ) {
										$style = '-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #555555; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: 700; hyphens: auto; line-height: 28px; margin: 0; padding: 20px 5px; text-align: left; vertical-align: top; word-wrap: break-word;';
									} else {
										$style = '-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; border-top: 2px solid #ff5c28; color: #555555; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: 700; hyphens: auto; line-height: 28px; margin: 0; padding: 20px 5px; text-align: left; vertical-align: top; word-wrap: break-word;';
									} ?>
                                    <td class="result-list-label bordered"
                                        style="<?php echo $style ?>">
										<?php echo ucfirst( Audit_API::get_action_text( strtolower( $key ) ) ) ?>
                                    </td>
                                    <td class="result-list-data bordered"
                                        style="<?php echo $style ?>">
										<?php foreach ( $row as $i => $v ): ?>
											<?php if ( $i == 'count' ) {
												continue;
											} ?>
                                            <span
                                                    style="display: inline-block; font-weight: 400; width: 100%;">
												<?php echo ucwords( Audit_API::get_action_text( strtolower( $i ) ) ) ?>
                                                : <?php echo $v ?>
											</span>
										<?php endforeach; ?>
                                    </td>
                                </tr>
								<?php $count ++; ?>
							<?php endforeach; ?>
                            </tbody>
                            <tfoot class="results-list-footer">
                            <tr style="padding: 0; text-align: left; vertical-align: top;">
                                <td colspan="2"
                                    style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #555555; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: normal; hyphens: auto; line-height: 26px; margin: 0; padding: 10px 0 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                                    <p style="Margin: 0; Margin-bottom: 0; color: #555555; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: normal; line-height: 26px; margin: 0; margin-bottom: 0; padding: 0 0 24px; text-align: left;">
                                        <a class="plugin-brand"
                                           href="<?php echo Utils::instance()->getAdminPageUrl( 'wdf-logging', array(
                                                           'date_from' => date( 'm/d/Y', strtotime( $date_from ) ),
                                                           'date_to' => date( 'm/d/Y', strtotime( $date_to ) )
                                               ) ) ?>"
