	<?php echo $message; ?>
	<?php echo $pagination_links; ?>
	<table id="list">
		<thead>
			<tr>
			<th><input type="checkbox" id="check_all" name="check_all" value="ids[]"></th>
			<th><?php echo anchor("admin/participants/order_by/registration_date/", lang('participants-date')); ?></th>
			<th><?php echo anchor("admin/participants/order_by/surname/", lang('participants-surname-and-name')); ?></th>
			<th><?php echo anchor("admin/participants/order_by/country_id/", lang('participants-country')); ?></th>
			<th><?php echo anchor("admin/participants/order_by/city_id/", lang('participants-city')); ?></th>
			<th><?php echo anchor("admin/participants/order_by/church_id/", lang('participants-church')); ?></th>
			<th><?php echo lang('action'); ?></th>
			</tr>
			
		</thead>

		<tbody>
			<tr>
			<td></td>
			<td></td>
			<td><input type="text" style="width:120px;" name="filter[surname]" value="<?php echo $filter['surname']; ?>"/></td>
			<td>
				<select name="filter[country_id]">
					<option value=""></option>
					<?php	foreach ($countries as $country): ?>
					<option value="<?php echo $country['id']; ?>" <?php echo ($filter['country_id'] == $country['id']) ? "selected" : ""; ?>><?php echo $country['name']; ?></option>
					<?php endforeach; ?>
				</select>
			</td>	
			<td>
				<select name="filter[city_id]">
					<option value=""></option>
					<?php	foreach ($countries as $country): ?>
						<optgroup label="<?php echo $country['name']; ?>">
							<?php	foreach ($cities as $city): ?>
								<?php	if ($country['id'] == $city['country_id']): ?>
									<option value="<?php echo $city['id']; ?>" <?php echo ($filter['city_id'] == $city['id']) ? "selected" : ""; ?>><?php echo $city['name']; ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
				</select>
			</td>	
			<td>
				<select name="filter[church_id]">
					<option value=""></option>
					<?php	foreach ($countries as $country): ?>
						<optgroup label="<?php echo $country['name']; ?>">
						<?php	foreach ($churches as $church): ?>
							<?php	if ($country['id'] == $church['country_id']): ?>
								<option value="<?php echo $church['id']; ?>" <?php echo ($filter['church_id'] == $church['id']) ? "selected" : ""; ?>><?php echo $church['name']; ?></option>
							<?php endif; ?>
						<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
				</select>
			</td>	
			<td><input type="submit" name="set_filter" value="<?php echo lang('filter'); ?>"/>&nbsp;<input type="submit" name="cancel_filter" value="<?php echo lang('filter-cancel'); ?>"/></td>
			</tr>
			
			<?php	foreach ($participants as $participant): ?>
				<tr>
				<td><input type="checkbox" name="ids[]" value="<?php echo $participant['id']; ?>"></td>
				<td><?php echo custom_datetime($datetime_format, $participant['registration_date']); ?></td>
				<td><?php echo $participant['surname'] . " " . $participant['name']; ?></td>
				<td>
				<?php 
				if ($countries != NULL)
				{
					foreach ($countries as $country)
					{
						if ($country['id'] == $participant['country_id'])
						{
							echo $country['name'];
							break;
						}
					}
				}
				?>
				</td>
				<td>
				<?php 
				if ($cities != NULL)
				{
					foreach ($cities as $city)
					{
						if ($city['id'] == $participant['city_id'])
						{
							echo $city['name'];
							break;
						}
					}
				}
				?>
				</td>
				<td>
				<?php 
				if ($churches != NULL)
				{
					foreach ($churches as $church)
					{
						if ($church['id'] == $participant['church_id'])
						{
							echo $church['name'];
							break;
						}
					}
				}
				?>
				</td>
				<td>
				<?php
				foreach ($anchors as $anchor)
					printf($anchor . "&nbsp;", $participant['id']);
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
	<?php echo $pagination_links; ?>