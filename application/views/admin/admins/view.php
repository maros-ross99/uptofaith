	<input type="hidden" name="ids[]" value="<?php echo $data['id']; ?>">	
	<table id="view">
		<tbody>
			<tr>
			<th><?php echo lang('admins-name'); ?></th>
			<td><?php echo $data['name']; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('admins-username'); ?></th>
			<td><?php echo $data['username']; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('admins-email'); ?></th>
			<td><?php echo $data['email']; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('admins-last-login'); ?></th>
			<td><?php echo custom_datetime($datetime_format, $data['last_login']); ?></td>
			</tr>
			<tr>
			<th><?php echo lang('admins-rights'); ?></th>
			<td>
			<table>
				<?php foreach($rights as $right_short => $right): ?>
				<tr>
					<td><strong><?php echo $right['name']; ?></strong></td>
					<td>
						<?php 
						foreach($rights_type[$right['type']] as $key => $value)
						{
							if ((array_key_exists($right_short, $data['rights'])) && ($data['rights'][$right_short] == $key))
								echo $value;
						}
						?>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
			</td>
			</tr>
		</tbody>
	</table>	