<?php

if (!class_exists("wpdreamsPageParents")) {
	/**
	 * Class wpdreamsPageParents
	 *
	 * Displays the pages that have children.
	 *
	 * @package  WPDreams/OptionsFramework/Classes
	 * @category Class
	 * @author Ernest Marcinko <ernest.marcinko@wp-dreams.com>
	 * @link http://wp-dreams.com, http://codecanyon.net/user/anago/portfolio
	 * @copyright Copyright (c) 2014, Ernest Marcinko
	 */
	class wpdreamsPageParents extends wpdreamsType {
		function getType() {
			parent::getType();
			$this->processData();
			$this->types = $this->get_pages_witch_children_list();
			echo "
      <div class='wpdreamsPageParents' id='wpdreamsPageParents-" . self::$_instancenumber . "'>
        <fieldset>
          <legend>" . $this->label . "</legend>";
			echo '<div class="sortablecontainer" id="sortablecontainer' . self::$_instancenumber . '">
            <div class="arrow-all-left"></div>
            <div class="arrow-all-right"></div>
            <p>Available parent pages</p><ul id="sortable' . self::$_instancenumber . '" class="connectedSortable">';
			if ($this->types != null && is_array($this->types)) {
				foreach ($this->types as $k => $v) {
					if ($this->selected == null || !in_array($k, $this->selected)) {
						echo '<li class="ui-state-default" bid="'.$k.'">' . $v . '</li>';
					}
				}
			}
			echo "</ul></div>";
			echo '<div class="sortablecontainer"><p>&nbsp;</p><ul id="sortable_conn' . self::$_instancenumber . '" class="connectedSortable">';
			if ($this->selected != null && is_array($this->selected)) {
				foreach ($this->selected as $k) {
					echo '<li class="ui-state-default" bid="'.$k.'">' . $this->types[$k] . '</li>';
				}
			}
			echo "</ul></div>";
			echo "
         <input isparam=1 type='hidden' value='" . $this->data . "' name='" . $this->name . "'>";
			echo "
         <input type='hidden' value='wpdreamsPageParents' name='classname-" . $this->name . "'>";
			echo "
        </fieldset>
      </div>";
		}

		function processData() {
			$this->data = str_replace("\n", "", $this->data);
			if ($this->data != "")
				$this->selected = explode("|", $this->data);
			else
				$this->selected = null;
			//$this->css = "border-radius:".$this->topleft."px ".$this->topright."px ".$this->bottomright."px ".$this->bottomleft."px;";
		}

		final function getData() {
			return $this->data;
		}

		final function getSelected() {
			return $this->selected;
		}

		final function get_pages_witch_children_list() {
			global $wpdb;
			$ret = array();
			$types = $wpdb->get_results("SELECT ID as id, post_title as title FROM " . $wpdb->posts . " p
				WHERE
				EXISTS (
					SELECT ID  FROM " . $wpdb->posts . " xp
					WHERE xp.post_parent = p.ID
					AND post_type = 'page'
					AND post_status IN ('publish', 'private')
				)
				AND post_type = 'page'
				AND post_status IN ('publish', 'private')
				LIMIT 150
				", ARRAY_A);
			if ($types != null && is_array($types)) {
				foreach ($types as $k => $v) {
					$ret[$v['id']] = $v['title'];
				}
			}
			return $ret;
		}
	}
}