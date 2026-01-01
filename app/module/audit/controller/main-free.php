<?php
/**
 * Author: Hoang Ngo
 */

namespace CP_Defender\Module\Audit\Controller;

use Hammer\Helper\HTTP_Helper;
use Hammer\Helper\Log_Helper;
use Hammer\Helper\WP_Helper;
use CP_Defender\Module\Audit\Component\Audit_API;
use CP_Defender\Module\Audit\Component\Audit_Table;
use CP_Defender\Module\Audit\Model\Settings;
use CP_Defender\Vendor\Email_Search;

class Main_Free extends \CP_Defender\Controller {
	protected $slug = 'wdf-logging';
	public $layout = 'layout';
	public $email_search;

	public function __construct() {
		$this->email_search = new Email_Search();
		
		if ( $this->is_network_activate( cp_defender()->plugin_slug ) ) {
			$this->add_action( 'network_admin_menu', 'adminMenu' );
		} else {
			$this->add_action( 'admin_menu', 'adminMenu' );
		}

		if ( $this->isInPage() || $this->isDashboard() ) {
			$this->add_action( 'defender_enqueue_assets', 'scripts', 11 );
		}
	}

	/**
	 * Add submit admin page
	 */
	public function adminMenu() {
		$cap = is_multisite() ? 'manage_network_options' : 'manage_options';
		add_submenu_page( 'cp-defender', esc_html__( "Audit Logging", cp_defender()->domain ), esc_html__( "Audit Logging", cp_defender()->domain ), $cap, $this->slug, array(
			&$this,
			'actionIndex'
		) );
	}

	public function scripts() {
		if ( $this->isInPage() ) {
			\WDEV_Plugin_Ui::load( cp_defender()->getPluginUrl() . 'shared-ui/' );
			wp_enqueue_script( 'defender' );
			wp_enqueue_style( 'defender' );
			wp_enqueue_script( 'audit', cp_defender()->getPluginUrl() . 'app/module/audit/js/script.js', array(
				'jquery-effects-core'
			) );
			wp_enqueue_script( 'audit-momentjs', cp_defender()->getPluginUrl() . 'app/module/audit/js/moment/moment.min.js' );
			wp_enqueue_style( 'audit-daterangepicker', cp_defender()->getPluginUrl() . 'app/module/audit/js/daterangepicker/daterangepicker.css' );
			wp_enqueue_script( 'audit-daterangepicker', cp_defender()->getPluginUrl() . 'app/module/audit/js/daterangepicker/daterangepicker.js' );
		} else {
			wp_enqueue_script( 'audit', cp_defender()->getPluginUrl() . 'app/module/audit/js/script.js' );
		}
	}

	public function actionIndex() {
		$from = HTTP_Helper::retrieve_get( 'date_from' );
		$to   = HTTP_Helper::retrieve_get( 'date_to' );

		if ( $from ) {
			$from = strtotime( $from );
		} else {
			$from = strtotime( '-30 days' );
		}

		if ( $to ) {
			$to = strtotime( $to );
		} else {
			$to = time();
		}

		$this->renderPartial( 'main', array(
			'from'           => date( 'm/d/Y', $from ),
			'to'             => date( 'm/d/Y', $to ),
			'email_search'   => new Email_Search(),
			'settings'       => Settings::instance()
		) );
	}
}