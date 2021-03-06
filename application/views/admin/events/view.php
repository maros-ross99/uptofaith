	<input type="hidden" name="ids[]" value="<?php echo $id; ?>">
	<table id="view">
		<tbody>
			<tr>
			<th colspan="2" class="section_description"><?php echo lang('events-basic-information'); ?></th>			
			</tr>
			<tr>
			<th><?php echo lang('events-active'); ?></th>
			<td><?php echo "<img src=\"" . (($data['active']) ? site_url("images/admin/accept.png") : site_url("images/admin/cross.png")) . "\" />"; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('events-name'); ?></th>
			<td><?php echo $data['name']; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('events-place'); ?></th>
			<td><?php echo $data['place']; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('events-map'); ?></th>
			<td><a href="<?php echo $data['place_map']; ?>" target="_blank"><?php echo $data['place_map']; ?></a></td>
			</tr>
			<tr>
			<th><?php echo lang('events-gps-coords'); ?></th>
			<td><?php echo $data['gps_coordinates']; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('events-from-date'); ?></th>
			<td><?php echo ($data['from_date'] != 0) ? custom_datetime($datetime_format, $data['from_date']) : ""; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('events-to-date'); ?></th>
			<td><?php echo ($data['to_date'] != 0) ? custom_datetime($datetime_format, $data['to_date']) : ""; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('events-registration-from-date'); ?></th>
			<td><?php echo ($data['registration_from_date'] != 0) ? custom_datetime($datetime_format, $data['registration_from_date']) : ""; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('events-registration-to-date'); ?></th>
			<td><?php echo ($data['registration_to_date'] != 0) ? custom_datetime($datetime_format, $data['registration_to_date']) : ""; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('events-description'); ?></th>
			<td><?php echo unescape($data['description']); ?></td>
			</tr>
			
			<tr>
			<th colspan="2" class="section_description"><?php echo lang('events-search-information'); ?></th>			
			</tr>
			<tr>
			<th><?php echo lang('events-meta-keywords'); ?></th>
			<td><?php echo $data['meta_keywords']; ?></td>
			</tr>
			<tr>		
			<th><?php echo lang('events-meta-description'); ?></th>
			<td><?php echo $data['meta_description']; ?></td>
			</tr>
			
		</tbody>
	</table>	