	<?php echo validation_errors(); ?>
	<?php echo $message; ?>
	<div class='msg-info'><?php echo lang('admins-profile-note'); ?></div>
	<table id="form">
		<tbody>
			<tr>
			<th><?php echo lang('admins-name'); ?> *</th>
			<td><input type="text" name="data[name]" value="<?php echo $data['name']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('admins-email'); ?> *</th>
			<td><input type="text" name="data[email]" value="<?php echo $data['email']; ?>"/></td>
			</tr>
			<tr>
			<th colspan="2" class="section_description"><?php echo lang('admins-change-password'); ?></th>			
			</tr>
			<tr>
			<th><?php echo lang('admins-new-password'); ?> *</th>
			<td><input type="password" name="data[new_password]" /></td>
			</tr>
			<tr>
			<th><?php echo lang('admins-new-password2'); ?> *</th>
			<td><input type="password" name="data[new_password2]" /></td>
			</tr>
			<tr>
			<th colspan="2" class="section_description"><?php echo lang('admins-save-profile'); ?></th>			
			</tr>
			<tr>
			<th><?php echo lang('admins-password'); ?> *</th>
			<td><input type="password" name="data[old_password]" /></td>
			</tr>
		</tbody>
	</table>	