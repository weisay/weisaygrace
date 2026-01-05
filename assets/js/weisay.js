//滚屏
jQuery(document).ready(function(){
jQuery('.roll-top').click(function(){jQuery('html,body').animate({scrollTop: '0px'}, 800);}); 
});

//手机版菜单展开
jQuery(document).ready(function(){
	jQuery('nav#menu').mmenu({
		extensions : [ 'left' ],
		counters : true,
		navbar : {
			title : '导航'
		},
	});
var $menu = jQuery('nav#menu-right');
	$menu.mmenu({
		offCanvas : {
			position : 'right'
			},
		navbar : {
			title : '侧边栏'
			},
	});
});

//侧边栏TAB效果
jQuery(document).ready(function(){
	jQuery(".tabnav li").click(function(){
		jQuery(this).addClass("selected").siblings().removeClass("selected");
		jQuery(".tab-content > ul").eq(jQuery(".tabnav li").index(this)).addClass("active").siblings().removeClass("active"); 
	});
});

//隐藏/显示侧边栏
jQuery(document).ready( function () {
	jQuery( '.close-sidebar' ).click( function () {
		jQuery( '.close-sidebar,.sidebar,#sidebar-follow' ).hide();
		jQuery( '.show-sidebar' ).show();
		jQuery(".main").addClass("main-all");
	});
	jQuery( '.show-sidebar' ).click( function () {
		jQuery( '.show-sidebar' ).hide();
		jQuery( '.close-sidebar,.sidebar' ).show();
		jQuery(".main").removeClass("main-all");
	});
});

//图片懒加载和渐隐
jQuery(document).ready(function(){
	jQuery('.thumbnail img,.related-img img,.link-image img,.tl-archive-img img').lazyload({
		placeholder:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQImWNgYGBgAAAABQABh6FO1AAAAABJRU5ErkJggg==",
		effect:"fadeIn"
	});
	jQuery('.thumbnail img').hover(
		function() {jQuery(this).fadeTo("fast", 0.7);},
		function() {jQuery(this).fadeTo("fast", 1);
	});
});

//评论表情
jQuery(document).ready(function(){
	jQuery(".emoji").click(function() {
		jQuery(".emoji-smilies").animate({
			opacity: "toggle"
		},
		300);
		return false
	});
	jQuery(".emoji-smilies a").click(function() {
		jQuery(".emoji-smilies").animate({
			opacity: "toggle",
		},
		300)
	});
});

//文章编辑hover
jQuery(document).ready(function(){
	jQuery('.post').hover(
		function() {
			jQuery(this).find('.edit').stop(true,true).fadeIn();
		},
		function() {
			jQuery(this).find('.edit').stop(true,true).fadeOut();
		}
	);
});

//新窗口打开
jQuery(document).ready(function(){
	jQuery("a[rel='external'],a[rel='external nofollow']").click(
	function(){window.open(this.href);return false})
});

//带缩略图版相关日志点击换一批
jQuery(document).ready(function($) {
	const $list = $(".article-related .related-list");
	const $items = $list.find(".related-item");
	const $btn = $("#toggle-related");
	const groupSize = 4;
	const total = $items.length;
	const groupCount = Math.ceil(total / groupSize);
	let currentGroup = 0;
	let isAnimating = false;
	if (total <= 5) {$btn.hide();}
	function showGroup(index) {
		if (isAnimating) return;
		isAnimating = true;
		const start = index * groupSize;
		const end = start + groupSize;
		const $visible = $items.filter(":visible");
		const $next = $items.slice(start, end);
		$visible.stop(true, true).fadeOut(200, function() {
			$next.stop(true, true).fadeIn(300, function() {
				isAnimating = false;
			});
		});
	}
	$btn.on("click", function() {
		if (isAnimating) return;
		currentGroup = (currentGroup + 1) % groupCount;
		showGroup(currentGroup);
	});
	$items.hide();
	$items.slice(0, groupSize).show();
});

//赏弹层
jQuery(document).ready(function(){
	jQuery('.zanzhu').click(function(){
		jQuery('.shang-bg').fadeIn(200);
		jQuery('.shang-content').fadeIn(400);
	});
	jQuery('.shang-bg, .shang-close').click(function(){
		jQuery('.shang-bg, .shang-content').fadeOut(400);
	});
});

//顶部导航下拉菜单，包含延迟效果
(function($){
jQuery.fn.hoverDelay = function(selector, options) {
	var defaults = {
		hoverDuring: 200,
		outDuring: 150,
		hoverEvent: jQuery.noop,
		outEvent: jQuery.noop
	};
	var sets = jQuery.extend(defaults, options || {});
	return jQuery(document).on("mouseenter mouseleave", selector, function(event) {
	var that = this;
	if(event.type == "mouseenter"){
		clearTimeout(that.outTimer);
		that.hoverTimer = setTimeout(
		function(){sets.hoverEvent.apply(that)},sets.hoverDuring);
	}else {
		clearTimeout(that.hoverTimer);
		that.outTimer = setTimeout(
		function(){sets.outEvent.apply(that)},sets.outDuring);
	}
	});
}
})(jQuery);
jQuery(document).ready(function(){
jQuery(".mainmenu li,.top-page li").each(function(){
if(jQuery(this).find("ul").length!=0){jQuery(this).find("a:first").addClass("hasmenu")};
});
jQuery(".mainmenu ul li,.top-page ul li").hoverDelay(".mainmenu ul li,.top-page ul li", {
	hoverEvent: function(){
	jQuery(this).children("ul").show();
},
	outEvent: function(){
	jQuery(this).children("ul").hide();
}
});
});

//文章目录
jQuery(document).ready(function($) {
	const $sidebar = $('.fixed-index');
	const $articleWidget = $('.article-index-area');
	const $indexLinks = $('.article-index-widget a');
	const $titles = $('[id^="title-"]');
	if ($sidebar.length === 0 || $articleWidget.length === 0) return;
	let scrollTimer;
	function clearHighlight() {
		$indexLinks.each(function () {
			$(this).removeClass('current');
			if (this.className.trim() === '') {
				this.removeAttribute('class');
			}
		});
	}
	function highlightCurrentSection() {
		const scrollTop = $(window).scrollTop();
		const articleTop = $articleWidget.offset().top;
		const articleBottom = articleTop + $articleWidget.outerHeight();
		if (scrollTop > articleBottom + 40) {
			clearHighlight();
			return;
		}
		let currentId = '';
		$titles.each(function () {
			const $el = $(this);
			if ($el.offset().top - 30 <= scrollTop) {
				currentId = '#' + $el.attr('id');
			}
		});
		clearHighlight();
		if (currentId) {
			$indexLinks.filter(`[href="${currentId}"]`).addClass('current');
		}
	}
	function handleScroll() {
		clearTimeout(scrollTimer);
		scrollTimer = setTimeout(() => {
			const scrollTop = $(window).scrollTop();
			const articleTop = $articleWidget.offset().top;
			$sidebar.toggle(scrollTop >= articleTop - 40);
			highlightCurrentSection();
		});
	}
	handleScroll();
	$(window).on('scroll', handleScroll);
});
// 点击菜单项时滚动到指定位置
jQuery(document).on('click', '.article-index-widget a[href^="#"]', function(e) {
	var id = jQuery(this).attr('href');
	var $id = jQuery(id);
	if ($id.length === 0) {
		return;
	}
	e.preventDefault();
	var pos = $id.offset().top - 20;
		jQuery('html, body').animate({
			scrollTop: pos
		}, 300);
});

//侧边栏固定跟随滚动
var q2w3_sidebar_options = [{
	"sidebar": "right",
	"margin_top": 0,
	"margin_bottom": 0,
	"stop_id": "footers",
	"screen_max_width": 0,
	"screen_max_height": 0,
	"width_inherit": true,
	"refresh_interval": 1000,
	"window_load_hook": false,
	"disable_mo_api": false,
	"widgets": ["sidebar-follow"]
}];
function q2w3_sidebar_init() {
	for (var e = 0; e < q2w3_sidebar_options.length; e++) q2w3_sidebar(q2w3_sidebar_options[e]);
	jQuery(window)
		.on("resize", function() {
			for (var e = 0; e < q2w3_sidebar_options.length; e++) q2w3_sidebar(q2w3_sidebar_options[e])
		});
	var i = function() {
		for (var e = ["WebKit", "Moz", "O", "Ms", ""], i = 0; i < e.length; i++)
			if (e[i] + "MutationObserver" in window) return window[e[i] + "MutationObserver"];
		return !1
	}();
	0 == q2w3_sidebar_options[0].disable_mo_api && i ? (q2w3Refresh = !1, new i(function(e) {
			e.forEach(function(e) {
				-1 != q2w3_exclude_mutations_array(q2w3_sidebar_options)
					.indexOf(e.target.id) || e.target.className && "function" == typeof e.target.className.indexOf && -1 != e.target.className.indexOf("q2w3-fixed-widget-container") || (q2w3Refresh = !0)
			})
		})
		.observe(document.body, {
			childList: !0,
			attributes: !0,
			attributeFilter: ["style", "class"],
			subtree: !0
		}), setInterval(function() {
			if (q2w3Refresh) {
				for (var e = 0; e < q2w3_sidebar_options.length; e++) q2w3_sidebar(q2w3_sidebar_options[e]);
				q2w3Refresh = !1
			}
		}, 300)) : (console.log("MutationObserver not supported or disabled!"), q2w3_sidebar_options[0].refresh_interval > 0 && setInterval(function() {
		for (var e = 0; e < q2w3_sidebar_options.length; e++) q2w3_sidebar(q2w3_sidebar_options[e])
	}, q2w3_sidebar_options[0].refresh_interval))
}
function q2w3_exclude_mutations_array(e) {
	for (var i = new Array, o = 0; o < e.length; o++)
		if (e[o].widgets.length > 0)
			for (var t = 0; t < e[o].widgets.length; t++) i.push(e[o].widgets[t]), i.push(e[o].widgets[t] + "_clone");
	return i
}
function q2w3_sidebar(e) {
	if (!e) return !1;
	if (!e.widgets) return !1;
	if (e.widgets.length < 1) return !1;

	function i() {}
	e.sidebar || (e.sidebar = "q2w3-default-sidebar");
	var o = new Array,
		t = jQuery(window)
		.height(),
		n = jQuery(document)
		.height(),
		r = e.margin_top;
	jQuery("#wpadminbar")
		.length && (r = e.margin_top + jQuery("#wpadminbar")
			.height()), jQuery(".q2w3-widget-clone-" + e.sidebar)
		.remove();
	for (var s = 0; s < e.widgets.length; s++) widget_obj = jQuery("#" + e.widgets[s]), widget_obj.css("position", ""), widget_obj.attr("id") ? (o[s] = new i, o[s].obj = widget_obj, o[s].clone = widget_obj.clone(), o[s].clone.children()
		.remove(), o[s].clone_id = widget_obj.attr("id") + "_clone", o[s].clone.addClass("q2w3-widget-clone-" + e.sidebar), o[s].clone.attr("id", o[s].clone_id), o[s].clone.css("height", widget_obj.height()), o[s].clone.css("visibility", "hidden"), o[s].offset_top = widget_obj.offset()
		.top, o[s].fixed_margin_top = r, o[s].height = widget_obj.outerHeight(!0), o[s].fixed_margin_bottom = r + o[s].height, r += o[s].height) : o[s] = !1;
	var d, a = 0;
	for (s = o.length - 1; s >= 0; s--) o[s] && (o[s].next_widgets_height = a, o[s].fixed_margin_bottom += a, a += o[s].height, d || ((d = widget_obj.parent())
		.addClass("q2w3-fixed-widget-container"), d.css("height", ""), d.height(d.height())));
	jQuery(window)
		.off("scroll." + e.sidebar);
	for (s = 0; s < o.length; s++) o[s] && _(o[s]);

	function _(i) {
		var o, r = i.offset_top - i.fixed_margin_top,
			s = n - e.margin_bottom;
		e.stop_id && jQuery("#" + e.stop_id)
			.length && (s = jQuery("#" + e.stop_id)
				.offset()
				.top - e.margin_bottom), o = e.width_inherit ? "inherit" : i.obj.css("width");
		var d = !1,
			a = !1,
			_ = !1;
		jQuery(window)
			.on("scroll." + e.sidebar, function(n) {
				if (jQuery(window)
					.width() <= e.screen_max_width || jQuery(window)
					.height() <= e.screen_max_height) _ || (i.obj.css("position", ""), i.obj.css("top", ""), i.obj.css("bottom", ""), i.obj.css("width", ""), i.obj.css("margin", ""), i.obj.css("padding", ""), widget_obj.parent()
					.css("height", ""), jQuery("#" + i.clone_id)
					.length > 0 && jQuery("#" + i.clone_id)
					.remove(), _ = !0, d = !1, a = !1);
				else {
					var w = jQuery(this)
						.scrollTop();
					w + i.fixed_margin_bottom >= s ? (a || (i.obj.css("position", "fixed"), i.obj.css("top", ""), i.obj.css("width", o), jQuery("#" + i.clone_id)
						.length <= 0 && i.obj.before(i.clone), a = !0, d = !1, _ = !1), i.obj.css("bottom", w + t + i.next_widgets_height - s)) : w >= r ? d || (i.obj.css("position", "fixed"), i.obj.css("top", i.fixed_margin_top), i.obj.css("bottom", ""), i.obj.css("width", o), jQuery("#" + i.clone_id)
						.length <= 0 && i.obj.before(i.clone), d = !0, a = !1, _ = !1) : _ || (i.obj.css("position", ""), i.obj.css("top", ""), i.obj.css("bottom", ""), i.obj.css("width", ""), jQuery("#" + i.clone_id)
						.length > 0 && jQuery("#" + i.clone_id)
						.remove(), _ = !0, d = !1, a = !1)
				}
			})
			.trigger("scroll." + e.sidebar)
	}
}
"undefined" != typeof q2w3_sidebar_options && q2w3_sidebar_options.length > 0 ? window.jQuery ? q2w3_sidebar_options[0].window_load_hook ? jQuery(window)
	.load(q2w3_sidebar_init) : "loading" != document.readyState ? q2w3_sidebar_init() : document.addEventListener("DOMContentLoaded", q2w3_sidebar_init) : console.log("jQuery is not loaded!") : console.log("q2w3_sidebar_options not found!");