	<script type="text/javascript">
	$(document).ready(function()
	{
		$("a[rel=photos]").fancybox();
	});
	</script>
	<?php echo validation_errors(); ?>
	<?php echo $message; ?>
	<table id="form">
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
			<th><?php echo lang('photogalleries-photogallery-name'); ?></th>
			<td><?php echo $data['name']; ?></td>
			</tr>
			<tr>
			<th rowspan="2"><?php echo lang('photogalleries-photos'); ?><p class="small"><?php echo lang('photogalleries-photos-note'); ?></p></th>
			<td>
			<input type="file" name="photos[]" accept="image/*" multiple=""/>
			<!--
			<div id="upload">
				<span id="buttonPlaceHolder"></span>
				<ul id="log"></ul>
			</div>
			-->
			</td>
			</tr>
			<tr>
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