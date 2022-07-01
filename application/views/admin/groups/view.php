	<input type="hidden" name="ids[]" value="<?php echo $data['id']; ?>">	
	<table id="view">
		<tbody>
			<tr>
			<th><?php echo lang('groups-event'); ?></th>
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
			<th><?php echo lang('groups-date'); ?></th>
			<td><?php echo custom_datetime($datetime_format, $data['registration_date']); ?></td>
			</tr>
			<tr>
			<th><?php echo lang('groups-count'); ?></th>
			<td><?php echo $data['count_men']; ?>&nbsp;<?php echo lang('groups-count-men');?>, <?php echo $data['count_women']; ?>&nbsp;<?php echo lang('groups-count-women');?></td>
			</tr>
			<tr>
			<th><?php echo lang('groups-surname-and-name'); ?></th>
			<td><?php echo $data['surname'] . " " . $data['name']; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('groups-email'); ?></th>
			<td><?php echo $data['email']; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('groups-country'); ?></th>
			<td>
			<?php 
			if ($countries != NULL)
			{
				foreach ($countries as $country)
				{
					if ($country['id'] == $data['country_id'])
					{
						echo $country['name'];
						break;
					}
				}
			}
			?>
			</td>
			</tr>
			<tr>
			<th><?php echo lang('groups-city'); ?></th>
			<td>
			<?php 
			if ($cities != NULL)
			{
				foreach ($cities as $city)
				{
					if ($city['id'] == $data['city_id'])
					{
						echo $city['name'];
						break;
					}
				}
			}
			?>
			</td>
			</tr>
			<tr>
			<th><?php echo lang('groups-church'); ?></th>
			<td>
			<?php 
			if ($churches != NULL)
			{
				foreach ($churches as $church)
				{
					if ($church['id'] == $data['church_id'])
					{
						echo $church['name'];
						break;
					}
				}
			}
			?>
			</td>
			</tr>
			<tr>
			<th><?php echo lang('groups-note'); ?></th>
			<td><?php echo $data['note']; ?></td>
			</tr>			
		</tbody>
	</table>