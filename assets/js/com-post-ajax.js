/**
 * WordPress jQuery-Ajax-Comments v1.3 by Willin Kan.
 * URI: http://kan.willin.org/?p=1271
 */
var i = 0, got = -1, len = document.getElementsByTagName('script').length;
while ( i <= len && got == -1){
	var js_url = document.getElementsByTagName('script')[i].src,
			got = js_url.indexOf('com-post-ajax.js'); i++ ;
}
var edit_mode = '1', // 再編輯模式 ( '1'=開; '0'=不開 )
		ajax_php_url = js_url.replace('assets/js/com-post-ajax.js','com-post-ajax.php'),
		wp_url = js_url.substr(0, js_url.indexOf('wp-content')),
		pic_sb = wp_url + 'wp-admin/images/wpspin_light.gif', // 提交 icon
		pic_no = wp_url + 'wp-admin/images/no.png',      // 錯誤 icon
		pic_ys = wp_url + 'wp-admin/images/yes.png',     // 成功 icon
		txt1 = '<div id="loading" class="comment-tips"><img src="' + pic_sb + '" style="vertical-align:middle;" alt=""/> 正在提交, 请稍候...</div>',
		txt2 = '<div id="error" class="comment-tips">#</div>',
		txt3 = '"> <div id="edita"><img src="' + pic_ys + '" style="vertical-align:middle;" alt=""/> 提交成功',
		edt1 = '，刷新页面之前你可以 <a rel="nofollow" class="comment-reply-link comment-reply-link-edit" href="#edit" onclick=\'return addComment.moveForm("',
		edt2 = ')\'>再次编辑</a></div> ',
		cancel_edit = '取消编辑',
		edit, num = 1, comm_array=[]; comm_array.push('');

jQuery(document).ready(function($) {
		$comments = $('#comments-title'); // 評論數的 ID
		$cancel = $('#cancel-comment-reply-link'); cancel_text = $cancel.text();
		$submit = $('#commentform #submit'); $submit.attr('disabled', false);
		$('#comment').after( txt1 + txt2 ); $('#loading').hide(); $('#error').hide();
		$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');

/** submit */
$('#commentform').submit(function() {
		$('#loading').slideDown();
		$submit.attr('disabled', true).fadeTo('slow', 0.5);
		if ( edit ) $('#comment').after('<input type="text" name="edit_id" id="edit_id" value="' + edit + '" style="display:none;" />');

/** Ajax */
	$.ajax( {
		url: ajax_php_url,
		data: $(this).serialize(),
		type: $(this).attr('method'),

		error: function(request) {
			$('#loading').slideUp();
			$('#error').slideDown().html('<img src="' + pic_no + '" style="vertical-align:middle;" alt=""/> ' + request.responseText);
			setTimeout(function() {$submit.attr('disabled', false).fadeTo('slow', 1); $('#error').slideUp();}, 3000);
			},

		success: function(data) {
			$('#loading').hide();
			comm_array.push($('#comment').val());
			$('textarea').each(function() {this.value = ''});
			var t = addComment, cancel = t.I('cancel-comment-reply-link'), temp = t.I('wp-temp-form-div'), respond = t.I(t.respondId), post = t.I('comment_post_ID').value, parent = t.I('comment_parent').value;

// comments
		if ( ! edit && $comments.length ) {
			n = parseInt($comments.text().match(/\d+/));
			$comments.text($comments.text().replace( n, n + 1 ));
		}

// show comment
		new_htm = '" id="new_comm_' + num + '"></';
		new_htm = ( parent == '0' ) ? ('\n<ol style="clear:both;" class="comment-list' + new_htm + 'ol>') : ('\n<ul class="children' + new_htm + 'ul>');

		ok_htm = '\n<span id="success_' + num + txt3;
		if ( edit_mode == '1' ) {
			div_ = (document.body.innerHTML.indexOf('div-comment-') == -1) ? '' : ((document.body.innerHTML.indexOf('li-comment-') == -1) ? 'div-' : '');
			ok_htm = ok_htm.concat(edt1, div_, 'comment-', parent, '", "', parent, '", "respond", "', post, '", ', num, edt2);
		}
		ok_htm += '</span><span></span>\n';

		$('#respond').before(new_htm);
		$('#new_comm_' + num).hide().append(data);
		$('#new_comm_' + num + ' li').append(ok_htm);
		$('#new_comm_' + num).fadeIn(4000);

		$body.animate( { scrollTop: $('#new_comm_' + num).offset().top - 200}, 900);
		countdown(); num++ ; edit = ''; $('*').remove('#edit_id');
		cancel.style.display = 'none';
		cancel.onclick = null;
		t.I('comment_parent').value = '0';
		if ( temp && respond ) {
			temp.parentNode.insertBefore(respond, temp);
			temp.parentNode.removeChild(temp)
		}
		}
	}); // end Ajax
	return false;
}); // end submit

/** comment-reply.dev.js */
addComment = {
	moveForm : function(commId, parentId, respondId, postId, num, replyTo) {
		var t = this, div, comm = t.I(commId), respond = t.I(respondId), cancel = t.I('cancel-comment-reply-link'), parent = t.I('comment_parent'), post = t.I('comment_post_ID');
		if ( edit ) exit_prev_edit();
		num ? (
			t.I('comment').value = comm_array[num],
			edit = t.I('new_comm_' + num).innerHTML.match(/(comment-)(\d+)/)[2],
			$new_sucs = $('#success_' + num ), $new_sucs.hide(),
			$new_comm = $('#new_comm_' + num ), $new_comm.hide(),
			$cancel.text(cancel_edit)
		) : $cancel.text(cancel_text);

		t.respondId = respondId;
		postId = postId || false;

		if ( !t.I('wp-temp-form-div') ) {
			div = document.createElement('div');
			div.id = 'wp-temp-form-div';
			div.style.display = 'none';
			respond.parentNode.insertBefore(div, respond)
		}

		!comm ? (
			temp = t.I('wp-temp-form-div'),
			t.I('comment_parent').value = '0',
			temp.parentNode.insertBefore(respond, temp),
			temp.parentNode.removeChild(temp)
		) : comm.parentNode.insertBefore(respond, comm.nextSibling);

		$body.animate( { scrollTop: $('#respond').offset().top - 180 }, 400);

		replyTo = replyTo || false;

		if (replyTo) {
			if (!$("#respond #reply-title").data("original-title"))
				$("#respond #reply-title").data("original-title", $("#respond #reply-title").html());
			$("#respond #reply-title").html(replyTo);
		}

		if ( post && postId ) post.value = postId;
		parent.value = parentId;
		cancel.style.display = '';

		cancel.onclick = function() {
			if ( edit ) exit_prev_edit();
			var t = addComment, temp = t.I('wp-temp-form-div'), respond = t.I(t.respondId);

			t.I('comment_parent').value = '0';
			if ( temp && respond ) {
				temp.parentNode.insertBefore(respond, temp);
				temp.parentNode.removeChild(temp);
			}
			this.style.display = 'none';
			this.onclick = null;

			if ($("#respond #reply-title").data("original-title"))
				$("#respond #reply-title").html($("#respond #reply-title").data("original-title"));

			return false;
		};

		try { t.I('comment').focus(); }
		catch(e) {}

		return false;
	},

	I : function(e) {
		return document.getElementById(e);
	}
}; // end addComment

function exit_prev_edit() {
		$new_comm.show(); $new_sucs.show();
		$('textarea').each(function() {this.value = ''});
		edit = '';
}

var wait = 15, submit_val = $submit.val();
function countdown() {
	if ( wait > 0 ) {
		$submit.val(wait); wait--; setTimeout(countdown, 1000);
	} else {
		$submit.val(submit_val).attr('disabled', false).fadeTo('slow', 1);
		wait = 15;
	}
}
});// end jQ

function post_karma(comment_id, action_url, elem) {
	elem.innerHTML = "<i class='iconfont hearticon'>&#xe63a;</i>";
	var origin_karma = elem.getAttribute("data-karma");
	var new_karma = Number(!parseInt(origin_karma));
	var formData = new FormData();
	formData.append('comment_id', comment_id);
	formData.append('comment_karma', new_karma);
	$.ajax({
		type: 'POST',
		url: action_url,
		data: new URLSearchParams(formData).toString(),
		dataType: 'json',
		timeout: 10000,
		beforeSend: function (xhr) {
		}
	}).done(function (data) {
		if (data.code == 200) {
			elem.setAttribute("data-karma", new_karma);
		} else {
			alert('设置失败');
		}
	}).fail(function (jqXHR, textStatus, errorThrown) {
		alert('设置失败(原因：\'' + textStatus + '\')，请稍后再试');
	}).always(function (jqXHR, textStatus) {
		if (elem.getAttribute("data-karma") == '0') {
			elem.innerHTML = "<i class='iconfont hearticon' title='加入走心'>&#xe602;</i>";
		} else {
			elem.innerHTML = "<i class='iconfont hearticon' title='取消走心'>&#xe601;</i>";
		}
	});
	return false;
}