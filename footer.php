<?php require_once get_template_directory() . '/includes/menu.php'; ?>
<?php require_once get_template_directory() . '/includes/sidebar-mobile.php'; ?>
<div class="clear"></div>
<div class="footer" id="footers">
<div class="<?php $footerClass = weisay_option('wei_footlayout') == 'layout_c' ? 'footer-vertical' : 'footer-horizontal';echo $footerClass; ?> container">
<div class="footer-left">
<p>© <?php
$start_date = weisay_option('wei_websitedate');
if (!empty($start_date) && strtotime($start_date) !== false && strtotime($start_date) < time()) {
		$start_year = date('Y', strtotime($start_date));
		$current_year = date('Y');
	if ($start_year == $current_year) {
		echo $start_year;
	} else {
		echo $start_year . '-' . $current_year;
	}
} else {
	echo date('Y');
}
?> <?php bloginfo('name'); ?>. <span class="mobile-hide">Powered by <a href="https://wordpress.org/" rel="external">WordPress</a>. Theme by <a href="https://www.weisay.com/blog/wordpress-theme-weisay-grace.html?theme" rel="external">Weisay</a>. <?php if (weisay_option('wei_footlayout') == 'layout_c') : ?><?php echo weisay_option('wei_custom1'); ?><?php endif; ?></span></p>
<?php if (weisay_option('wei_footlayout') == 'layout_lr') : ?><?php echo weisay_option('wei_custom1') ? '<p class="mobile-hide">' . weisay_option('wei_custom1') . '</p>' : ''; ?><?php endif; ?>
</div>
<div class="footer-right">
<p><span class="mobile-hide"><?php if (weisay_option('wei_beian') == 'display') : ?><a href="https://beian.miit.gov.cn/" rel="external nofollow"><?php echo weisay_option('wei_beianhao'); ?></a>. <?php endif; ?><?php if (weisay_option('wei_gwab') == 'display') : ?><a href="https://www.beian.gov.cn/portal/registerSystemInfo" rel="external nofollow"><?php echo weisay_option('wei_gwabh'); ?></a>.<?php endif; ?> <?php if (weisay_option('wei_footlayout') == 'layout_c') : ?><?php echo weisay_option('wei_custom2'); ?><?php endif; ?></span></p>
<?php if (weisay_option('wei_footlayout') == 'layout_lr') : ?><?php echo weisay_option('wei_custom2') ? '<p class="mobile-hide">' . weisay_option('wei_custom2') . '</p>' : ''; ?><?php endif; ?>
</div>
</div>
</div>
<?php if (is_single() || is_page() ) { ?>
<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/activate-power-mode.min.js'); ?>"></script>
<script type="text/javascript">POWERMODE.colorful = true; POWERMODE.shake = false; document.body.addEventListener('input', POWERMODE);</script>
<?php }?>
<?php if ( is_user_logged_in() ) : ?>
<style type="text/css">@media screen and (max-width:992px){#wpadminbar {display:none;}html {margin-top:0px !important;}}</style>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>