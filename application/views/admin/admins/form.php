	<?php echo validation_errors(); ?>
	<table id="form">
		<tbody>
			<tr>
			<th><?php echo lang('admins-name'); ?> *</th>
			<td><input type="text" name="data[name]" value="<?php echo $data['name']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('admins-username'); ?> *</th>
			<td><input type="text" name="data[username]" value="<?php echo $data['username']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('admins-email'); ?> *</th>
			<td><input type="text" name="data[email]" value="<?php echo $data['email']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('admins-password'); ?></th>
			<td><input type="text" name="data[new_password]" /></td>
			</tr>
			<tr>
			<th><?php echo lang('admins-rights'); ?> *</th>
			<td>
			<table>
				<?php foreach($rights as $right_short => $right): ?>
				<tr>
					<td><?php echo $right['name']; ?></td>
					<td>
					<select name="data[rights][<?php echo $right_short; ?>]">
						<?php foreach($rights_type[$right['type']] as $key => $value): ?>
							<option value="<?php echo $key; ?>" <?php echo ((array_key_exists($right_short, $data['rights'])) && ($data['rights'][$right_short] == $key)) ? "selected" : ""; ?>><?php echo $value; ?></option>
						<?php endforeach; ?>
					</select>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
			</td>
			</tr>
		</tbody>
	</table>	