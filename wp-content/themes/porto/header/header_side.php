<?php
global $porto_settings, $porto_layout;
?>
<header id="header" class="header-9 <?php echo $porto_settings['search-size'] ?> sticky-menu-header">
    <div class="header-main<?php if ($porto_settings['show-minicart'] && class_exists('WooCommerce')) echo ' show-minicart' ?>">

        <div class="side-top">
            <div class="container">
                <?php
                // show currency and view switcher
                $currency_switcher = porto_currency_switcher();
                $view_switcher = porto_view_switcher();
                $minicart = porto_minicart();

                echo $currency_switcher;

                echo $view_switcher;

                echo $minicart;
                ?>
            </div>
        </div>

        <div class="container">
            <div class="header-left">
                <?php // show logo ?>
                <?php if ( is_front_page() && is_home() ) : ?><h1 class="logo"><?php else : ?><div class="logo"><?php endif; ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?> - <?php bloginfo( 'description' ); ?>" rel="home">
                        <?php if($porto_settings['logo'] && $porto_settings['logo']['url']) {
                            $logo_width = '';
                            $logo_height = '';
                            if ( isset($porto_settings['logo-retina-width']) && isset($porto_settings['logo-retina-height']) && $porto_settings['logo-retina-width'] && $porto_settings['logo-retina-height'] ) {
                                $logo_width = (int)$porto_settings['logo-retina-width'];
                                $logo_height = (int)$porto_settings['logo-retina-height'];
                            }

                            echo '<img class="img-responsive standard-logo"'.($logo_width?' width="'.$logo_width.'"':'').($logo_height?' height="'.$logo_height.'"':'').' src="' . esc_url(str_replace( array( 'http:', 'https:' ), '', $porto_settings['logo']['url'])) . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" />';

                            $retina_logo = '';
                            if (isset($porto_settings['logo-retina']) && isset($porto_settings['logo-retina']['url'])) {
                                $retina_logo = $porto_settings['logo-retina']['url'];
                            }

                            if ($retina_logo) {
                                echo '<img class="img-responsive retina-logo"'.($logo_width?' width="'.$logo_width.'"':'').($logo_height?' height="'.$logo_height.'"':'').' src="' . esc_url(str_replace( array( 'http:', 'https:' ), '', $retina_logo)) . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" style="max-height:'.$logo_height.'px;display:none;" />';
                            } else {
                                echo '<img class="img-responsive retina-logo"'.($logo_width?' width="'.$logo_width.'"':'').($logo_height?' height="'.$logo_height.'"':'').' src="' . esc_url(str_replace( array( 'http:', 'https:' ), '', $porto_settings['logo']['url'])) . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" style="display:none;" />';
                            }
                        } else {
                            bloginfo( 'name' );
                        } ?>
                    </a>
                <?php if ( is_front_page() && is_home() ) : ?></h1><?php else : ?></div><?php endif; ?>
            </div>

            <div class="header-center">
                <?php
                $sidebar_menu = porto_header_side_menu();
                if ($sidebar_menu) :
                    echo $sidebar_menu;
                endif;
                ?>

                <?php
                // show search form
                echo porto_search_form();
                // show mobile toggle
                ?>
                <a class="mobile-toggle"><i class="fa fa-reorder"></i></a>

                <?php
                // show top navigation
                $top_nav = porto_mobile_top_navigation();
                echo $top_nav;
                ?>
            </div>

            <div class="header-right">
                <div class="side-bottom">
                    <?php
                    // show contact info and mini cart
                    $contact_info = $porto_settings['header-contact-info'];

                    if ($contact_info)
                        echo '<div class="header-contact">' . force_balance_tags($contact_info) . '</div>';
                    ?>

                    <?php
                    // show social links
                    echo porto_header_socials();
                    ?>

                    <?php
                    // show copyright
                    $copyright = $porto_settings['header-copyright'];

                    if ($copyright)
                        echo '<div class="header-copyright">' . force_balance_tags($copyright) . '</div>';
                    ?>
                </div>
            </div>
        </div>
    </div>
</header>