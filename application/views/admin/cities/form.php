	<?php echo validation_errors(); ?>
	<table id="form">
		<tbody>
			<tr>
			<th><?php echo lang('cities-name'); ?> *</th>
			<td><input type="text" name="data[name]" value="<?php echo $data['name']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('cities-country'); ?> *</th>
			<td>
				<select name="data[country_id]">
					<option value=""></option>
					<?php	foreach ($countries as $country): ?>
					<option value="<?php echo $country['id']; ?>" <?php echo ($data['country_id'] == $country['id']) ? "selected" : ""; ?>><?php echo $country['name']; ?></option>
					<?php endforeach; ?>
				</select>
			</td>
			</tr>
		</tbody>
	</table>	