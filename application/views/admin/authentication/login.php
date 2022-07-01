	<?php echo $message; ?>
	<?php echo validation_errors(); ?>
	<table id="form">
		<tbody>
			<tr>
			<th><?php echo lang('authentication-username'); ?></th>
			<td><input type="text" name="data[username]" value=""></td>
			</tr>
			<tr>
			<th><?php echo lang('authentication-password'); ?></th>
			<td><input type="password" name="data[password]" value=""></td>
			</tr>
			<?php if ($captcha != NULL) : ?>
				<tr>
				<th><?php echo lang('authentication-captcha') . "<br />" . $captcha['image']; ?></th>
				<td><input type="text" name="data[captcha]" value=""></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>	