<?php
/* Prevent direct access */
defined( 'ABSPATH' ) or die( "You can't access this file directly." );

class AjaxSearchProWidget extends WP_Widget {
	function __construct() {
		$widget_ops = array( 'classname' => 'AjaxSearchProWidget', 'description' => 'Displays an Ajax Search Pro!' );
		parent::__construct( 'AjaxSearchProWidget', 'Ajax Search Pro', $widget_ops );
	}

	function form( $instance ) {
		global $wpdb;
		if ( isset( $wpdb->base_prefix ) ) {
			$_prefix = $wpdb->base_prefix;
		} else {
			$_prefix = $wpdb->prefix;
		}
		$searches = $wpdb->get_results( "SELECT * FROM " . $_prefix . "ajaxsearchpro", ARRAY_A );
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title    = $instance['title'];
		if ( isset( $instance['searchid'] ) ) {
			$searchid = $instance['searchid'];
		} else {
			$searchid = '';
		}
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">Title: <input class="widefat"
		                                                                            id="<?php echo $this->get_field_id( 'title' ); ?>"
		                                                                            name="<?php echo $this->get_field_name( 'title' ); ?>"
		                                                                            type="text"
		                                                                            value="<?php echo esc_attr( $title ); ?>"/></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'searchid' ); ?>">Select the search form: </label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'searchid' ); ?>"
			        id="<?php echo $this->get_field_id( 'searchid' ); ?>">
				<?php
				if ( is_array( $searches ) ) {
					foreach ( $searches as $search ) {
						echo "<option value='" . $search['id'] . "' " . ( ( esc_attr( $searchid ) == $search['id'] ) ? "selected='selected'" : "''" ) . ">" . $search['name'] . "</option>";
					}
				}
				?>
			</select>
		</p>
	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['title']    = $new_instance['title'];
		$instance['searchid'] = $new_instance['searchid'];

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		echo $before_widget;
		$title    = empty( $instance['title'] ) ? ' ' : apply_filters( 'widget_title', $instance['title'] );
		$searchid = empty( $instance['searchid'] ) ? '' : $instance['searchid'];
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		};
		// WIDGET CODE GOES HERE
		if ( ! empty( $searchid ) ) {
			echo do_shortcode( "[wpdreams_ajaxsearchpro id=" . $searchid . "]" );
		}
		echo $after_widget;
	}
}

class AjaxSearchProLastSearchesWidget extends WP_Widget {
	static $instancenum;

	function __construct() {
		$widget_ops = array(
			'classname'   => 'AjaxSearchProLastSearchesWidget',
			'description' => 'Displays the last searches done by Ajax Search Pro.'
		);
		parent::__construct( 'AjaxSearchProLastSearchesWidget', 'Ajax Search Pro Last Searches', $widget_ops );
		AjaxSearchProLastSearchesWidget::$instancenum ++;
	}

	function form( $instance ) {
		global $wpdb;
		if ( isset( $wpdb->base_prefix ) ) {
			$_prefix = $wpdb->base_prefix;
		} else {
			$_prefix = $wpdb->prefix;
		}
		$searches  = $wpdb->get_results( "SELECT * FROM " . $_prefix . "ajaxsearchpro", ARRAY_A );
		$instance  = wp_parse_args( (array) $instance, array(
			'title' => '',
			'action' => 0,
			'number' => 10,
			'searchid' => 0,
			'targetid' => 0,
			'delimiter' => ','
		) );
		$title     = $instance['title'];
		$action    = $instance['action'];
		$number    = $instance['number'];
		$searchid  = $instance['searchid'];
		$targetid  = $instance['targetid'];
		$delimiter = $instance['delimiter'];
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">Title: <input class="widefat"
		                                                                            id="<?php echo $this->get_field_id( 'title' ); ?>"
		                                                                            name="<?php echo $this->get_field_name( 'title' ); ?>"
		                                                                            type="text"
		                                                                            value="<?php echo esc_attr( $title ); ?>"/></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'searchid' ); ?>">Select the search form: </label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'searchid' ); ?>"
			        id="<?php echo $this->get_field_id( 'searchid' ); ?>">
				<option value='0' <?php echo( ( esc_attr( $searchid ) == 0 ) ? "selected='selected'" : "''" ); ?>>All
				</option>
				<?php
				if ( is_array( $searches ) ) {
					foreach ( $searches as $search ) {
						echo "<option value='" . $search['id'] . "' " . ( ( esc_attr( $searchid ) == $search['id'] ) ? "selected='selected'" : "''" ) . ">" . $search['name'] . "</option>";
					}
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'action' ); ?>">Action on click: </label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'action' ); ?>"
			        id="<?php echo $this->get_field_id( 'action' ); ?>">
				<option value='0' <?php echo( ( esc_attr( $action ) == 0 ) ? "selected='selected'" : "''" ); ?>>Do
					Nothing
				</option>
				<option value='1' <?php echo( ( esc_attr( $action ) == 1 ) ? "selected='selected'" : "''" ); ?>>Redirect
					to Default search page
				</option>
				<option value='2' <?php echo( ( esc_attr( $action ) == 2 ) ? "selected='selected'" : "''" ); ?>>Do an
					Ajax search with the target
				</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>">Number: </label>
			<input type='text' class="widefat" name="<?php echo $this->get_field_name( 'number' ); ?>"
			       id="<?php echo $this->get_field_id( 'number' ); ?>"
			       value='<?php echo( isset( $number ) ? $number : '10' ); ?>'/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'delimiter' ); ?>">Delimiter: </label>
			<input type='text' class="widefat" name="<?php echo $this->get_field_name( 'delimiter' ); ?>"
			       id="<?php echo $this->get_field_id( 'delimiter' ); ?>"
			       value='<?php echo( isset( $delimiter ) ? $delimiter : ', ' ); ?>'/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'targetid' ); ?>">Target search form: </label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'targetid' ); ?>"
			        id="<?php echo $this->get_field_id( 'targetid' ); ?>">
				<?php
				if ( is_array( $searches ) ) {
					foreach ( $searches as $search ) {
						echo "<option value='" . $search['id'] . "' " . ( ( esc_attr( $targetid ) == $search['id'] ) ? "selected='selected'" : "''" ) . ">" . $search['name'] . "</option>";
					}
				}
				?>
			</select>
		</p>
	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['title']     = $new_instance['title'];
		$instance['searchid']  = $new_instance['searchid'];
		$instance['targetid']  = $new_instance['targetid'];
		$instance['action']    = $new_instance['action'];
		$instance['number']    = $new_instance['number'];
		$instance['delimiter'] = $new_instance['delimiter'];

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		echo $before_widget;
		$title       = empty( $instance['title'] ) ? ' ' : apply_filters( 'widget_title', $instance['title'] );
		$searchid    = empty( $instance['searchid'] ) ? '' : $instance['searchid'];
		$targetid    = empty( $instance['targetid'] ) ? '' : $instance['targetid'];
		$action      = ! isset( $instance['action'] ) ? '' : $instance['action'];
		$number      = empty( $instance['number'] ) ? '' : $instance['number'];
		$delimiter   = empty( $instance['delimiter'] ) ? '' : $instance['delimiter'];
		$instancenum = AjaxSearchProLastSearchesWidget::$instancenum ++;

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		};
		// WIDGET CODE GOES HERE
		global $wpdb;
		if ( isset( $wpdb->base_prefix ) ) {
			$_prefix = $wpdb->base_prefix;
		} else {
			$_prefix = $wpdb->prefix;
		}
		if ( $searchid == 0 ) {
			$keywords = $wpdb->get_results( 'SELECT * FROM ' . $_prefix . 'ajaxsearchpro_statistics GROUP BY keyword ORDER BY last_date DESC LIMIT ' . $number, ARRAY_A );
		} else {
			$keywords = $wpdb->get_results( 'SELECT * FROM ' . $_prefix . 'ajaxsearchpro_statistics WHERE search_id=' . $searchid . ' GROUP BY keyword ORDER BY last_date DESC LIMIT ' . $number, ARRAY_A );
		}
		$i = 1;

        $html_data = json_encode(array(
            'instance' => $instancenum,
            'action' => $action,
            'id' => $targetid
        ));

		?>
		<div class='ajaxsearchprotop ajaxsearhcprotop<?php echo $instancenum; ?> keywords' data-aspdata="<?php echo htmlentities($html_data, ENT_QUOTES, 'UTF-8'); ?>">
			<?php foreach ( $keywords as $keyword ) { ?>
				<a
				href='<?php echo get_bloginfo( 'wpurl' ) . "?s=" . $keyword['keyword']; ?>'><?php echo $keyword['keyword']; ?></a><?php echo( ( $i < count( $keywords ) ) ? $delimiter : "" ); ?>
				<?php $i ++; ?>
			<?php } ?>
		</div>
		<?php
		echo $after_widget;
	}
}

class AjaxSearchProTopSearchesWidget extends WP_Widget {
	static $instancenum;

	function __construct() {
		$widget_ops = array(
			'classname'   => 'AjaxSearchProTopSearchesWidget',
			'description' => 'Displays the Top searches done by Ajax Search Pro.'
		);
		parent::__construct( 'AjaxSearchProTopSearchesWidget', 'Ajax Search Pro Top Searches', $widget_ops );
		AjaxSearchProLastSearchesWidget::$instancenum ++;
	}

	function form( $instance ) {
		global $wpdb;
		if ( isset( $wpdb->base_prefix ) ) {
			$_prefix = $wpdb->base_prefix;
		} else {
			$_prefix = $wpdb->prefix;
		}
		$searches = $wpdb->get_results( "SELECT * FROM " . $_prefix . "ajaxsearchpro", ARRAY_A );
		//var_dump($instance);
		$instance  = wp_parse_args( (array) $instance, array(
			'title' => '',
			'action' => 0,
			'number' => 10,
			'searchid' => 0,
			'targetid' => 0,
			'delimiter' => ','
		) );
		$title     = $instance['title'];
		$action    = $instance['action'];
		$number    = $instance['number'];
		$searchid  = $instance['searchid'];
		$targetid  = $instance['targetid'];
		$delimiter = $instance['delimiter'];
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">Title: <input class="widefat"
		                                                                            id="<?php echo $this->get_field_id( 'title' ); ?>"
		                                                                            name="<?php echo $this->get_field_name( 'title' ); ?>"
		                                                                            type="text"
		                                                                            value="<?php echo esc_attr( $title ); ?>"/></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'searchid' ); ?>">Select the search form: </label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'searchid' ); ?>"
			        id="<?php echo $this->get_field_id( 'searchid' ); ?>">
				<option value='0' <?php echo( ( esc_attr( $searchid ) == 0 ) ? "selected='selected'" : "''" ); ?>>All
				</option>
				<?php
				if ( is_array( $searches ) ) {
					foreach ( $searches as $search ) {
						echo "<option value='" . $search['id'] . "' " . ( ( esc_attr( $searchid ) == $search['id'] ) ? "selected='selected'" : "''" ) . ">" . $search['name'] . "</option>";
					}
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'action' ); ?>">Action on click: </label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'action' ); ?>"
			        id="<?php echo $this->get_field_id( 'action' ); ?>">
				<option value='0' <?php echo( ( esc_attr( $action ) == 0 ) ? "selected='selected'" : "''" ); ?>>Do
					Nothing
				</option>
				<option value='1' <?php echo( ( esc_attr( $action ) == 1 ) ? "selected='selected'" : "''" ); ?>>Redirect
					to Default search page
				</option>
				<option value='2' <?php echo( ( esc_attr( $action ) == 2 ) ? "selected='selected'" : "''" ); ?>>Do an
					Ajax search with the target
				</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>">Number: </label>
			<input type='text' class="widefat" name="<?php echo $this->get_field_name( 'number' ); ?>"
			       id="<?php echo $this->get_field_id( 'number' ); ?>"
			       value='<?php echo( isset( $number ) ? $number : '10' ); ?>'/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'delimiter' ); ?>">Delimiter: </label>
			<input type='text' class="widefat" name="<?php echo $this->get_field_name( 'delimiter' ); ?>"
			       id="<?php echo $this->get_field_id( 'delimiter' ); ?>"
			       value='<?php echo( isset( $delimiter ) ? $delimiter : ', ' ); ?>'/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'targetid' ); ?>">Target search form: </label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'targetid' ); ?>"
			        id="<?php echo $this->get_field_id( 'targetid' ); ?>">
				<?php
				if ( is_array( $searches ) ) {
					foreach ( $searches as $search ) {
						echo "<option value='" . $search['id'] . "' " . ( ( esc_attr( $targetid ) == $search['id'] ) ? "selected='selected'" : "''" ) . ">" . $search['name'] . "</option>";
					}
				}
				?>
			</select>
		</p>
	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['title']     = $new_instance['title'];
		$instance['searchid']  = $new_instance['searchid'];
		$instance['targetid']  = $new_instance['targetid'];
		$instance['action']    = $new_instance['action'];
		$instance['number']    = $new_instance['number'];
		$instance['delimiter'] = $new_instance['delimiter'];

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		echo $before_widget;
		$title       = empty( $instance['title'] ) ? ' ' : apply_filters( 'widget_title', $instance['title'] );
		$searchid    = empty( $instance['searchid'] ) ? '' : $instance['searchid'];
		$targetid    = empty( $instance['targetid'] ) ? '' : $instance['targetid'];
		$action      = ! isset( $instance['action'] ) ? '' : $instance['action'];
		$number      = empty( $instance['number'] ) ? '' : $instance['number'];
		$delimiter   = empty( $instance['delimiter'] ) ? '' : $instance['delimiter'];
		$instancenum = AjaxSearchProLastSearchesWidget::$instancenum ++;

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		};
		// WIDGET CODE GOES HERE
		global $wpdb;
		if ( isset( $wpdb->base_prefix ) ) {
			$_prefix = $wpdb->base_prefix;
		} else {
			$_prefix = $wpdb->prefix;
		}
		if ( $searchid == 0 ) {
			$keywords = $wpdb->get_results( 'SELECT * FROM ' . $_prefix . 'ajaxsearchpro_statistics GROUP BY keyword ORDER BY num DESC LIMIT ' . $number, ARRAY_A );
		} else {
			$keywords = $wpdb->get_results( 'SELECT * FROM ' . $_prefix . 'ajaxsearchpro_statistics WHERE search_id=' . $searchid . ' GROUP BY keyword ORDER BY num DESC LIMIT ' . $number, ARRAY_A );
		}
		$i = 1;

        $html_data = json_encode(array(
            'instance' => $instancenum,
            'action' => $action,
            'id' => $targetid
        ));

		?>
		<div class='ajaxsearchprotop ajaxsearhcprotop<?php echo $instancenum; ?> keywords' data-aspdata="<?php echo htmlentities($html_data, ENT_QUOTES, 'UTF-8'); ?>">
			<?php foreach ( $keywords as $keyword ) { ?>
				<a
				href='<?php echo get_bloginfo( 'wpurl' ) . "?s=" . $keyword['keyword']; ?>'><?php echo $keyword['keyword']; ?></a><?php echo( ( $i < count( $keywords ) ) ? $delimiter : "" ); ?>
				<?php $i ++; ?>
			<?php } ?>
		</div>
		<?php
		echo $after_widget;
	}
}


add_action( 'widgets_init', create_function( '', 'return register_widget("AjaxSearchProWidget");' ) );
add_action( 'widgets_init', create_function( '', 'return register_widget("AjaxSearchProLastSearchesWidget");' ) );
add_action( 'widgets_init', create_function( '', 'return register_widget("AjaxSearchProTopSearchesWidget");' ) );