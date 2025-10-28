<div class="sidebar">

<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
<?php dynamic_sidebar( 'sidebar-1' ); ?>
<?php endif; ?>

<?php if (is_home()){ ?>
<?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
<?php dynamic_sidebar( 'sidebar-2' ); ?>
<?php endif; ?>
<?php 
$my_sidebars = array('sidebar-1', 'sidebar-2', 'sidebar-4', 'sidebar-5');
display_global_sidebar_notice($my_sidebars);
?>
<?php } ?>

<?php if (is_singular()){ ?>
<?php if ( is_active_sidebar( 'sidebar-3' ) ) : ?>
<?php dynamic_sidebar( 'sidebar-3' ); ?>
<?php endif; ?>
<?php 
$my_sidebars = array('sidebar-1', 'sidebar-3', 'sidebar-4', 'sidebar-5');
display_global_sidebar_notice($my_sidebars);
?>
<?php } ?>

<?php if ( is_active_sidebar( 'sidebar-4' ) ) : ?>
<?php dynamic_sidebar( 'sidebar-4' ); ?>
<?php endif; ?>

<?php if ( is_active_sidebar( 'sidebar-5' ) ) : ?>
<?php dynamic_sidebar( 'sidebar-5' ); ?>
<?php endif; ?>

</div>