<?php
/* Prevent direct access */
defined( 'ABSPATH' ) or die( "You can't access this file directly." );

$it_options = get_option( 'asp_it_options' );
$index_obj  = new asp_indexTable();

if ( ASP_DEMO ) {
	$_POST = null;
}

$asp_cron_data = get_option("asp_it_cron", array(
    "last_run"  => "",
    "result"    => array()
));

if ( $index_obj->checkIndexTable( true ) === false ): ?>
	<div id="wpdreams" class='wpdreams wrap'>
		<div class="wpdreams-box">
			<p class="errorMsg">The index table does not exist and cannot be created. Please contact support.</p>
		</div>
	</div>
	<?php
	return;
endif;
?>
<div id="wpdreams" class='wpdreams wrap'>
	<div class="wpdreams-box">

		<?php ob_start(); ?>

		<!-- TODO Relevanssi table detection -->
		<div tabid="1">
			<div class="item">
				<?php $o = new wpdreamsYesNo( "it_index_title", "Index post titles?",
					wpdreams_setval_or_getoption( $it_options, 'it_index_title', 'asp_it_def' )
				); ?>
			</div>
			<div class="item">
				<?php $o = new wpdreamsYesNo( "it_index_content", "Index post content?",
					wpdreams_setval_or_getoption( $it_options, 'it_index_content', 'asp_it_def' )
				); ?>
			</div>
			<div class="item">
				<?php $o = new wpdreamsYesNo( "it_index_excerpt", "Index post excerpt?",
					wpdreams_setval_or_getoption( $it_options, 'it_index_excerpt', 'asp_it_def' )
				); ?>
			</div>
			<div class="item">
				<?php
				$o = new wpdreamsCustomPostTypesAll( "it_post_types", "Post types to index",
					wpdreams_setval_or_getoption( $it_options, 'it_post_types', 'asp_it_def' ) );
				?>
			</div>
			<div class="item">
				<?php $o = new wpdreamsYesNo( "it_index_tags", "Index post tags?",
					wpdreams_setval_or_getoption( $it_options, 'it_index_tags', 'asp_it_def' )
				); ?>
			</div>
			<div class="item">
				<?php $o = new wpdreamsYesNo( "it_index_categories", "Index post categories?",
					wpdreams_setval_or_getoption( $it_options, 'it_index_categories', 'asp_it_def' )
				); ?>
			</div>
			<div class="item">
				<?php
				$o = new wpdreamsTaxonomySelect( "it_index_taxonomies", "Index taxonomies", array(
					"value" => wpdreams_setval_or_getoption( $it_options, 'it_index_taxonomies', 'asp_it_def' ),
					"type"  => "include"
				) );
				?>
			</div>
			<div class="item"><?php
				$o = new wpdreamsCustomFields( "it_index_customfields", "Index custom fields",
					wpdreams_setval_or_getoption( $it_options, 'it_index_customfields', 'asp_it_def' ) );
				?>
			</div>
			<div class="item">
				<?php $o = new wpdreamsText( "it_post_statuses", "Post statuses to index",
					wpdreams_setval_or_getoption( $it_options, 'it_post_statuses', 'asp_it_def' )
				); ?>
				<p class="descMsg">Comma separated list. WP Defaults: publish, future, draft, pending, private, trash,
					auto-draft</p>
			</div>
			<div class="item">
				<?php $o = new wpdreamsYesNo( "it_index_author_name", "Index post author name?",
					wpdreams_setval_or_getoption( $it_options, 'it_index_author_name', 'asp_it_def' )
				); ?>
			</div>
			<div class="item">
				<?php $o = new wpdreamsYesNo( "it_index_author_bio", "Index post author bio (description)?",
					wpdreams_setval_or_getoption( $it_options, 'it_index_author_bio', 'asp_it_def' )
				); ?>
			</div>
		</div>
		<div tabid="2">
            <div class="item">
                <?php $o = new wpdreamsYesNo( "it_replace_dash_like", "Remove dash like characters?",
                    wpdreams_setval_or_getoption( $it_options, 'it_replace_dash_like', 'asp_it_def' )
                ); ?>
                <p class="descMsg">Will replace the "-" (dash) and the "_" (underscore) characters with spaces.
                    Leave on OFF, if you have product names or descriptions with dashes.</p>
            </div>
			<div class="item"><?php
				$o = new wpdreamsBlogselect( "it_blog_ids", "Blogs to index posts from",
					wpdreams_setval_or_getoption( $it_options, 'it_blog_ids', 'asp_it_def' ) );
				?>
			</div>
			<div class="item">
				<?php $o = new wpdreamsTextSmall( "it_limit", "Post limit per iteration",
					wpdreams_setval_or_getoption( $it_options, 'it_limit', 'asp_it_def' )
				); ?>
				<p class="descMsg">Posts to index per ajax call. Reduce this number if the process fails. Default:
					25</p>
			</div>
			<div class="item">
				<?php $o = new wpdreamsYesNo( "it_use_stopwords", "Enable stop-words?",
					wpdreams_setval_or_getoption( $it_options, 'it_use_stopwords', 'asp_it_def' )
				); ?>
				<p class="descMsg">Words from the list below (common words, stop words) will be excluded if enabled.</p>
			</div>
			<div class="item">
				<?php $o = new wpdreamsTextarea( "it_stopwords", "Stop words list",
					wpdreams_setval_or_getoption( $it_options, 'it_stopwords', 'asp_it_def' )
				); ?>
				<p class="descMsg"><strong>Comma</strong> separated list of stop words.</p>
			</div>
			<div class="item">
				<?php $o = new wpdreamsTextSmall( "it_min_word_length", "Min. word length",
					wpdreams_setval_or_getoption( $it_options, 'it_min_word_length', 'asp_it_def' )
				); ?>
				<p class="descMsg">Words below this length will be ignored. Default: 3</p>
			</div>
			<div class="item">
				<?php $o = new wpdreamsYesNo( "it_extract_shortcodes", "Execute shortcodes?",
					wpdreams_setval_or_getoption( $it_options, 'it_extract_shortcodes', 'asp_it_def' )
				); ?>
				<p class="descMsg">Will execute shortcodes in content as well. Great if you have lots of content
					generated by shortcodes.</p>
			</div>
			<div class="item">
				<?php $o = new wpdreamsTextarea( "it_exclude_shortcodes", "Remove these shortcodes",
					wpdreams_setval_or_getoption( $it_options, 'it_exclude_shortcodes', 'asp_it_def' )
				); ?>
				<p class="descMsg"><strong>Comma</strong> separated list of shortcodes to remove. Use this to exclude
					shortcodes, which does not reflect your content appropriately.</p>
			</div>
		</div>
        <div tabid="3">
            <div class="item">
                <div class="infoMsg">New/modified posts are automatically indexed when publishing/saving them. Use the cron only if neccessary.</div>
            </div>
            <div class="item">
                <?php $o = new wpdreamsYesNo( "it_cron_enable", "Use wp_cron() to extend the index table automatically?",
                    wpdreams_setval_or_getoption( $it_options, 'it_cron_enable', 'asp_it_def' )
                ); ?>
                <p class="descMsg">Will register a cron job with wp_cron() and run it periodically.</p>
            </div>
            <div class="item">
                <?php $o = new wpdreamsCustomSelect( "it_cron_period", "Period",
                    array(
                        'selects' => array(
                            array("option" => "Hourly", "value" => "hourly"),
                            array("option" => "Twice Daily", "value" => "twicedaily"),
                            array("option" => "Daily", "value" => "daily")
                        ),
                        'value' => wpdreams_setval_or_getoption( $it_options, 'it_cron_period', 'asp_it_def' )
                    )
                ); ?>
                <p class="descMsg">The periodicity of execution. wp_cron() only accepts these values.</p>
            </div>
            <div class="item">
                <fieldset>
                    <legend>Last execution info</legend>
                    <ul style="float:right;text-align:left;width:50%;">
                        <li><b>Last exeuction time: </b><?php echo $asp_cron_data['last_run'] != "" ? date("H:i:s, F j. Y", $asp_cron_data['last_run']) : "No information."; ?></li>
                        <li><b>Current system time: </b><?php echo date("H:i:s, F j. Y", time()); ?></li>
                        <li><b>Posts indexed: </b><?php echo w_isset_def($asp_cron_data['result']['postsIndexedNow'], "No information."); ?></li>
                        <li><b>Keywords found: </b><?php echo w_isset_def($asp_cron_data['result']['keywordsFound'], "No information."); ?></li>
                    </ul>
                </fieldset>
            </div>
        </div>
		<?php $_r = ob_get_clean(); ?>

		<?php
		$updated = false;
		if ( isset( $_POST ) && isset( $_POST['submit_asp_index_options'] ) && ( wpdreamsType::getErrorNum() == 0 ) ) {
			$values = array(
				'it_index_title'        => $_POST['it_index_title'],
				'it_index_content'      => $_POST['it_index_content'],
				'it_index_excerpt'      => $_POST['it_index_excerpt'],
				'it_post_types'         => $_POST['it_post_types'],
				'it_index_tags'         => $_POST['it_index_tags'],
				'it_index_categories'   => $_POST['it_index_categories'],
				'it_index_taxonomies'   => $_POST['it_index_taxonomies'],
				'it_index_customfields' => $_POST['it_index_customfields'],
				'it_post_statuses'      => $_POST['it_post_statuses'],
				'it_index_author_name'  => $_POST['it_index_author_name'],
				'it_index_author_bio'   => $_POST['it_index_author_bio'],
                'it_replace_dash_like'  => $_POST['it_replace_dash_like'],
				'it_blog_ids'           => $_POST['it_blog_ids'],
				'it_limit'              => $_POST['it_limit'],
				'it_use_stopwords'      => $_POST['it_use_stopwords'],
				'it_stopwords'          => $_POST['it_stopwords'],
				'it_min_word_length'    => $_POST['it_min_word_length'],
				'it_extract_shortcodes' => $_POST['it_extract_shortcodes'],
				'it_exclude_shortcodes' => $_POST['it_exclude_shortcodes'],
                'it_cron_enable'        => $_POST['it_cron_enable'],
                'it_cron_period'        => $_POST['it_cron_period']
			);
			update_option( 'asp_it_options', $values );
			$updated = true;
		}
		?>
		<?php
		$_comp = wpdreamsCompatibility::Instance();
		if ( $_comp->has_errors() ):
			?>
			<div class="wpdreams-slider errorbox">
				<p class='errors'>Possible incompatibility! Please go to the <a
						href="<?php echo get_admin_url() . "admin.php?page=ajax-search-pro/backend/comp_check.php"; ?>">error
						check</a> page to see the details and solutions!</p>
			</div>
		<?php endif; ?>
		<div class='wpdreams-slider'>

			<?php if ( ASP_DEMO ): ?>
				<p class="infoMsg">DEMO MODE ENABLED - Please note, that these options are read-only on the demo</p>
			<?php endif; ?>

			<form name='asp_indextable_settings' id='asp_indextable_settings' class="asp_indextable_settings"
			      method='post'>

				<fieldset>
					<legend>Index Table Operations</legend>
					<div id="index_buttons" style="margin: 0 0 15px 0;">
						<input type="button" name="asp_index_new" id="asp_index_new" class="submit wd_button_green"
						       index_action='new' index_msg='Do you want to generate a new index table?'
						       value="Create new index">
						<input type="button" name="asp_index_extend" id="asp_index_extend" class="submit wd_button_blue"
						       index_action='extend' index_msg='Do you want to extend the index table?'
						       value="Continue existing index">
						<input type="button" name="asp_index_delete" id="asp_index_delete" class="submit"
						       index_action='delete' index_msg='Do you really want to empty the index table?'
						       value="Delete the index">
					</div>
					<div class="wd_progress_text hiddend">Initializing, please wait. This might take a while.</div>
					<div class="wd_progress wd_progress_75 hiddend"><span style="width:0%;"></span></div>
					<span class="wd_progress_stop hiddend">Stop</span>

					<div id='asp_i_success' class="infoMsg hiddend">100% - Index table successfully generated!</div>
					<div id='asp_i_error' class="errorMsg hiddend">Something went wrong :(</div>
					<textarea id="asp_i_error_cont" class="hiddend"></textarea>

					<p class="descMsg">To read more about the index table, please read the <a
							href="http://wpdreams.gitbooks.io/ajax-search-pro-documentation/content/index_table.html">documentation
							chapter about Index table</a> usage.</p>
                    <?php if ( is_multisite() ): ?>
                        <p class="descMsg" style="color:#666; ">
                            Total keywords: <b id="keywords_counter"><?php echo $index_obj->getTotalKeywords(); ?></b>
                        </p>
                    <?php else: ?>
                        <p class="descMsg" style="color:#666; ">
                            Items Indexed: <b id="indexed_counter"><?php echo $index_obj->getPostsIndexed(); ?></b>
                            &nbsp;|&nbsp;Items not indexed: <b id="not_indexed_counter"><?php echo $index_obj->getPostIdsToIndexCount(); ?></b>
                            &nbsp;|&nbsp;Total keywords: <b id="keywords_counter"><?php echo $index_obj->getTotalKeywords(); ?></b>
                        </p>
                    <?php endif; ?>
				</fieldset>

				<fieldset id='asp_indextable_options'>
					<div id="asp_it_disable" class="hiddend"></div>

					<legend>Index Table options</legend>

					<?php if ( $updated ): ?>
						<div class='infoMsg'>Index table options successfuly updated!</div><?php endif; ?>

					<ul id="tabs" class='tabs'>
						<li><a tabid="1" class='current general'>General Options</a></li>
						<li><a tabid="2" class='advanced'>Advanced Options</a></li>
                        <li><a tabid="3" class='advanced'>Cron options</a></li>
					</ul>
					<div id="content" class='tabscontent'>
						<?php print $_r; ?>
					</div>
					<input type='hidden' name='asp_index_table_page' value='1'/>

					<div class="item">
						<input name="submit_asp_index_options" type="submit" value="Save options"/>
					</div>
				</fieldset>
			</form>

		</div>
	</div>
	<script>
		jQuery(function ($) {
			var post = null;
			var $buttons = $("#index_buttons input[type='button']");
			var $progress = $(".wd_progress_text, .wd_progress, .wd_progress_stop");
			var $progress_bar = $(".wd_progress span");
			var $progress_text = $(".wd_progress_text");
			var $overlay = $("#asp_it_disable");
			var $success = $("#asp_i_success");
			var $error = $("#asp_i_error");
			var $error_cont = $("#asp_i_error_cont");
			var data = "";
			var keywords_found = 0;
			var remaining_blogs = [];
			var blog = "";
			var initial_action = "";

			function asp_on_post_success(response) {
				var res = response.replace(/^\s*[\r\n]/gm, "");
				res = res.match(/!!!ASP_INDEX_START!!!(.*[\s\S]*)!!!ASP_INDEX_STOP!!!/);
				if (res != null && (typeof res[1] != 'undefined')) {
					res = JSON.parse(res[1]);

					if (
						typeof res.postsIndexed != "undefined" ||
						(typeof res.postsIndexed != "undefined" && remaining_blogs.length > 0)
					) {
						// New or extend operation
						res.postsIndexed = Number(res.postsIndexed);
						res.postsToIndex = Number(res.postsToIndex);
						res.keywordsFound = Number(res.keywordsFound);
                        res.totalKeywords = Number(res.totalKeywords);

                        $("#indexed_counter").html(res.postsIndexed);
                        $("#not_indexed_counter").html(res.postsToIndex);
                        $("#keywords_counter").html(res.totalKeywords);

						if (res.postsToIndex > 0 || remaining_blogs.length > 0) {
							var percent = (res.postsIndexed / (res.postsToIndex + res.postsIndexed)) * 100;
							keywords_found += res.keywordsFound;

							$progress_bar.css('width', percent + "%");

							if ($('input[name=it_blog_ids]').val() != "")
								$progress_text.html("Progress: " + percent.toFixed(2) + "% | Keywords found so far: " + keywords_found + " | Processing blog no. " + blog);
							else
								$progress_text.html("Progress: " + percent.toFixed(2) + "% | Keywords found so far: " + keywords_found);

							var the_action = 'extend';
							// No posts left, try switching the blog
							if (res.postsToIndex <= 0 && remaining_blogs.length > 0) {
								blog = remaining_blogs.shift();
								if (initial_action == 'new')
									the_action = 'switching_blog';
							}

							data = {
								action: 'asp_indextable_admin_ajax',
								asp_index_action: the_action,
								blog_id: blog,
								data: $('#asp_indextable_settings').serialize()
							};

                            // Wait a bit to cool off the server
                            setTimeout(function () {
                                post = $.post(ajaxurl, data)
                                    .done(asp_on_post_success)
                                    .fail(asp_on_post_failure);
                            }, 1500);

							return;
						}

						keywords_found += res.keywordsFound;
						$success.removeClass('hiddend').html("Success. <strong>" + keywords_found + "</strong> new keywords were added to the database.");
					} else {
                        res.postsToIndex = Number(res.postsToIndex);
                        res.totalKeywords = Number(res.totalKeywords);

                        $("#indexed_counter").html(0);
                        $("#not_indexed_counter").html(res.postsToIndex);
                        $("#keywords_counter").html(res.totalKeywords);

						$success.removeClass('hiddend').html("Success. The index table was emptied.");
					}
				} else {
					$error.removeClass('hiddend').html('Something went wrong. Here is the error message returned: ');
					$error_cont.removeClass('hiddend').val(response);
				}

				$buttons.removeAttr('disabled');
				$progress.addClass('hiddend');
				$overlay.addClass('hiddend');
			}

			function asp_on_post_failure(response, t) {
				if (t === "timeout") {
					$error.removeClass('hiddend').html('Timeout error. Try lowering the <strong>Post limit per iteration</strong> option below.');
				} else {
					$error.removeClass('hiddend').html('Something went wrong. Here is the error message returned: ');
					$error_cont.removeClass('hiddend').val(response);
				}
				$buttons.removeAttr('disabled');
				$progress.addClass('hiddend');
				$overlay.addClass('hiddend');
			}


			$('#asp_index_new, #asp_index_extend, #asp_index_delete').on('click', function (e) {
				if (!confirm($(this).attr('index_msg'))) {
					return false;
				}

				$('.wd_progress_stop').click();

				var blogids_input_val = $('input[name=it_blog_ids]').val().replace('xxx1', '');

				if ($('input.use-all-blogs').is(':checked')) {
					$(".wpdreamsBlogselect ul.connectedSortable li").each(function () {
						remaining_blogs.push($(this).attr('bid'));
					});
				} else if (blogids_input_val != "") {
					remaining_blogs = blogids_input_val.split('|');
				} else {
					remaining_blogs = [<?php echo get_current_blog_id(); ?>];
				}

				// Still nothing
				if (remaining_blogs.length == 0)
					remaining_blogs = [<?php echo get_current_blog_id(); ?>];

				blog = remaining_blogs.shift();

				$buttons.attr('disabled', 'disabled');
				$progress.removeClass('hiddend');
				$overlay.removeClass('hiddend');
				$success.addClass('hiddend');
				$error.addClass('hiddend');
				$error_cont.addClass('hiddend');

				initial_action = $(this).attr('index_action');

				data = {
					action: 'asp_indextable_admin_ajax',
					asp_index_action: $(this).attr('index_action'),
					blog_id: blog,
					data: $('#asp_indextable_settings').serialize()
				};

				// Wait a bit to cool off the server
				setTimeout(function () {
					post = $.post(ajaxurl, data)
						.done(asp_on_post_success)
						.fail(asp_on_post_failure);
				}, 250);
			});

			$('.wd_progress_stop').on('click', function (e) {
				if (post != null) post.abort();
				keywords_found = 0;
				data = "";
				$("#index_buttons input[type='button']").removeAttr('disabled');
				$(".wd_progress_text, .wd_progress, .wd_progress_stop").addClass('hiddend');
				$error.addClass('hiddend');
				$error_cont.addClass('hiddend');
				$progress_bar.css('width', "0%");
				$progress_text.html("Initializing, please wait.");
			});

			$('.tabs a[tabid=1]').click();
		});
	</script>
</div>