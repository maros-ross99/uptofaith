	<?php echo $message; ?>
	<table id="list">
		<thead>
			<tr>
			<th><input type="checkbox" id="check_all" name="check_all" value="ids[]"></th>
			<th><?php echo anchor("admin/messages/order_by/date/", lang('messages-date')); ?></th>
			<th><?php echo anchor("admin/messages/order_by/subject/", lang('messages-subject')); ?></th>
			<th><?php echo lang('messages-message'); ?></th>
			<th><?php echo lang('action'); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php	foreach ($messages as $message): ?>
				<tr>
				<td><input type="checkbox" name="ids[]" value="<?php echo $message['id']; ?>"></td>
				<td><?php echo custom_datetime($datetime_format, $message['date']); ?></td>
				<td><?php echo $message['subject']; ?></td>
				<td><?php echo mb_strcut(strip_tags(unescape($message['message'])), 0, 64, "UTF-8") . "..."; ?></td>
				<td>
				<?php
				foreach ($anchors as $anchor)
					printf($anchor . "&nbsp;", $message['id']);
				?>
				</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		
		<tfoot>
			<tr>
			<td colspan="5"></td>
			</tr>
		</tfoot>
	</table>