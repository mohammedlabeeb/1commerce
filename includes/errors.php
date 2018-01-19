<p class="error">The following error(s) occured when submitting your enquiry:</p>
<ul class="error">
<?php
foreach($errors as $e) {
	?><li><?php echo $e?></li><?php
}
?>
</ul>
<p class="error">Please correct these errors and resubmit your enquiry.</p>