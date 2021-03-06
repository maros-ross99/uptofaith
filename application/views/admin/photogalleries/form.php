	<script type="text/javascript">
	$(document).ready(function()
	{
		$("a[rel=photos]").fancybox();
	});
	</script>
	<?php echo validation_errors(); ?>
	<table id="form">
		<tbody>
			<tr>
			<th><?php echo lang('photogalleries-event'); ?></th>
			<td><?php echo $event_active['name']; ?></td>
			</tr>
			
			<tr>		
			<th><?php echo lang('photogalleries-visible'); ?></th>
			<td><input type="checkbox" name="data[visible]" value="1" <?php echo (!empty($data['visible']) ? "checked" : ""); ?> /></td>
			</tr>
			
			<?php if (!array_key_exists('id', $data)) : ?>
			<tr>
			<th><?php echo lang('photogalleries-news-add'); ?></th>
			<td><input type="checkbox" name="data[news_add]" value="1" <?php echo (!empty($data['news_add']) ? "checked" : ""); ?>/></td>
			</tr>
			<?php endif; ?>
			
			<tr>
			<th><?php echo lang('photogalleries-name'); ?> *</th>
			<td><input type="text" name="data[name]" value="<?php echo $data['name']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('photogalleries-description'); ?></th>
			<td><textarea class="ckeditor" name="data[description]"><?php echo $data['description']; ?></textarea></td>
			</tr>
			
			<?php if (array_key_exists('id', $data)) : ?>
			<tr>
			<th><?php echo lang('photogalleries-photos'); ?><br /><br /><input type="checkbox" id="check_all" value="data[photos][]" />&nbsp;<label><?php echo lang('check-all'); ?></label></th>
			<td>
			<?php	foreach ($photos as $name => $photo): ?>
				<div class="photo">
					<a rel="photos" href="<?php echo site_url($photo['original']); ?>"><img src="<?php echo site_url($photo['thumbnail']); ?>" style="width:200px;" /></a>
					<br />
					<div class="control"><input type="checkbox" name="data[photos][]" value="<?php echo $name; ?>" /><label><?php echo lang('remove'); ?></label></div>
				</div>
			<?php endforeach; ?>
			</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>	