<?php
	$t1=$post->post_date_gmt;
	$t2=date("Y-m-d H:i:s");
	$interval=(strtotime($t2)-strtotime($t1))/86400;
	if($interval<5){echo '<i class="iconfont newicon">&#xe610;</i>';}
?>