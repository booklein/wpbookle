<?php
if (!class_exists("wpdreamsSortable")) {
    /**
     * Class wpdreamsCustomPostTypes
     *
     * A sortable based on jQuery UI
     *
     * @package  WPDreams/OptionsFramework/Classes
     * @category Class
     * @author Ernest Marcinko <ernest.marcinko@wp-dreams.com>
     * @link http://wp-dreams.com, http://codecanyon.net/user/anago/portfolio
     * @copyright Copyright (c) 2014, Ernest Marcinko
     */
    class wpdreamsSortable extends wpdreamsType {
        function getType() {
            parent::getType();
            $this->processData();
            echo "
        <div class='wpdreamsSortable'>";
            echo '<div class="sortablecontainer" style="float:right;"><p>'.$this->label.'</p><ul id="sortable' . self::$_instancenumber . '" class="connectedSortable">';
            if ($this->types != null && is_array($this->types)) {
                foreach ($this->types as $k => $v) {
                    echo '<li class="ui-state-default">' . $v . '</li>';
                }
            }
            echo "
            </ul></div>
         <input isparam=1 type='hidden' value='" . $this->data . "' name='" . $this->name . "'>";
            echo "
         <input type='hidden' value='wpdreamsSortable' name='classname-" . $this->name . "'>";
            ?>
            <script type='text/javascript'>
                (function ($) {
                    $(document).ready(function () {
                        $("#sortable<?php echo self::$_instancenumber ?>, #sortable_conn<?php echo self::$_instancenumber ?>").sortable({

                        }, {
                            update: function (event, ui) {
                                parent = $(ui.item).parent();
                                while (!parent.hasClass('wpdreamsSortable')) {
                                    parent = $(parent).parent();
                                }
                                var items = $('ul[id*=sortable] li', parent);
                                var hidden = $('input[name=<?php echo $this->name; ?>]', parent);
                                var val = "";
                                items.each(function () {
                                    val += "|" + $(this).html();
                                });
                                val = val.substring(1);
                                hidden.val(val);
                            }
                        }).disableSelection();
                    });
                }(jQuery));
            </script>
         </div>
         <div class="clear"></div>
            <?php
        }

        function processData() {
            $this->data = str_replace("\n", "", $this->data);
            if ($this->data != "")
                $this->types = explode("|", $this->data);
            else
                $this->types = array();
        }

        final function getData() {
            return $this->data;
        }

        final function getSelected() {
            return $this->types;
        }
    }
}