<?php
class asp_updates {

	private static $_instance;

	private $url = "https://raw.githubusercontent.com/WPDreams/plugins/master/ajax-search-pro/updates.txt";

	// 2 seconds of timeout, no need to hold up the back-end
	private $timeout = 2;

	private $interval = 7200;

	private $option_name = "asp_updates";

	private $data = "";

	private $version = "";

	private $version_string = "";

	private $requires_version = "3.5";

	private $tested_version = "4.2";

	private $downloaded_count = 0;

	private $last_updated = "2015-01-01";

	private $knowledge_base = "";

	private $support = "";

	private $change_log = array();

	function __construct() {
		$this->getData();
		$this->processData();
	}

	function getData($force_update = false) {
		$last_checked = get_option($this->option_name . "_lc", time() - $this->interval - 500);

		if ($this->data != "" && $force_update != true) return;

		if (
			((time() - $this->interval) > $last_checked) ||
			$force_update
		) {
			$response = wp_remote_get( $this->url, array( 'timeout' => $this->timeout ) );
			if ( is_wp_error( $response ) ) {
				$this->data = get_option($this->option_name, false);
			} else {
				$this->data = $response['body'];
				update_option($this->option_name . "_lc", time());
				update_option($this->option_name, $this->data);
			}
		} else {
			$this->data = get_option($this->option_name, false);
		}
	}

	function processData() {
		if ($this->data === false) return false;

		// Version
		preg_match("/VERSION:(.*?)[\r\n]/s", $this->data, $m);
		$this->version = isset($m[1]) ? (trim($m[1]) + 0) : $this->version;

		// Version string
		preg_match("/VERSION_STRING:(.*?)[\r\n]/s", $this->data, $m);
		$this->version_string = isset($m[1]) ? trim($m[1]) : $this->version_string;

		// Requires version string
		preg_match("/REQUIRES:(.*?)[\r\n]/s", $this->data, $m);
		$this->requires_version = isset($m[1]) ? trim($m[1]) : $this->requires_version;

		// Tested version string
		preg_match("/TESTED:(.*?)[\r\n]/s", $this->data, $m);
		$this->tested_version = isset($m[1]) ? trim($m[1]) : $this->tested_version;

		// Downloaded count
		preg_match("/DOWNLOADED:(.*?)[\r\n]/s", $this->data, $m);
		$this->downloaded_count = isset($m[1]) ? trim($m[1]) : $this->downloaded_count;

		// Last updated date
		preg_match("/LAST_UPDATED:(.*?)[\r\n]/s", $this->data, $m);
		$this->last_updated = isset($m[1]) ? trim($m[1]) : $this->last_updated;

		// Support
		preg_match("/===SUPPORT===(.*?)(?:===|\Z)/s", $this->data, $m);
		$this->support = isset($m[1]) ? trim($m[1]) : $this->support;

		// Knowledge Base
		preg_match("/===KNOWLEDGE_BASE===(.*?)(?:===|\Z)/s", $this->data, $m);
		$this->knowledge_base = isset($m[1]) ? trim($m[1]) : $this->knowledge_base;
		$this->knowledge_base = preg_replace("/\[(.+?)\]\((.+?)\)/sm", "<li><a href='$2' target='_blank'>$1</a></li>", $this->knowledge_base);

		// ChangeLog
		preg_match("/===CHANGELOG===(.*?)(?:===|\Z)/sm", $this->data, $m);
		$changelog = isset($m[1]) ? trim($m[1]) : false;

		if ($changelog !== false) {
			preg_match_all( "/==(.*?)==[\r\n](.*?)==/s", $changelog, $mm );
			if (isset($mm[1]) && isset($mm[2]))
				foreach ($mm[1] as $k => $v) {
					// x[version] = version_changelog
					$this->change_log[$v] = $mm[2][$k];
				}
		}
	}

	function getVersion() {
		return $this->version;
	}

	function getVersionString() {
		return $this->version_string;
	}

	function needsUpdate() {
		if ($this->version != "")
			if ($this->version > ASP_CURR_VER)
				return true;
		return false;
	}

    function updateTransient() {
        return self::$force_update;
    }

	function getRequiresVersion() {
		return $this->requires_version;
	}

	function getTestedVersion() {
		return $this->tested_version;
	}

	function getDownloadedCount() {
		return $this->downloaded_count;
	}

	function getLastUpdated() {
		return $this->last_updated;
	}

	function getLastChangelog() {
		foreach ($this->change_log as $ver => $log) {
			return $log;
		}
		return "";
	}

	function getKnowledgeBase() {
		if ($this->knowledge_base != "")
			return "<ul>" . $this->knowledge_base . "</ul>";
		return $this->knowledge_base;
	}

	function getChangeLog() {
		return $this->change_log;
	}

	function getSupport() {
		return $this->support;
	}

	/**
	 * Get the instane of VC_Manager
	 *
	 * @return self
	 */
	public static function getInstance() {
		if ( ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Cloning disabled
	 */
	private function __clone() {
	}

	/**
	 * Serialization disabled
	 */
	private function __sleep() {
	}

	/**
	 * De-serialization disabled
	 */
	private function __wakeup() {
	}
}