<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

/**
 * Fulltext creator/checker class for WP by Ernest Marcinko
 *
 * @class       wpdreamsFulltext
 * @version     2.0
 * @package     AjaxSearchPro/Abstracts
 * @category    Class
 * @author      Ernest Marcinko
 *
 */
if (!class_exists('wpdreamsFulltext')) {
    class wpdreamsFulltext {
        private static $singleton_instance = null;

        private function construct__() {

        }

        public static function getInstance() {
            static $singleton_instance = null;
            if ($singleton_instance === null) {
                $singleton_instance = new wpdreamsFulltext();
            }

            return ($singleton_instance);
        }


        /**
         * Creates the indexes on the array of (specified table, index name and
         * columns)
         *
         * @return boolean true on success, false on failure
         * @param
         *      Array(
         *        Array('table'=>'..', 'index'=>'..', 'columns'='..')
         *      )
         *
         */
        public function createIndexes($indexes) {
            global $wpdb;
            /* Temporarily ensure that errors are not displayed: */
            $previous_value = $wpdb->hide_errors();
            foreach ($indexes as $index) {
                if ($this->indexExists($index['table'], $index['index'])) continue;
                $wpdb->query("ALTER TABLE " . $wpdb->{$index['table']} . " ADD FULLTEXT `" . $index['index'] . "` (" . $index['columns'] . ")");
                if (!empty($wpdb->last_error)) {
                    return false;
                }
            }
            /* Restore previous setting */
            $wpdb->show_errors($previous_value);

            return true;
        }


        /**
         * Removes the selected indexes
         *
         * @return boolean true on success, false on failure
         * @param string[] of index names: Array('tablename'=>Array('index1'..), ..);
         *
         */
        public function removeIndexes($indexes) {
            global $wpdb;
            /* Temporarily ensure that errors are not displayed: */
            $previous_value = $wpdb->hide_errors();
            foreach ($indexes as $table => $_indexes) {
                if (is_array($_indexes)) {
                    foreach ($_indexes as $index) {
                        $wpdb->query("ALTER TABLE " . $wpdb->{$table} . " DROP INDEX " . $index);
                    }
                } else {
                    $wpdb->query("ALTER TABLE " . $wpdb->{$table} . " DROP INDEX " . $_indexes);
                }
            }
            /* Restore previous setting */
            $wpdb->show_errors($previous_value);
        }

        /**
         * Checks if creating fulltext indexes is possible
         *
         * Compares the database version number to 5.5 - innoDB engine does not have FULLTEXT indexes before that
         * database version. It does not check MyIsam availability anymore.
         *
         * @return bool
         */
        public function isFulltextAvailable() {
            global $wpdb;
            if ( method_exists( $wpdb, 'db_version' ) )
                return ( version_compare($wpdb->db_version(), '5.5', '>=') );
            return false;
        }

        /**
         * Checks if index exists on the given table
         *
         */
        public function indexExists($table, $index) {
            global $wpdb;
            $wpdb->get_results("SHOW INDEX FROM " . $wpdb->{$table} . " WHERE Key_name = '" . $index . "'");
            return ($wpdb->num_rows >= 1);
        }

        /**
         * Checks if the array of table has myisam engine enabled.
         *
         * @param string[] $tables the string array of tables
         *
         */
        public function check($tables) {
            if (!is_array($tables)) return false;
            foreach ($tables as $table) {
                if (!$this->myisamEnabled($table)) return false;
            }
            return true;
        }

        /**
         * Retrievs the value of 'ft_min_word_len' variable
         *
         */
        public function getMinWordLength() {
            global $wpdb;
            $previous_value = $wpdb->hide_errors();
            $res = $wpdb->get_row("SHOW VARIABLES LIKE 'ft_min_word_len'");
            $wpdb->show_errors($previous_value);
            return ($res == null) ? 4 : $res->Value;
        }

        /**
         * Checks if the given table has myisam enabled.
         *
         * @param string $table the database table
         */
        private function myisamEnabled($table) {
            global $wpdb;
            $tables = $wpdb->get_results("show table status like '" . $wpdb->{$table} . "'");
            foreach ($tables as $table) {
                if ($table->Engine === 'MyISAM')
                    return true;
                else
                    return false;
            }
            return false;
        }
    }
}