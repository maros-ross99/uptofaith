	<div id="box_buttons">
		<?php echo form_open_multipart($action); ?>
		<?php
		foreach ($buttons as $button)
			echo "&nbsp;" . $button;
		?>
	</div>