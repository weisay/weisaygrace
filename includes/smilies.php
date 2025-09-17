<script type="text/javascript">
/* <![CDATA[ */
	function grin(tag) {
		var myField;
		tag = ' ' + tag + ' ';
		if (document.getElementById('comment') && document.getElementById('comment').type == 'textarea') {
			myField = document.getElementById('comment');
		} else {
			return false;
		}
		if (document.selection) {
			myField.focus();
			sel = document.selection.createRange();
			sel.text = tag;
			myField.focus();
		}
		else if (myField.selectionStart || myField.selectionStart == '0') {
			var startPos = myField.selectionStart;
			var endPos = myField.selectionEnd;
			var cursorPos = endPos;
			myField.value = myField.value.substring(0, startPos)
				+ tag
				+ myField.value.substring(endPos, myField.value.length);
			cursorPos += tag.length;
			myField.focus();
			myField.selectionStart = cursorPos;
			myField.selectionEnd = cursorPos;
		}
		else {
			myField.value += tag;
			myField.focus();
		}
	}
/* ]]> */
</script>
<a href="javascript:grin(':?:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_question.gif'); ?>" alt="?" /></a>
<a href="javascript:grin(':razz:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_razz.gif'); ?>" alt="razz" /></a>
<a href="javascript:grin(':sad:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_sad.gif'); ?>" alt="sad" /></a>
<a href="javascript:grin(':evil:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_evil.gif'); ?>" alt="evil" /></a>
<a href="javascript:grin(':!:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_exclaim.gif'); ?>" alt="!" /></a>
<a href="javascript:grin(':smile:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_smile.gif'); ?>" alt="smile" /></a>
<a href="javascript:grin(':oops:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_redface.gif'); ?>" alt="oops" /></a>
<a href="javascript:grin(':grin:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_biggrin.gif'); ?>" alt="grin" /></a>
<a href="javascript:grin(':eek:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_surprised.gif'); ?>" alt="eek" /></a>
<a href="javascript:grin(':shock:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_eek.gif'); ?>" alt="shock" /></a>
<a href="javascript:grin(':???:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_confused.gif'); ?>" alt="???" /></a>
<a href="javascript:grin(':cool:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_cool.gif'); ?>" alt="cool" /></a>
<a href="javascript:grin(':lol:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_lol.gif'); ?>" alt="lol" /></a>
<a href="javascript:grin(':mad:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_mad.gif'); ?>" alt="mad" /></a>
<a href="javascript:grin(':twisted:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_twisted.gif'); ?>" alt="twisted" /></a>
<a href="javascript:grin(':roll:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_rolleyes.gif'); ?>" alt="roll" /></a>
<a href="javascript:grin(':wink:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_wink.gif'); ?>" alt="wink" /></a>
<a href="javascript:grin(':idea:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_idea.gif'); ?>" alt="idea" /></a>
<a href="javascript:grin(':arrow:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_arrow.gif'); ?>" alt="arrow" /></a>
<a href="javascript:grin(':neutral:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_neutral.gif'); ?>" alt="neutral" /></a>
<a href="javascript:grin(':cry:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_cry.gif'); ?>" alt="cry" /></a>
<a href="javascript:grin(':mrgreen:')"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/smilies/icon_mrgreen.gif'); ?>" alt="mrgreen" /></a>
<br />