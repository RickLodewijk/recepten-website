<?php
/*
 * Template part for a single section inside the ACF 'sections' repeater.
 * Expected sub fields: 'title' (text), 'content' (wysiwyg/textarea), 'image' (image)
 */
$title = get_sub_field('title');
$content = get_sub_field('content');
$image = get_sub_field('image');
?>
<section class="rick-section">
  <?php if ( $title ) : ?>
    <h2><?php echo esc_html( $title ); ?></h2>
  <?php endif; ?>

  <?php if ( $image ) :
    echo wp_get_attachment_image( $image, 'medium', false, array('class'=>'section-image') );
  endif; ?>

  <?php if ( $content ) : ?>
    <div class="section-content"><?php echo wp_kses_post( wpautop( $content ) ); ?></div>
  <?php endif; ?>
</section>
