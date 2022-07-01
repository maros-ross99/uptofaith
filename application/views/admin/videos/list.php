	<?php echo $message; ?>
	<table id="list">
		<thead>
			<tr>
			<th><input type="checkbox" id="check_all" name="check_all" value="ids[]"></th>
			<th><?php echo anchor("admin/videos/order_by/visible/", lang('videos-visible')); ?></th>
			<th><?php echo anchor("admin/videos/order_by/date/", lang('videos-date')); ?></th>
			<th><?php echo anchor("admin/videos/order_by/name/", lang('videos-name')); ?></th>
			<th><?php echo lang('videos-description'); ?></th>
			<th><?php echo lang('action'); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php	foreach ($videos as $video): ?>
				<tr>
				<td><input type="checkbox" name="ids[]" value="<?php echo $video['id']; ?>"></td>
				<td><?php echo "<img src=\"" . (($video['visible']) ? site_url("images/admin/accept.png") : site_url("images/admin/cross.png")) . "\" />"; ?></td>
				<td><?php echo custom_datetime($datetime_format, $video['date']); ?></td>
				<td><?php echo $video['name']; ?></td>
				<td><?php echo mb_strcut(strip_tags(unescape($video['description'])), 0, 64, "UTF-8") . "..."; ?></td>
				<td>
				<?php
				foreach ($anchors as $anchor)
					printf($anchor . "&nbsp;", $video['id']);
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