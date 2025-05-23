//归档折叠展开
jQuery(document).ready(function() {
	function setsplicon(c, d) {
		if (c.html()=='+' || d=='+') {
			c.html('-');
			c.removeClass('car-plus');
			c.addClass('car-minus');
		} else if( !d || d=='-'){
			c.html('+');
			c.removeClass('car-minus');
			c.addClass('car-plus');
		}
	}
	jQuery('.car-collapse').find('.car-yearmonth').click(function() {
		jQuery(this).next('ul').slideToggle('fast');
		setsplicon(jQuery(this).find('.car-toggle-icon'));
	});
	jQuery('.car-collapse').find('.car-toggler').click(function() {
		if ( '展开所有月份' == jQuery(this).text() ) {
			jQuery(this).parent('.car-container').find('.car-monthlisting').show();
			jQuery(this).text('折叠所有月份');
			setsplicon(jQuery('.car-collapse').find('.car-toggle-icon'), '+');
		}
		else {
			jQuery(this).parent('.car-container').find('.car-monthlisting').hide();
			jQuery(this).text('展开所有月份');
			setsplicon(jQuery('.car-collapse').find('.car-toggle-icon'), '-');
		}
		return false;
	});
jQuery("#archive-selector").change(function(){ 
	var selval = parseInt(jQuery("#archive-selector").find("option:selected").val(), 10);
	if (selval == 0) {
	jQuery(".car-list li[class^='car-pubyear-'],.car-list div[class^='car-year-']").show();
	} else {
	jQuery.each(jQuery(".car-list div[class^='car-year-']"), function(i, obj){
		var orgval = parseInt(obj.className.replace("car-year-", ""), 10);
				if (selval == orgval)
					obj.style.display='';
				else
			obj.style.display='none';
	});
	 jQuery.each(jQuery(".car-list li[class^='car-pubyear-']"), function(i, obj){
		var orgval = parseInt(obj.className.replace("car-pubyear-", ""), 10);
				if (selval == orgval)
					obj.style.display='';
				else
			obj.style.display='none';
	});
	}
	});

jQuery("#archive-selector").append("<option value='0'> 全部 </option>");
jQuery.each(jQuery(".car-list li[class^='car-pubyear-']"), function(i, obj){
	var year1 = obj.className.replace("car-pubyear-", "");
	if (jQuery("#archive-selector option[value=" + year1 + "]").length < 1)
		jQuery("#archive-selector").append("<option value='" + year1 + "'> " + year1 + "年 </option>");
});

});

//动画数字计数器
jQuery(document).ready(function(){
	jQuery('.archives-count').each(function() {
		jQuery(this).prop('counter', 0).animate({
		counter: jQuery(this).text()
		}, {
		duration: 800,
		easing: 'swing',
		step: function(now) {
			jQuery(this).text(Math.ceil(now));
			}
		});
	});
});