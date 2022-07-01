	<?php echo $message; ?>
	<table id="list">
		<thead>
			<tr>
			<th><input type="checkbox" id="check_all" name="check_all" value="ids[]"></th>
			<th><?php echo anchor("admin/countries/order_by/name/", lang('countries-name')); ?></th>
			<th><?php echo lang('action'); ?></th>
			</tr>
			
		</thead>

		<tbody>
			
			<?php	foreach ($countries as $country): ?>
				<tr>
				<td><input type="checkbox" name="ids[]" value="<?php echo $country['id']; ?>"></td>
				<td><?php echo $country['name']; ?></td>
				<td>
				<?php
				foreach ($anchors as $anchor)
					printf($anchor . "&nbsp;", $country['id']);
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
