<?php
remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); //解决4.2版本部分主题大量404请求问题
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' ); //解决后台404请求，V5.0+已无
remove_action( 'init', 'smilies_init', 5 ); //移除4.2版本表情钩子
remove_action( 'wp_print_styles', 'print_emoji_styles' ); //移除4.2版本前台表情样式钩子
remove_action( 'admin_print_styles', 'print_emoji_styles' ); //移除4.2版本后台表情样式钩子，V5.0+已无
remove_action( 'the_content_feed', 'wp_staticize_emoji' ); //移除4.2 emoji相关钩子，V5.0+已无
remove_action( 'comment_text_rss', 'wp_staticize_emoji' ); //移除4.2 emoji相关钩子，V5.0+已无
//remove_action( 'comment_text', 'convert_smilies', 20 ); //评论表情相关钩子
//remove_action( 'the_content', 'convert_smilies' ); //文章表情相关钩子
//remove_action( 'the_excerpt', 'convert_smilies' ); //摘要表情相关钩子

add_action( 'comment_text', 'convert_smilies_diy', 20 ); //自定义表情相关钩子
add_action( 'the_content', 'convert_smilies_diy' ); //自定义表情相关钩子
add_action( 'the_excerpt', 'convert_smilies_diy' ); //自定义表情相关钩子
add_action( 'init', 'smilies_init_old', 5 ); //自定义表情钩子
if ( !is_admin() ) { //后台不禁止
add_filter( 'emoji_svg_url', '__return_false' ); //移除s.w.org
}

//原函数 smilies_init 位于wp-includes/functions.php
function smilies_init_old() {
	global $wpsmiliestrans, $wp_smiliessearch;
	// don't bother setting up smilies if they are disabled
	if ( !get_option( 'use_smilies' ) )
		return;
	if ( !isset( $wpsmiliestrans ) ) {
		$wpsmiliestrans = array(
		':mrgreen:' => 'icon_mrgreen.gif',
		':neutral:' => 'icon_neutral.gif',
		':twisted:' => 'icon_twisted.gif',
		':arrow:' => 'icon_arrow.gif',
		':shock:' => 'icon_surprised.gif',	//与eek调整
		':smile:' => 'icon_smile.gif',
		':???:' => 'icon_confused.gif',
		':cool:' => 'icon_cool.gif',
		':evil:' => 'icon_evil.gif',
		':grin:' => 'icon_biggrin.gif',
		':idea:' => 'icon_idea.gif',
		':oops:' => 'icon_redface.gif',
		':razz:' => 'icon_razz.gif',
		':roll:' => 'icon_rolleyes.gif',
		':wink:' => 'icon_wink.gif',
		':cry:' => 'icon_cry.gif',
		':eek:' => 'icon_eek.gif',	//与shock调整
		':lol:' => 'icon_lol.gif',
		':mad:' => 'icon_mad.gif',
		':sad:' => 'icon_sad.gif',
		'8-)' => 'icon_cool.gif',
		'8-O' => 'icon_eek.gif',
		':-(' => 'icon_sad.gif',
		':-)' => 'icon_smile.gif',
		':-?' => 'icon_confused.gif',
		':-D' => 'icon_biggrin.gif',
		':-P' => 'icon_razz.gif',
		':-o' => 'icon_surprised.gif',
		':-x' => 'icon_mad.gif',
		':-|' => 'icon_neutral.gif',
		';-)' => 'icon_wink.gif',
	// This one transformation breaks regular text with frequency.
	//	'8)' => 'icon_cool.gif',
		'8O' => 'icon_eek.gif',
		':(' => 'icon_sad.gif',
		':)' => 'icon_smile.gif',
		':?' => 'icon_confused.gif',
		':D' => 'icon_biggrin.gif',
		':P' => 'icon_razz.gif',
		':o' => 'icon_surprised.gif',
		':x' => 'icon_mad.gif',
		':|' => 'icon_neutral.gif',
		';)' => 'icon_wink.gif',
		':!:' => 'icon_exclaim.gif',
		':?:' => 'icon_question.gif',
		);
	}
	if (count($wpsmiliestrans) == 0) {
		return;
	}
	/*
	 * NOTE: we sort the smilies in reverse key order. This is to make sure
	 * we match the longest possible smilie (:???: vs :?) as the regular
	 * expression used below is first-match
	 */
	krsort($wpsmiliestrans);
	$spaces = wp_spaces_regexp();
	// Begin first "subpattern"
	$wp_smiliessearch = '/(?<=' . $spaces . '|^)';
	$subchar = '';
	foreach ( (array) $wpsmiliestrans as $smiley => $img ) {
		$firstchar = substr($smiley, 0, 1);
		$rest = substr($smiley, 1);
		// new subpattern?
		if ($firstchar != $subchar) {
			if ($subchar != '') {
				$wp_smiliessearch .= ')(?=' . $spaces . '|$)'; // End previous "subpattern"
				$wp_smiliessearch .= '|(?<=' . $spaces . '|^)'; // Begin another "subpattern"
			}
			$subchar = $firstchar;
			$wp_smiliessearch .= preg_quote($firstchar, '/') . '(?:';
		} else {
			$wp_smiliessearch .= '|';
		}
		$wp_smiliessearch .= preg_quote($rest, '/');
	}

	$wp_smiliessearch .= ')(?=' . $spaces . '|$)/m';
}

//原函数 convert_smilies 位于wp-includes/formatting.php
function convert_smilies_diy( $text ) {
	global $wp_smiliessearch;
	$output = '';
	if ( get_option( 'use_smilies' ) && ! empty( $wp_smiliessearch ) ) {
		// HTML loop taken from texturize function, could possible be consolidated
		$textarr = preg_split( '/(<.*>)/U', $text, -1, PREG_SPLIT_DELIM_CAPTURE ); // capture the tags as well as in between
		$stop = count( $textarr );// loop stuff
		// Ignore proessing of specific tags
		$tags_to_ignore = 'code|pre|style|script|textarea';
		$ignore_block_element = '';
		for ( $i = 0; $i < $stop; $i++ ) {
			$content = $textarr[$i];
			// If we're in an ignore block, wait until we find its closing tag
			if ( '' == $ignore_block_element && preg_match( '/^<(' . $tags_to_ignore . ')>/', $content, $matches ) ) {
				$ignore_block_element = $matches[1];
			}
			// If it's not a tag and not in ignore block
			if ( '' == $ignore_block_element && strlen( $content ) > 0 && '<' != $content[0] ) {
				$content = preg_replace_callback( $wp_smiliessearch, 'translate_smiley_diy', $content );
			}
			// did we exit ignore block
			if ( '' != $ignore_block_element && '</' . $ignore_block_element . '>' == $content ) {
				$ignore_block_element = '';
			}
			$output .= $content;
		}
	} else {
		// return default text.
		$output = $text;
	}
	return $output;
}
//原函数 translate_smiley 位于wp-includes/formatting.php
function translate_smiley_diy( $matches ) {
	global $wpsmiliestrans;
	if ( count( $matches ) == 0 )
		return '';
	$smiley = trim( reset( $matches ) );
	$img = $wpsmiliestrans[ $smiley ];
	$matches = array();
	$ext = preg_match( '/\.([^.]+)$/', $img, $matches ) ? strtolower( $matches[1] ) : false;
	$image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );
	// Don't convert smilies that aren't images - they're probably emoji.
	if ( ! in_array( $ext, $image_exts ) ) {
		return $img;
	}
	/**
	 * Filter the Smiley image URL before it's used in the image element.
	 *
	 * @since 2.9.0
	 *
	 * @param string $smiley_url URL for the smiley image.
	 * @param string $img        Filename for the smiley image.
	 * @param string $site_url   Site URL, as returned by site_url().
	 */
	 //请注意！已将表情路径定义到主题目录下的 images/smilies 文件夹
	$src_url = apply_filters( 'smilies_src', esc_url(get_template_directory_uri() . '/assets/images/smilies/' . $img), $img, site_url() );
	return sprintf( '<img src="%s" alt="%s" class="wp-smiley" style="/*height: 1em; max-height: 1em;*/" />', esc_url( $src_url ), esc_attr( $smiley ) );
	}
?>