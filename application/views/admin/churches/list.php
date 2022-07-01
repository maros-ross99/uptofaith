	<?php echo $message; ?>
	<?php echo $pagination_links; ?>
	<table id="list">
		<thead>
			<tr>
			<th><input type="checkbox" id="check_all" name="check_all" value="ids[]"></th>
			<th><?php echo anchor("admin/churches/order_by/name/", lang('churches-name')); ?></th>
			<th><?php echo anchor("admin/churches/order_by/country_id/", lang('churches-country')); ?></th>
			<th><?php echo lang('action'); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php	foreach ($churches as $church): ?>
				<tr>
				<td><input type="checkbox" name="ids[]" value="<?php echo $church['id']; ?>"></td>
				<td><?php echo $church['name']; ?></td>
				<td>
				<?php 
				if ($countries != NULL)
					foreach ($countries as $country)
						if ($country['id'] == $church['country_id'])
							echo $country['name'];
				?>
				</td>
				<td>
				<?php
				foreach ($anchors as $anchor)
					printf($anchor . "&nbsp;", $church['id']);
				?>
				</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		
		<tfoot>
			<tr>
			<td colspan="4"></td>
			</tr>
		</tfoot>
	</table>
	<?php echo $pagination_links; ?>