	<?php echo validation_errors(); ?>
	<table id="form">
		<tbody>
			<tr>
			<th><?php echo lang('countries-name'); ?> *</th>
			<td><input type="text" name="data[name]" value="<?php echo $data['name']; ?>"/></td>
			</tr>
		</tbody>
	</table>	