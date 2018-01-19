<?php
// CAPTCHA OPTION 2: 
$custom_captcha = "yes"; // set to "yes" to use

// ENTER YOUR QUESTION AND ANSWER PAIRS - add as many as you like
$custom_captcha_challenges[] = array("What colour is custard?", "yellow");
$custom_captcha_challenges[] = array("Which day follows monday?", "tuesday");
$custom_captcha_challenges[] = array("Complete the following: Fish and", "chips");
$custom_captcha_challenges[] = array("Which colour wine, white, rose or...", "red");
$custom_captcha_challenges[] = array("What is the capital of England?", "london");
$custom_captcha_challenges[] = array("What is the capital of Scotland?", "edinburgh");


$rnd = rand(0,count($custom_captcha_challenges)-1);
$custom_antispam_field_index = $rnd;
$custom_antispam_field_question = $custom_captcha_challenges[$rnd][0];
$customer_antispam_field_HTML = $custom_captcha_challenges[$rnd][0].
	' &nbsp; <input type="hidden" name="custom_antispam_field_index" value="'.$rnd.'" />
	  <input type="hidden" name="custom_antispam_field_answer" value="'.$custom_captcha_challenges[$rnd][1].'" />
	  <input size="8" type="text" name="custom_antispam_field" id="custom_antispam_field" class="review" />';
?>