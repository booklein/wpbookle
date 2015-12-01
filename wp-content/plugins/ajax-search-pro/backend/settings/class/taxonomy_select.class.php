<?php
if (!class_exists("wpdreamsTaxonomySelect")) {
    /**
     * Class wpdreamsTaxonomySelect
     *
     * A taxonomy drag and drop UI element.
     *
     * @package  WPDreams/OptionsFramework/Classes
     * @category Class
     * @author Ernest Marcinko <ernest.marcinko@wp-dreams.com>
     * @link http://wp-dreams.com, http://codecanyon.net/user/anago/portfolio
     * @copyright Copyright (c) 2014, Ernest Marcinko
     */
    class wpdreamsTaxonomySelect extends wpdreamsType {
        function getType() {
            parent::getType();
            $this->processData();
            $this->types = $this->getAllTaxonomies();
            echo "
      <div class='wpdreamsTaxonomySelect'>
        <fieldset>

          <legend>" . $this->label . "</legend>";
            echo '<div class="sortablecontainer" id="sortablecontainer' . self::$_instancenumber . '">
                  <div class="arrow-all-left"></div>
                  <div class="arrow-all-right"></div>
            <p>Available taxonomies</p>
            <ul id="sortable' . self::$_instancenumber . '" class="connectedSortable">';
            if ($this->types != null && is_array($this->types)) {
                foreach ($this->types as $tax) {
                    $custom_post_type = "";
                    if ($tax->object_type != null && $tax->object_type[0] != null)
                        $custom_post_type = $tax->object_type[0] . " - ";
                    if ($this->selected == null || !wpdreams_in_array_r($tax->name, $this->selected)) {
                        echo '<li class="ui-state-default" taxonomy="' . $tax->name . '">' . $custom_post_type . $tax->labels->name . '</li>';
                    }

                }
            }
            echo "</ul></div>";
            echo '<div class="sortablecontainer"><p>Drag here the taxonomies you want to <b>' . $this->otype . '</b>!</p><ul id="sortable_conn' . self::$_instancenumber . '" class="connectedSortable">';
            if ($this->selected != null && is_array($this->selected)) {
                foreach ($this->selected as $_tax) {
                    $tax = get_taxonomy( $_tax );
                    $custom_post_type = "";
                    if ($tax->object_type != null && $tax->object_type[0] != null)
                        $custom_post_type = $tax->object_type[0] . " - ";
                    echo '<li class="ui-state-default" taxonomy="' . $tax->name . '">' . $custom_post_type . $tax->labels->name . '</li>';
                }
            }
            echo "</ul></div>";
            echo "
         <input isparam=1 type='hidden' value='" . $this->data["value"] . "' name='" . $this->name . "'>
         <input type='hidden' value='wpdreamsTaxonomySelect' name='classname-" . $this->name . "'>";
            ?>
            <script type='text/javascript'>
                (function ($) {
                    $(document).ready(function () {
                        var selector = "#sortable<?php echo self::$_instancenumber ?>, #sortable_conn<?php echo self::$_instancenumber ?>";
                        $(selector).sortable({
                            connectWith: ".connectedSortable"
                        }, {
                            update: function (event, ui) {
                            }
                        }).disableSelection();
                        $(selector).on('sortupdate', function(event, ui) {
                            if (typeof(ui)!='undefined')
                                parent = $(ui.item).parent();
                            else
                                parent = $(event.target);
                            while (!parent.hasClass('wpdreamsTaxonomySelect')) {
                                parent = $(parent).parent();
                            }
                            var items = $('ul[id*=sortable_conn] li', parent);
                            var hidden = $('input[name="<?php echo $this->name; ?>"]', parent);
                            var val = "";
                            items.each(function () {
                                val += "|" + $(this).attr('taxonomy');
                            });
                            val = val.substring(1);
                            hidden.val(val);
                        });
                        $("#sortablecontainer<?php echo self::$_instancenumber ?> .arrow-all-left").click(function(){
                            $("#sortable_conn<?php echo self::$_instancenumber ?> li")
                                .detach().appendTo("#sortable<?php echo self::$_instancenumber ?>");
                            $(selector).trigger("sortupdate");
                        });
                        $("#sortablecontainer<?php echo self::$_instancenumber ?> .arrow-all-right").click(function(){
                            $("#sortable<?php echo self::$_instancenumber ?> li")
                                .detach().appendTo("#sortable_conn<?php echo self::$_instancenumber ?>");
                            $(selector).trigger("sortupdate");
                        });
                    });
                }(jQuery));
            </script>
            <?php
            echo "
        </fieldset>
      </div>";
        }

        function getAllTaxonomies() {
            $args = array(
                'public' => true,
                '_builtin' => false

            );
            $output = 'objects'; // or objects
            $operator = 'and'; // 'and' or 'or'
            $taxonomies = get_taxonomies($args, $output, $operator);
            return $taxonomies;
        }

        function processData() {
            if (is_array($this->data) && isset($this->data['type']) && isset($this->data['value'])) {
                $this->otype = $this->data['type'];
                $this->v = str_replace("\n", "", $this->data["value"]);
            } else {
                $this->otype = "include";
                $this->v = str_replace("\n", "", $this->data);
            }

            $this->selected = array();
            if ($this->v != "") {
                $this->selected = explode("|", $this->v);
            } else {
                $this->selected = null;
            }

        }

        final function getData() {
            return $this->data;
        }

        final function getSelected() {
            return $this->selected;
        }
    }
}