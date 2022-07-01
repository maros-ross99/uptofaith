	<script type="text/javascript">
	$(document).ready(function()
	{
		$("a[rel=photos]").fancybox();
	});
	</script>
	<input type="hidden" name="ids[]" value="<?php echo $data['id']; ?>">	
	<table id="view">
		<tbody>
			<tr>
			<th><?php echo lang('photogalleries-event'); ?></th>
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
			<th><?php echo lang('photogalleries-visible'); ?></th>
			<td><?php echo "<img src=\"" . (($data['visible']) ? site_url("images/admin/accept.png") : site_url("images/admin/cross.png")) . "\" />"; ?></td>
			</tr>
			
			<tr>
			<th><?php echo lang('photogalleries-date'); ?></th>
			<td><?php echo custom_datetime($datetime_format, $data['date']); ?></td>
			</tr>
			<tr>
			<th><?php echo lang('photogalleries-name'); ?></th>
			<td><?php echo $data['name']; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('photogalleries-description'); ?></th>
			<td><?php echo unescape($data['description']); ?></td>
			</tr>
			<tr>
			<th><?php echo lang('photogalleries-photos'); ?></th>
			<td>
			<?php	foreach ($photos as $photo): ?>
				<div class="photo">
					<a rel="photos" href="<?php echo site_url($photo['original']); ?>"><img src="<?php echo site_url($photo['thumbnail']); ?>" style="width:180px;" /></a>
				</div>
			<?php endforeach; ?>
			</td>
			</tr>
		</tbody>
	</table>	