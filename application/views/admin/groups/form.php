	<?php echo validation_errors(); ?>
	<table id="form">
		<tbody>
			<tr>
			<th><?php echo lang('groups-event'); ?></th>
			<td><?php echo $event_active['name']; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('groups-count'); ?> *</th>
			<td>
			<input type="text" name="data[count_men]" value="<?php echo $data['count_men']; ?>" style="width:50px;" />&nbsp;<?php echo lang('groups-count-men');?>
			<input type="text" name="data[count_women]" value="<?php echo $data['count_women']; ?>" style="width:50px;" />&nbsp;<?php echo lang('groups-count-women');?>
			</td>
			</tr>
			<tr>
			<th><?php echo lang('groups-name'); ?> *</th>
			<td><input type="text" name="data[name]" value="<?php echo $data['name']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('groups-surname'); ?> *</th>
			<td><input type="text" name="data[surname]" value="<?php echo $data['surname']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('groups-email'); ?> *</th>
			<td><input type="text" name="data[email]" value="<?php echo $data['email']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('groups-city'); ?> *</th>
			<td>
				<select name="data[city_id]">
					<option value=""></option>
					<?php	foreach ($countries as $country): ?>
						<optgroup label="<?php echo $country['name']; ?>">
							<?php	foreach ($cities as $city): ?>
								<?php	if ($country['id'] == $city['country_id']): ?>
									<option value="<?php echo $city['id']; ?>" <?php echo ($data['city_id'] == $city['id']) ? "selected" : ""; ?>><?php echo $city['name']; ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
				</select>
			</td>
			</tr>
			<tr>
			<th><?php echo lang('groups-church'); ?></th>
			<td>
				<select name="data[church_id]">
					<option value=""></option>
					<?php	foreach ($countries as $country): ?>
						<optgroup label="<?php echo $country['name']; ?>">
						<?php	foreach ($churches as $church): ?>
							<?php	if ($country['id'] == $church['country_id']): ?>
								<option value="<?php echo $church['id']; ?>" <?php echo ($data['church_id'] == $church['id']) ? "selected" : ""; ?>><?php echo $church['name']; ?></option>
							<?php endif; ?>
						<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
				</select>
			</td>
			</tr>
			<tr>
			<th><?php echo lang('groups-note'); ?></th>
			<td><textarea name="data[note]"><?php echo $data['note']; ?></textarea></td>
			</tr>
		</tbody>
	</table>	