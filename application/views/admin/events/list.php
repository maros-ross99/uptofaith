	<?php echo $message; ?>
	<?php echo lang('events-note'); ?>
	<table id="list">
		<thead>
			<tr>
			<th><input type="checkbox" id="check_all" name="check_all" value="ids[]"></th>
			<th><?php echo anchor("admin/events/order_by/active/", lang('events-active')); ?></th>
			<th><?php echo anchor("admin/events/order_by/name/", lang('events-name')); ?></th>
			<th><?php echo anchor("admin/events/order_by/place/", lang('events-place')); ?></th>
			<th><?php echo anchor("admin/events/order_by/from_date/", lang('events-from-date')); ?></th>
			<th><?php echo anchor("admin/events/order_by/registration_from_date/", lang('events-registration-from-date')); ?></th>
			<th><?php echo lang('action'); ?></th>
			</tr>
			
		</thead>

		<tbody>
			
			<?php	foreach ($events as $event): ?>
				<tr>
				<td><input type="checkbox" name="ids[]" value="<?php echo $event['id']; ?>"></td>
				<td><?php echo "<img src=\"" . (($event['active']) ? site_url("images/admin/accept.png") : site_url("images/admin/cross.png")) . "\" />"; ?></td>
				<td><?php echo $event['name']; ?></td>
				<td><?php echo $event['place']; ?></td>
				<td><?php echo ($event['from_date'] != 0) ? custom_datetime($datetime_format, $event['from_date']) : lang('events-not-specified'); ?></td>
				<td><?php echo ($event['registration_from_date'] != 0) ? custom_datetime($datetime_format, $event['registration_from_date']) : lang('events-not-specified'); ?></td>
				<td>
				<?php
				foreach ($anchors as $anchor)
					printf($anchor . "&nbsp;", $event['id']);
				?>
				</td>
				</tr>
			<?php endforeach; ?>
			
		</tbody>
		
		<tfoot>
			<tr>
			<td colspan="7"></td>
			</tr>
		</tfoot>
	</table>