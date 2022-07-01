	<?php echo $message; ?>
	<table id="list">
		<thead>
			<tr>
			<th><input type="checkbox" id="check_all" name="check_all" value="ids[]"></th>
			<th><?php echo anchor("admin/admins/order_by/name/", lang('admins-name')); ?></th>
			<th><?php echo anchor("admin/admins/order_by/username/", lang('admins-username')); ?></th>
			<th><?php echo anchor("admin/admins/order_by/email/", lang('admins-email')); ?></th>
			<th><?php echo anchor("admin/admins/order_by/last_login2/", lang('admins-last-login')); ?></th>
			<th><?php echo lang('action'); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php	foreach ($admins as $user): ?>
				<tr>
				<td><input type="checkbox" name="ids[]" value="<?php echo $user['id']; ?>"></td>				
				<td><?php echo $user['name']; ?></td>
				<td><?php echo $user['username']; ?></td>
				<td><?php echo $user['email']; ?></td>
				<td><?php echo custom_datetime($datetime_format, $user['last_login2']); ?></td>
				<td>
				<?php
				foreach ($anchors as $anchor)
					printf($anchor . "&nbsp;", $user['id']);
				?>
				</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		
		<tfoot>
			<tr>
			<td colspan="6"></td>
			</tr>
		</tfoot>
	</table>