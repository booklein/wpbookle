<?php
global $porto_settings, $post;

$portfolio_link = get_post_meta($post->ID, 'portfolio_link', true);
$show_external_link = $porto_settings['portfolio-external-link'];

if (has_post_thumbnail()) :
    ?>
    <div class="portfolio-item-small">
        <?php
        $attachment_id = get_post_thumbnail_id();
        $attachment_thumb = porto_get_attachment($attachment_id, 'widget-thumb-medium');
        ?>
        <div class="portfolio-image thumbnail">
            <a href="<?php if ($show_external_link && $portfolio_link) echo $portfolio_link; else the_permalink() ?>">
                <img width="<?php echo $attachment_thumb['width'] ?>" height="<?php echo $attachment_thumb['height'] ?>" src="<?php echo $attachment_thumb['src'] ?>" alt="<?php echo $attachment_thumb['alt'] ?>" />
            </a>
        </div>
    </div>
<?php
endif;
