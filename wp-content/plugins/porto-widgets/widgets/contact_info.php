<?php
add_action('widgets_init', 'porto_contact_info_load_widgets');

function porto_contact_info_load_widgets() {
    register_widget('Porto_Contact_Info_Widget');
}

class Porto_Contact_Info_Widget extends WP_Widget {

    public function __construct() {

        $widget_ops = array('classname' => 'contact-info', 'description' => __('Add contact information.', 'porto-widgets'));

        $control_ops = array('id_base' => 'contact-info-widget');

        parent::__construct('contact-info-widget', __('Porto: Contact Info', 'porto-widgets'), $widget_ops, $control_ops);
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        $contact_before = $instance['contact_before'];
        $address = $instance['address'];
        $phone = $instance['phone'];
        $email = $instance['email'];
        $working = $instance['working'];
        $contact_after = $instance['contact_after'];

        echo $before_widget;

        if ($title) {
            echo $before_title . $title . $after_title;
        }
        ?>
        <div class="contact-info">
            <?php if ($contact_before) : ?><p><?php echo force_balance_tags($contact_before) ?></p><?php endif; ?>
            <ul class="contact-details">
                <?php if ($address) : ?><li><i class="fa fa-map-marker"></i> <strong><?php _e('Address', 'porto-widgets') ?>:</strong> <span><?php echo force_balance_tags($address) ?></span></li><?php endif; ?>
                <?php if ($phone) : ?><li><i class="fa fa-phone"></i> <strong><?php _e('Phone', 'porto-widgets') ?>:</strong> <span><?php echo force_balance_tags($phone) ?></span></li><?php endif; ?>
                <?php if ($email) : ?><li><i class="fa fa-envelope"></i> <strong><?php _e('Email', 'porto-widgets') ?>:</strong> <span><a href="mailto:<?php echo esc_attr($email) ?>"><?php echo force_balance_tags($email) ?></a></span></li><?php endif; ?>
                <?php if ($working) : ?><li><i class="fa fa-clock-o"></i> <strong><?php _e('Working Days/Hours', 'porto-widgets') ?>:</strong> <span><?php echo force_balance_tags($working) ?></span></li><?php endif; ?>
            </ul>
            <?php if ($contact_after) : ?><p><?php echo force_balance_tags($contact_after) ?></p><?php endif; ?>
        </div>

        <?php
        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        $instance['contact_before'] = $new_instance['contact_before'];
        $instance['address'] = $new_instance['address'];
        $instance['phone'] = $new_instance['phone'];
        $instance['email'] = $new_instance['email'];
        $instance['working'] = $new_instance['working'];
        $instance['contact_after'] = $new_instance['contact_after'];

        return $instance;
    }

    function form($instance) {
        $defaults = array('title' => __('Contact Us', 'porto-widgets'), 'contact_before' => '', 'address' => '1234 Street Name, City Name, Country Name', 'phone' => '(123) 456-7890', 'email' => 'mail@example.com', 'working' => 'Mon - Sun / 9:00 AM - 8:00 PM', 'contact_after' => '');
        $instance = wp_parse_args((array) $instance, $defaults); ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                <strong><?php echo __('Title', 'porto-widgets') ?>:</strong>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset($instance['title'])) echo $instance['title']; ?>" />
            </label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('contact_before'); ?>">
                <strong><?php echo __('Before Description', 'porto-widgets') ?>:</strong>
                <textarea class="widefat" id="<?php echo $this->get_field_id('contact_before'); ?>" name="<?php echo $this->get_field_name('contact_before'); ?>"><?php if (isset($instance['contact_before'])) echo $instance['contact_before']; ?></textarea>
            </label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('address'); ?>">
                <strong><?php echo __('Address', 'porto-widgets') ?>:</strong>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('address'); ?>" name="<?php echo $this->get_field_name('address'); ?>" value="<?php if (isset($instance['address'])) echo $instance['address']; ?>" />
            </label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('phone'); ?>">
                <strong><?php echo __('Phone', 'porto-widgets') ?>:</strong>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('phone'); ?>" name="<?php echo $this->get_field_name('phone'); ?>" value="<?php if (isset($instance['phone'])) echo $instance['phone']; ?>" />
            </label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('email'); ?>">
                <strong><?php echo __('Email', 'porto-widgets') ?>:</strong>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name('email'); ?>" value="<?php if (isset($instance['email'])) echo $instance['email']; ?>" />
            </label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('working'); ?>">
                <strong><?php echo __('Working Days/Hours', 'porto-widgets') ?>:</strong>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('working'); ?>" name="<?php echo $this->get_field_name('working'); ?>" value="<?php if (isset($instance['working'])) echo $instance['working']; ?>" />
            </label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('contact_after'); ?>">
                <strong><?php echo __('After Description', 'porto-widgets') ?>:</strong>
                <textarea class="widefat" id="<?php echo $this->get_field_id('contact_after'); ?>" name="<?php echo $this->get_field_name('contact_after'); ?>"><?php if (isset($instance['contact_after'])) echo $instance['contact_after']; ?></textarea>
            </label>
        </p>
    <?php
    }
}
?>