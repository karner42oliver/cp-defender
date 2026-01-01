<?php
/**
 * Author: Hoang Ngo
 */

namespace CP_Defender\Module\Scan\Behavior\Pro;

use Hammer\Base\Behavior;
use Hammer\Helper\Log_Helper;
use CP_Defender\Module\Scan\Component\Scan_Api;
use CP_Defender\Module\Scan\Model\Result_Item;

class Vuln_Scan extends Behavior {
	protected $endPoint = "https://premium.wpmudev.org/api/defender/v1/vulnerabilities";
	protected $model;

	public function processItemInternal( $args, $current ) {
		$model       = $args['model'];
		$this->model = $model;
		$this->scan();

		return true;
	}

	public function scan( $wp_version = null, $plugins = array(), $themes = array() ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if ( is_null( $wp_version ) ) {
			global $wp_version;
		}

		if ( empty( $plugins ) ) {
			//get all the plugins, even activate or not, as in network
			foreach ( get_plugins() as $slug => $plugin ) {
				$base_slug             = explode( '/', $slug ); //DIRECTORY_SEPARATOR wont work on windows
				$base_slug             = array_shift( $base_slug );
				$plugins[ $base_slug ] = $plugin['Version'];
			}
		}

		if ( empty( $themes ) ) {
			foreach ( wp_get_themes() as $theme ) {
				$themes[ $theme->get_template() ] = $theme->Version;
			}
		}

		// Check if WPScan API token is configured
		$settings = \CP_Defender\Module\Scan\Model\Settings::instance();
		$api_token = trim( $settings->wpscan_api_token );
		
		if ( empty( $api_token ) ) {
			// No API token configured - skip vulnerability scan
			return true;
		}

		// Use WPScan API for vulnerability checking
		$response = $this->checkWPScanAPI( $wp_version, $plugins, $themes, $api_token );

		if ( is_array( $response ) ) {
			if ( isset( $response['wordpress'] ) ) {
				$this->processWordPressVuln( $response['wordpress'] );
			}
			if ( isset( $response['plugins'] ) ) {
				$this->processPluginsVuln( $response['plugins'] );
			}
			if ( isset( $response['themes'] ) ) {
				$this->processThemesVuln( $response['themes'] );
			}
		}

		return true;
	}

	/**
	 * Check vulnerabilities via WPScan API
	 */
	private function checkWPScanAPI( $wp_version, $plugins, $themes, $api_token ) {
		$result = array(
			'wordpress' => array(),
			'plugins' => array(),
			'themes' => array()
		);

		// Check WordPress core
		$wp_response = wp_remote_get( 'https://wpscan.com/api/v3/wordpresses/' . $wp_version, array(
			'timeout' => 15,
			'headers' => array(
				'Authorization' => 'Token token=' . $api_token
			)
		) );

		if ( ! is_wp_error( $wp_response ) && 200 == wp_remote_retrieve_response_code( $wp_response ) ) {
			$wp_data = json_decode( wp_remote_retrieve_body( $wp_response ), true );
			if ( isset( $wp_data[ $wp_version ]['vulnerabilities'] ) ) {
				$result['wordpress'] = $wp_data[ $wp_version ]['vulnerabilities'];
			}
		}

		// Check plugins
		foreach ( $plugins as $slug => $version ) {
			$plugin_response = wp_remote_get( 'https://wpscan.com/api/v3/plugins/' . $slug, array(
				'timeout' => 15,
				'headers' => array(
					'Authorization' => 'Token token=' . $api_token
				)
			) );

			if ( ! is_wp_error( $plugin_response ) && 200 == wp_remote_retrieve_response_code( $plugin_response ) ) {
				$plugin_data = json_decode( wp_remote_retrieve_body( $plugin_response ), true );
				if ( isset( $plugin_data[ $slug ]['vulnerabilities'] ) ) {
					foreach ( $plugin_data[ $slug ]['vulnerabilities'] as $vuln ) {
						// Check if current version is vulnerable
						if ( $this->isVersionVulnerable( $version, $vuln ) ) {
							if ( ! isset( $result['plugins'][ $slug ] ) ) {
								$result['plugins'][ $slug ] = array();
							}
							$result['plugins'][ $slug ][] = $vuln;
						}
					}
				}
			}
		}

		// Check themes
		foreach ( $themes as $slug => $version ) {
			$theme_response = wp_remote_get( 'https://wpscan.com/api/v3/themes/' . $slug, array(
				'timeout' => 15,
				'headers' => array(
					'Authorization' => 'Token token=' . $api_token
				)
			) );

			if ( ! is_wp_error( $theme_response ) && 200 == wp_remote_retrieve_response_code( $theme_response ) ) {
				$theme_data = json_decode( wp_remote_retrieve_body( $theme_response ), true );
				if ( isset( $theme_data[ $slug ]['vulnerabilities'] ) ) {
					foreach ( $theme_data[ $slug ]['vulnerabilities'] as $vuln ) {
						if ( $this->isVersionVulnerable( $version, $vuln ) ) {
							if ( ! isset( $result['themes'][ $slug ] ) ) {
								$result['themes'][ $slug ] = array();
							}
							$result['themes'][ $slug ][] = $vuln;
						}
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Check if version is vulnerable based on WPScan data
	 */
	private function isVersionVulnerable( $version, $vuln ) {
		// WPScan provides fixed_in version
		if ( isset( $vuln['fixed_in'] ) && ! empty( $vuln['fixed_in'] ) ) {
			return version_compare( $version, $vuln['fixed_in'], '<' );
		}
		return true; // If no fix info, assume vulnerable
	}


	/**
	 * @param $issues
	 */
	private function processWordPressVuln( $issues ) {
		if ( empty( $issues ) ) {
			return;
		}
		$model           = new Result_Item();
		$model->type     = 'vuln';
		$model->parentId = $this->model->id;
		$model->status   = Result_Item::STATUS_ISSUE;
		$model->raw      = array(
			'type' => 'wordpress',
			'slug' => 'wordpress',
			'bugs' => array()
		);
		foreach ( $issues as $issue ) {
			$model->raw['bugs'][] = array(
				'vuln_type' => $issue['vuln_type'],
				'title'     => $issue['title'],
				'ref'       => $issue['references'],
				'fixed_in'  => $issue['fixed_in']
			);
		}

		$model->save();
	}

	/**
	 * @param $issues
	 */
	private function processThemesVuln( $issues ) {
		if ( empty( $issues ) ) {
			return;
		}

		foreach ( $issues as $slug => $bugs ) {
			if ( ( $id = Scan_Api::isIgnored( $slug ) ) ) {
				$status = Result_Item::STATUS_IGNORED;
				$model  = Result_Item::findByID( $id );
			} else {
				$status = Result_Item::STATUS_ISSUE;
				$model  = new Result_Item();
			}
			$model->parentId = $this->model->id;
			$model->type     = 'vuln';
			$model->status   = $status;
			$model->raw      = array(
				'type' => 'theme',
				'slug' => $slug,
				'bugs' => array()
			);
			if ( is_array( $bugs['confirmed'] ) ) {
				foreach ( $bugs['confirmed'] as $bug ) {
					$model->raw['bugs'][] = array(
						'vuln_type' => $bug['vuln_type'],
						'title'     => $bug['title'],
						'ref'       => $bug['references'],
						'fixed_in'  => $bug['fixed_in'],
					);
				}
			}
			if ( count( $model->raw['bugs'] ) ) {
				$model->save();
			}
		}
	}

	/**
	 * @param $issues
	 */
	private function processPluginsVuln( $issues ) {
		if ( empty( $issues ) ) {
			return;
		}
		foreach ( $issues as $slug => $bugs ) {
			if ( ( $id = Scan_Api::isIgnored( $slug ) ) ) {
				$status = Result_Item::STATUS_IGNORED;
				$model  = Result_Item::findByID( $id );
			} else {
				$status = Result_Item::STATUS_ISSUE;
				$model  = new Result_Item();
			}
			$model->parentId = $this->model->id;
			$model->type     = 'vuln';
			$model->status   = $status;
			$model->raw      = array(
				'type' => 'plugin',
				'slug' => $slug,
				'bugs' => array()
			);
			if ( is_array( $bugs['confirmed'] ) ) {
				foreach ( $bugs['confirmed'] as $bug ) {
					$model->raw['bugs'][] = array(
						'vuln_type' => $bug['vuln_type'],
						'title'     => $bug['title'],
						'ref'       => $bug['references'],
						'fixed_in'  => $bug['fixed_in'],
					);
				}
			}
			$model->save();
		}
	}

	/**
	 * @return array
	 */
	public function behaviors() {
		return array(
			'utils' => 'CP_Defender\Behavior\Utils'
		);
	}
}