	<?php echo $message; ?>
	<table id="list">
		<thead>
			<tr>
			<th><input type="checkbox" id="check_all" name="check_all" value="ids[]"></th>
			<th><?php echo anchor("admin/photogalleries/order_by/date/", lang('photogalleries-visible')); ?></th>
			<th><?php echo anchor("admin/photogalleries/order_by/date/", lang('photogalleries-date')); ?></th>
			<th><?php echo anchor("admin/photogalleries/order_by/name/", lang('photogalleries-name')); ?></th>
			<th><?php echo lang('photogalleries-description'); ?></th>
			<th><?php echo lang('action'); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php	foreach ($photogalleries as $photogallery): ?>
				<tr>
				<td><input type="checkbox" name="ids[]" value="<?php echo $photogallery['id']; ?>"></td>
				<td><?php echo "<img src=\"" . (($photogallery['visible']) ? site_url("images/admin/accept.png") : site_url("images/admin/cross.png")) . "\" />"; ?></td>
				<td><?php echo custom_datetime($datetime_format, $photogallery['date']); ?></td>
				<td><?php echo $photogallery['name']; ?></td>
				<td><?php echo mb_strcut(strip_tags(unescape($photogallery['description'])), 0, 64, "UTF-8") . "..."; ?></td>
				<td>
				<?php
				foreach ($anchors as $anchor)
					printf($anchor . "&nbsp;", $photogallery['id']);
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