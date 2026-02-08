/**
 * WordPress jQuery-Ajax-Comments v1.3 by Willin Kan. Optimized Code by Weisay.
 * URI: http://kan.willin.org/?p=1271
 */
var scripts = document.getElementsByTagName('script');
var i = 0, got = -1, len = scripts.length;
while ( i <= len && got == -1){
	var js_url = scripts[i].src,
		got = js_url.indexOf('com-post-ajax.js'); i++ ;
}
var edit_mode = '1', // 再編輯模式 ( '1'=開; '0'=不開 )
	ajax_php_url = js_url.replace('assets/js/com-post-ajax.js','com-post-ajax.php'),
	wp_url = js_url.substr(0, js_url.indexOf('wp-content')),
	pic_sb = wp_url + 'wp-admin/images/wpspin_light.gif', // 提交 icon
	pic_no = wp_url + 'wp-admin/images/no.png', // 錯誤 icon
	pic_ys = wp_url + 'wp-admin/images/yes.png', // 成功 icon
	txt1 = '<div id="loading" class="comment-tips"><img src="' + pic_sb + '" style="vertical-align:middle;" alt=""/> 正在提交, 请稍候...</div>',
	txt2 = '<div id="error" class="comment-tips">#</div>',
	txt3 = '"> <div id="edita"><img src="' + pic_ys + '" style="vertical-align:middle;" alt=""/> 提交成功',
	cancel_edit = '取消编辑',
	edit, num = 1, comm_array=[]; comm_array.push('');

jQuery(document).ready(function($) {
	$comments = $('#comments-title'); // 評論數的 ID
	$cancel = $('#cancel-comment-reply-link'); cancel_text = $cancel.text();
	$submit = $('#commentform #submit'); $submit.prop('disabled', false);
	$('#comment').after( txt1 + txt2 ); $('#loading').hide(); $('#error').hide();
	$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');


var formAboveComments = false; // 默认评论表单在下方
jQuery(function($){
	var $respond = $('#respond');
	var $commentList = $('.comment-list').first();
	if ($respond.length && $commentList.length) {
		formAboveComments = $respond.offset().top < $commentList.offset().top;
	}
});

/** submit */
$('#commentform').submit(function() {
	$('#loading').slideDown();
	$submit.prop('disabled', true).fadeTo('slow', 0.5);

	var formData = $(this).serialize();
	if ( edit ) {
		formData += '&edit_id=' + encodeURIComponent(edit);
	}

	/** Ajax */
	$.ajax( {
		url: ajax_php_url,
		data: formData,
		type: $(this).attr('method'),

		error: function(request) {
			$('#loading').slideUp();
			$('#error').slideDown().html('<img src="' + pic_no + '" style="vertical-align:middle;" alt=""/> ' + request.responseText);
			setTimeout(function() {$submit.prop('disabled', false).fadeTo('slow', 1); $('#error').slideUp();}, 3000);
		},

		success: function(data) {
			$('#loading').hide();
			// 获取提交成功时的标题文字，用于后续“再次编辑”时找回状态
			var current_reply_text = $('#reply-title').html();

			comm_array.push($('#comment').val());
			$('textarea').each(function() {this.value = ''});
			var t = addComment, cancel = t.I('cancel-comment-reply-link'), temp = t.I('wp-temp-form-div'), respond = t.I(t.respondId), post = t.I('comment_post_ID').value, parent = t.I('comment_parent').value;

			// 更新评论计数逻辑
			if (!edit && $comments.length) {
				var text = $comments.text().trim();
				var m = text.match(/(\d{1,3}(?:,\d{3})*|\d+)(?!.*\d)/);
				if (m) {
					var raw = m[1];
					var number = parseInt(raw.replace(/,/g, ''), 10);
					var next = number + 1;
					var formatted = raw.includes(',') ? next.toLocaleString('en-US') : String(next);
					text = text.replace(raw, formatted);
					$comments.text(text);
				}
			}

			// show comment
			new_htm = '" id="new_comm_' + num + '"></';
			new_htm = ( parent == '0' ) ? ('\n<ol style="clear:both;" class="comment-list' + new_htm + 'ol>') : ('\n<ul class="children' + new_htm + 'ul>');

			ok_htm = '\n<span id="success_' + num + txt3;
			if ( edit_mode == '1' ) {
				div_ = (document.body.innerHTML.indexOf('div-comment-') == -1) ? '' : ((document.body.innerHTML.indexOf('li-comment-') == -1) ? 'div-' : '');
				ok_htm += '，刷新页面之前你可以 <a rel="nofollow" class="comment-reply-link comment-reply-link-edit" ' + 
						  'data-replyto="' + current_reply_text + '" href="#edit" ' + 
						  'onclick=\'return addComment.moveForm("' + div_ + 'comment-' + parent + '", "' + parent + '", "respond", "' + post + '", ' + num + ', this)\'>再次编辑</a></div> ';
			}
			ok_htm += '</span><span></span>\n';

			$('#respond').before(new_htm);
			$('#new_comm_' + num).hide().append(data);
			$('#new_comm_' + num + ' li').append(ok_htm);
			$('#new_comm_' + num).fadeIn(4000);

			var $newComm = $('#new_comm_' + num);
			var isReply = parent !== '0';
			var offset = (formAboveComments && isReply) ? -100 : 180;
			$body.animate({ scrollTop: $newComm.offset().top - offset }, 900);
			countdown(); num++ ; edit = ''; $('*').remove('#edit_id');
			cancel.style.display = 'none';
			cancel.onclick = null;
			t.I('comment_parent').value = '0';
			var $replyTitle = $("#respond #reply-title");
			if ($replyTitle.data("original-title")) {
				$replyTitle.html($replyTitle.data("original-title"));
			}
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
	moveForm : function(commId, parentId, respondId, postId, num, element) {
		var t = this, div, comm = t.I(commId), respond = t.I(respondId), cancel = t.I('cancel-comment-reply-link'), parent = t.I('comment_parent'), post = t.I('comment_post_ID');
		var $replyTitle = $("#respond #reply-title");
		var replyToText = false;
		if ( edit ) exit_prev_edit();

		if ( num ) {
			t.I('comment').value = comm_array[num];
			edit = t.I('new_comm_' + num).innerHTML.match(/(comment-)(\d+)/)[2];
			$new_sucs = $('#success_' + num ); $new_sucs.hide();
			$new_comm = $('#new_comm_' + num ); $new_comm.hide();
			$cancel.text(cancel_edit);
			if (element && $(element).attr('data-replyto')) {
				replyToText = $(element).attr('data-replyto');
			}
		} else {
			$cancel.text(cancel_text);
			replyToText = element;
		}
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

		$body.animate({ scrollTop: $('#respond').offset().top - 180 }, {
			duration: 400,
			easing: 'swing'
		});

		if (replyToText) {
			if (!$replyTitle.data("original-title"))
				$replyTitle.data("original-title", $replyTitle.html());
			$replyTitle.html(replyToText);
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

			// 取消编辑或取消回复时，将标题恢复为最初的“发表评论”
			if ($replyTitle.data("original-title"))
				$replyTitle.html($replyTitle.data("original-title"));

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
		$submit.val(submit_val).prop('disabled', false).fadeTo('slow', 1);
		wait = 15;
	}
}
}); // end jQ

// ajax评论翻页
jQuery(document).ready(function comment_page_ajax(){
	jQuery(document).on('click', '#commentpager a', function(e){
		// 如果是朴素型链接则不使用ajax翻页
		var compageUrl = jQuery(this).attr('href');
		if (compageUrl.indexOf('?cpage=') !== -1 || compageUrl.indexOf('&cpage=') !== -1) {
			return;
		}
		e.preventDefault();
		// 先取消任何已激活的“回复”状态，确保表单回到原始位置
		var $cancelReply = jQuery("#cancel-comment-reply-link");
		if ($cancelReply.length) {
			$cancelReply.trigger("click");
		}
		var post_id = jQuery('#comment_post_ID').val();
		var page_id = 1;
		var pathMatch = compageUrl.match(/(comment-page-|page\/)(\d+)/);
		if (pathMatch && pathMatch[2]) {
			page_id = pathMatch[2];
		}
		var cleanUrl = compageUrl.replace(/\/$/, '');
		jQuery.ajax({
			url: cleanUrl,
			type: "POST",
			data: {
				action: 'compageajax',
				postid: post_id,
				pageid: page_id,
				nonce_field: commentPagingNonce
			},
			beforeSend: function() {
				document.body.style.cursor = 'wait';
				jQuery('#commentpager').html('<em class="comment-ajax-tip">正在努力为您加载中...</em>');
			},
			success: function(data) {
				jQuery('#comment-ajax').html(data);
				document.body.style.cursor = 'auto';
				jQuery('html,body').animate({
					scrollTop: jQuery('#comments').offset().top - 20
				}, 500);
			},
			error: function(xhr) {
				console.error('评论加载错误:', xhr.responseText);
				jQuery('#commentpager').html('<em class="comment-ajax-tip-error">加载失败，请刷新重试</em>');
			}
		});
	});
});

// ajax走心评论
function post_karma(comment_id, action_url, elem) {
	elem.innerHTML = "<i class='iconfont hearticon'>&#xe63a;</i>";
	var origin_karma = elem.getAttribute("data-karma");
	var new_karma = Number(!parseInt(origin_karma));
	var formData = new FormData();
	formData.append('comment_id', comment_id);
	formData.append('comment_karma', new_karma);
	jQuery.ajax({
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