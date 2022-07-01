	<?php echo $message; ?>
	<table id="list">
		<thead>
			<tr>
			<th><input type="checkbox" id="check_all" name="check_all" value="ids[]"></th>
			<th><?php echo anchor("admin/news/order_by/visible/", lang('news-visible')); ?></th>
			<th><?php echo anchor("admin/news/order_by/date/", lang('news-date')); ?></th>
			<th><?php echo anchor("admin/news/order_by/title/", lang('news-title')); ?></th>
			<th><?php echo lang('news-content'); ?></th>
			<th><?php echo lang('action'); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php	foreach ($news as $new): ?>
				<tr>
				<td><input type="checkbox" name="ids[]" value="<?php echo $new['id']; ?>"></td>
				<td><?php echo "<img src=\"" . (($new['visible']) ? site_url("images/admin/accept.png") : site_url("images/admin/cross.png")) . "\" />"; ?></td>
				<td><?php echo custom_datetime($datetime_format, $new['date']); ?></td>
				<td><?php echo $new['title']; ?></td>
				<td><?php echo mb_strcut(strip_tags(unescape($new['content'])), 0, 64, "UTF-8") . "..."; ?></td>
				<td>
				<?php
				foreach ($anchors as $anchor)
					printf($anchor . "&nbsp;", $new['id']);
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