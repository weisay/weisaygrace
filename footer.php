<?php include('includes/menu.php'); ?>
<?php include('includes/sideright.php'); ?>
<div class="clear"></div>
<div class="footer" id="footers">
<div class="footer-info container">
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
?> <?php bloginfo('name'); ?>. <span class="footer-hide">Powered by <a href="https://www.wordpress.org/" rel="external">WordPress</a>. Theme by <a href="https://www.weisay.com/" rel="external">Weisay</a>.</span></p>
</div>
<div class="footer-right">
<p><span class="footer-hide"><?php if (weisay_option('wei_beian') == 'display') : ?><a href="https://beian.miit.gov.cn/" rel="external nofollow"><?php echo weisay_option('wei_beianhao'); ?></a>. <?php endif; ?><?php if (weisay_option('wei_gwab') == 'display') : ?><a href="https://www.beian.gov.cn/portal/registerSystemInfo" rel="external nofollow"><?php echo weisay_option('wei_gwabh'); ?></a>.<?php endif; ?></span></p>
</div>
</div>
</div>
<?php wp_footer(); ?>
<?php if (is_single() || is_page() ) { ?>
<script src="<?php bloginfo('template_directory'); ?>/assets/js/activate-power-mode.min.js"></script>
<script>
	POWERMODE.colorful = true;
	POWERMODE.shake = false;
	document.body.addEventListener('input', POWERMODE);
</script>
<?php }?>
<?php if ( is_user_logged_in() ) : ?><style type="text/css">@media screen and (max-width:992px){#wpadminbar {display:none;}html {margin-top:0px !important;}}</style><?php endif; ?>
</body>
</html>