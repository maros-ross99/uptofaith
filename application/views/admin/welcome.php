<div class="msg-info">
	<?php printf(lang('welcome-text'), $admin['name']); ?>
	<br />
	<?php printf(lang('welcome-last-login'), custom_datetime($datetime_format, $admin['last_login'])); ?>
</div>