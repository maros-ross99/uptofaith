<?php foreach ($ids as $id) : ?>
	<input type="hidden" name="ids[]" value="<?php echo $id; ?>">
<?php endforeach; ?>
<div class="msg-warning"><?php echo $message; ?></div>
