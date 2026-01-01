<?php
/**
 * @author: Hoang Ngo
 */
// If uninstall is not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$phpVersion = phpversion();
if ( version_compare( $phpVersion, '5.3', '<' ) ) {
	//php 5.2 does not need uninstall
	return;
}

$path = dirname( __FILE__ );
include_once $path . DIRECTORY_SEPARATOR . 'cp-defender.php';

$tweakFixed = \CP_Defender\Module\Hardener\Model\Settings::instance()->getFixed();

foreach ( $tweakFixed as $rule ) {
	$rule->getService()->revert();
}

$scan = \CP_Defender\Module\Scan\Model\Scan::findAll();
foreach ( $scan as $model ) {
	$model->delete();
}

\CP_Defender\Module\Scan\Component\Scan_Api::flushCache();

$cache = \Hammer\Helper\WP_Helper::getCache();
$cache->delete( 'isActivated' );
$cache->delete( 'wdfchecksum' );
$cache->delete( 'cleanchecksum' );

\CP_Defender\Module\Scan\Model\Settings::instance()->delete();
\CP_Defender\Module\Audit\Model\Settings::instance()->delete();
\CP_Defender\Module\Hardener\Model\Settings::instance()->delete();
\CP_Defender\Module\IP_Lockout\Model\Settings::instance()->delete();
\CP_Defender\Module\Advanced_Tools\Model\Auth_Settings::instance()->delete();
//clear old stuff
delete_site_option( 'cp_defender' );
delete_option( 'cp_defender' );
delete_option( 'wd_db_version' );
delete_site_option( 'wd_db_version' );