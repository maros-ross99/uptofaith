	<input type="hidden" name="ids[]" value="<?php echo $data['id']; ?>">	
	<table id="view">
		<tbody>
			<tr>
			<th><?php echo lang('messages-event'); ?></th>
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
			<th><?php echo lang('messages-date'); ?></th>
			<td><?php echo custom_datetime($datetime_format, $data['date']); ?></td>
			</tr>
			<tr>
			<th><?php echo lang('messages-recipients'); ?></th>
			<td><?php echo $data['recipients']; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('messages-subject'); ?></th>
			<td><?php echo $data['subject']; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('messages-message'); ?></th>
			<td><?php echo unescape($data['message']); ?></td>
			</tr>			
		</tbody>
	</table>	