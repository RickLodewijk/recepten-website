<?php
get_header();

if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        $baktip_tekst = get_field('baktip_tekst');
        $afbeelding = get_the_post_thumbnail_url(get_the_ID(), 'large');
        ?>
        <div class="baktip-container">
            <div class="header<?php echo $afbeelding ? ' has-image' : ''; ?>">
                <div class="content">
                    <p class="meta-info">Handige Baktip</p>
                    <h1><?php the_title(); ?></h1>
                </div>

                <?php if ( $afbeelding ) : ?>
                    <div class="image-wrapper">
                        <img class="image" src="<?php echo esc_url( $afbeelding ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                    </div>
                <?php endif; ?>
            </div>

            <?php if ( $baktip_tekst ) : ?>
                <div class="baktip-content">
                    <?php echo wp_kses_post( wpautop( $baktip_tekst ) ); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    endwhile;
endif;

get_footer();