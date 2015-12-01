<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");


wp_register_script('wpdreams-jqPlot', ASP_URL . 'backend/settings/assets/js/jqPlot/jquery.jqplot.min.js', array(
    'jquery'
));
wp_enqueue_script('wpdreams-jqPlot');
wp_register_script('wpdreams-jqPlotdateAxisRenderer', ASP_URL . 'backend/settings/assets/js/jqPlot/plugins/jqplot.dateAxisRenderer.min.js', array(
    'wpdreams-jqPlot'
));
wp_enqueue_script('wpdreams-jqPlotdateAxisRenderer');
wp_register_script('wpdreams-jqPlotcanvasTextRenderer', ASP_URL . 'backend/settings/assets/js/jqPlot/plugins/jqplot.canvasTextRenderer.min.js', array(
    'wpdreams-jqPlot'
));
wp_enqueue_script('wpdreams-jqPlotcanvasTextRenderer');
wp_register_script('wpdreams-jqPlotcanvasAxisTickRenderer', ASP_URL . 'backend/settings/assets/js/jqPlot/plugins/jqplot.canvasAxisTickRenderer.min.js', array(
    'wpdreams-jqPlot'
));
wp_enqueue_script('wpdreams-jqPlotcanvasAxisTickRenderer');
wp_register_script('wpdreams-jqPlotcategoryAxisRenderer', ASP_URL . 'backend/settings/assets/js/jqPlot/plugins/jqplot.categoryAxisRenderer.min.js', array(
    'wpdreams-jqPlot'
));
wp_enqueue_script('wpdreams-jqPlotcategoryAxisRenderer');
wp_register_script('wpdreams-jqPlotbarRenderer', ASP_URL . 'backend/settings/assets/js/jqPlot/plugins/jqplot.barRenderer.min.js', array(
    'wpdreams-jqPlot'
));
wp_enqueue_script('wpdreams-jqPlotbarRenderer');

wp_register_style('wpdreams-jqPlotstyle', ASP_URL . 'backend/settings/assets/js/jqPlot/jquery.jqplot.min.css');
wp_enqueue_style('wpdreams-jqPlotstyle');


global $sliders;
global $wpdb;
if (isset($_POST['asp_stat'])) {
    update_option("asp_stat", $_POST['asp_stat']);
}
$asp_stat = get_option("asp_stat") === false ? 0 : get_option("asp_stat");
$where = "";

$where = "";
if (isset($_POST['searchform']) && $_POST['searchform'] != 0) {
    $where = " WHERE search_id=" . $_POST['searchform'];
}
if (isset($_POST['clearstatistics'])) {
    $wpdb->query("DELETE FROM " . $wpdb->prefix . "ajaxsearchpro_statistics");
}
$onoff = get_option("asp_stat");
$searchforms = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "ajaxsearchpro", ARRAY_A);
$top20 = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "ajaxsearchpro_statistics " . $where . " ORDER BY num DESC LIMIT 20", ARRAY_A);
$last20 = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "ajaxsearchpro_statistics " . $where . " ORDER BY last_date DESC LIMIT 20", ARRAY_A);
$all = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "ajaxsearchpro_statistics " . $where . " ORDER BY num DESC", ARRAY_A);
if (isset($_POST['searchform']))
    $current_search = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "ajaxsearchpro WHERE id=" . $_POST['searchform'], ARRAY_A);


?>
<div id="wpdreams" class='wpdreams wrap'>
    <div class="wpdreams-box">
        <style>
            #all span {
                font-size: 12px;
            }
        </style>
        <fieldset>
            <legend>Statistics Options</legend>
            <form style='margin:20px;' name="asp_stat_settings" action="" method="POST">
                <div class="item">
                    <?php $o = new wpdreamsYesNo("asp_stat", "Enable statistics?", $asp_stat); ?>
                </div>
            </form>
            <form style='float:left;margin:20px;' name="settings" action="" method="POST">
                <input name="clearstatistics" class='submit' type="submit"
                       onclick='var c=confirm("Are you sure?");if (!c) event.preventDefault();'
                       value="Clear search Statistics"/>
            </form>
            <form style='float:left;margin:20px;' name="settings" action="" method="POST">
                <label>Statistics for:</label>
                <select name='searchform'>
                    <option value='0'>All</option>
                    <?php foreach ($searchforms as $search) { ?>
                        <option value='<?php echo $search['id'] ?>'><?php echo $search['name'] ?></option>
                    <?php } ?>
                </select>
                <input type='submit' class='submit' value='Get Statistics!'/>
            </form>
            <div class='clear'></div>
        </fieldset>
        </form>
    </div>
    <ul id="tabs" class='tabs'>
        <li><a tabid="1" class='current'>Statistics
                for: <?php echo(isset($current_search['name']) ? $current_search['name'] : 'All'); ?></a></li>
        <li><a tabid="2">Keywords</a></li>
    </ul>
    <div id="content" class='tabscontent'>
        <div tabid="1">
            <div id='top20' style='width:800px; height:300px;margin:70px;float:left;'></div>
            <div id='last20' style='width:800px; height:300px;margin:70px;float:left;'></div>
            <div class='clear'></div>
        </div>
        <div tabid="2">
            <div id='all' style='width:800px; height:auto;margin:70px;float:left;'>
                <h3>All keywords</h3>
                <?php
                foreach ($all as $keyword) {
                    echo "
            <span>&nbsp;&nbsp;" . strip_tags($keyword['keyword']) . " (" . $keyword['num'] . ")
            &nbsp;&nbsp;<img keyword='" . $keyword['id'] . "' style='cursor:pointer;vertical-align:middle;' title='Click here if you want to delete this keyword from the list!'' src='" . plugins_url('/settings/assets/icons/delete.png', __FILE__) . "' class='deletekeyword' />
            </span>
            ";
                }
                ?>
            </div>
            <div class='clear'></div>
        </div>
    </div>
    <script>
        (function ($) {
            $(document).ready(function () {

                $("form[name='asp_stat_settings'] .wpdreamsYesNoInner").on("click", function () {
                    setTimeout(function () {
                        $("form[name='asp_stat_settings']").get(0).submit();
                    }, 500);
                });

                $('#content .deletekeyword').click(function () {
                    var del = confirm("Do yo really want to delete this item?");
                    var $this = $(this);
                    if (del) {
                        id = $(this).attr('keyword');
                        var data = {
                            action: 'ajaxsearchpro_deletekeyword',
                            keywordid: id
                        };
                        jQuery.post(ajaxsearchpro.ajaxurl, data, function (response) {
                            if (response == 1) {
                                $this.parent().fadeOut();
                            }
                        });
                    }
                });

                <?php
                  $items1 = "";
                  foreach ($top20 as $item) {
                    $items1.= "['".wd_mysql_escape_mimic($item['keyword'])."', ".$item['num']."],";
                    rtrim($items1, ",");
                  }
                  $items2 = "";
                  foreach ($last20 as $item) {
                    $items2.= "['".wd_mysql_escape_mimic($item['keyword'])."', ".$item['num']."],";
                    rtrim($items2, ",");
                  }
                ?>
                var line1 = [<?php echo $items1; ?>];
                var line2 = [<?php echo $items2; ?>];
                var plot1 = $.jqplot('top20', [line1], {
                    title: 'Top 20 Search Phrases',
                    series: [
                        {renderer: $.jqplot.BarRenderer}
                    ],
                    axesDefaults: {
                        tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                        tickOptions: {
                            angle: -30,
                            fontSize: '10pt'
                        }
                    },
                    axes: {
                        xaxis: {
                            renderer: $.jqplot.CategoryAxisRenderer
                        }
                    }
                });
                var plot2 = $.jqplot('last20', [line2], {
                    title: 'Last 20 Search Phrases',
                    series: [
                        {renderer: $.jqplot.BarRenderer}
                    ],
                    axesDefaults: {
                        tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                        tickOptions: {
                            angle: -30,
                            fontSize: '10pt'
                        }
                    },
                    axes: {
                        xaxis: {
                            renderer: $.jqplot.CategoryAxisRenderer
                        }
                    }
                });
            });
        }(jQuery));
    </script>
</div>      