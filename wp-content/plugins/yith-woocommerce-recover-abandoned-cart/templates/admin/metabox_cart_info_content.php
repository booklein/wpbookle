<?php
/**
 * YITH WooCommerce Recover Abandoned Cart Content metabox template
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author  Yithemess
 */

?>
<table class="yith-ywrac-info-cart" cellspacing="20">
    <tbody>
        <tr>
            <th><?php _e('Cart Status:','ywrac') ?></th>
            <td><span class="<?php echo $status ?>"><?php echo $status ?></span></td>
        </tr>

        <tr>
            <th><?php _e('Cart Last Update:','ywrac') ?></th>
            <td><?php echo $last_update ?></td>
        </tr>

        <tr>
            <th><?php _e('User:','ywrac') ?></th>
            <td><?php echo $user->display_name ?></td>
        </tr>

        <tr>
            <th><?php _e('User email:','ywrac') ?></th>
            <td><?php echo '<a href="mailto:'.$user->user_email.'">'.$user->user_email.'</a>' ?></td>
        </tr>



    </tbody>
</table>