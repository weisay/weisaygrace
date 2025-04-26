<div class="thumbnail">
<a href="<?php the_permalink() ?>" rel="nofollow" title="<?php the_title(); ?>">
<?php if (has_post_thumbnail()) : ?>
<?php the_post_thumbnail('thumbnail'); ?>
<?php elseif (get_post_meta($post->ID, 'thumbnail', true)) : ?>
<?php $image = get_post_meta($post->ID, 'thumbnail', true); ?>
<img class="diagram" src="<?php echo $image; ?>" alt="<?php the_title(); ?>" itemprop="image" loading="lazy" />
<?php elseif (function_exists('catch_first_image') && ($first_image = catch_first_image())) : ?>
<img class="diagram" src="<?php echo esc_url($first_image); ?>" alt="<?php the_title(); ?>" itemprop="image" loading="lazy" />
<?php else: ?>
<img class="diagram" src="<?php bloginfo('template_directory'); ?>/assets/images/random/<?php echo rand(1,30)?>.jpg" alt="<?php the_title(); ?>" itemprop="image" loading="lazy" />
<?php endif; ?>
</a>
</div>