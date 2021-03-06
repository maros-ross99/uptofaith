	<input type="hidden" name="ids[]" value="<?php echo $data['id']; ?>">	
	<table id="view">
		<tbody>
			<tr>
			<th><?php echo lang('videos-event'); ?></th>
			<td>
			<?php 
			foreach($events as $event)
			{
				if ($event['id'] == $data['event_id'])
				{
					echo $event['name'];
					break;
				}
			}
			?>
			</td>
			</tr>
			
			<tr>
			<th><?php echo lang('videos-visible'); ?></th>
			<td><?php echo "<img src=\"" . (($data['visible']) ? site_url("images/admin/accept.png") : site_url("images/admin/cross.png")) . "\" />"; ?></td>
			</tr>
			
			<tr>
			<th><?php echo lang('videos-date'); ?></th>
			<td><?php echo custom_datetime($datetime_format, $data['date']); ?></td>
			</tr>
			<tr>
			<th><?php echo lang('videos-name'); ?></th>
			<td><?php echo $data['name']; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('videos-description'); ?></th>
			<td><?php echo unescape($data['description']); ?></td>
			</tr>
			<tr>
			<th><?php echo lang('videos-code'); ?></th>
			<td><?php echo unescape($data['code']); ?></td>
			</tr>
		</tbody>
	</table>	