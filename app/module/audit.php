<?php
/**
 * Author: Hoang Ngo
 */

namespace CP_Defender\Module;

use Hammer\Base\Module;
use CP_Defender\Module\Audit\Controller\Main;
use CP_Defender\Module\Audit\Controller\Main_Free;

class Audit extends Module {
	public function __construct() {
		if ( file_exists( __DIR__ . '/audit/test' ) ) {
			@unlink( __DIR__ . '/audit/test' );
		}
		if ( cp_defender()->isFree ) {
			new Main_Free();
		} else {
			new Main();
		}
	}
}