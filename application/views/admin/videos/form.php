	<?php echo validation_errors(); ?>
	<table id="form">
		<tbody>
			<tr>
			<th><?php echo lang('videos-event'); ?></th>
			<td><?php echo $event_active['name']; ?></td>
			</tr>
			
			<tr>		
			<th><?php echo lang('videos-visible'); ?></th>
			<td><input type="checkbox" name="data[visible]" value="1" <?php echo (!empty($data['visible']) ? "checked" : ""); ?> /></td>
			</tr>

			<?php if (!array_key_exists('id', $data)) : ?>
			<tr>
			<th><?php echo lang('videos-news-add'); ?></th>
			<td><input type="checkbox" name="data[news_add]" value="1" <?php echo (!empty($data['news_add']) ? "checked" : ""); ?>/></td>
			</tr>
			<?php endif; ?>

			<tr>
			<th><?php echo lang('videos-name'); ?> *</th>
			<td><input type="text" name="data[name]" value="<?php echo $data['name']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('videos-description'); ?></th>
			<td><textarea class="ckeditor" name="data[description]"><?php echo $data['description']; ?></textarea></td>
			</tr>
			<tr>
			<th><?php echo lang('videos-code'); ?> *<p class="small"><?php echo lang('videos-code-note'); ?></p></th>
			<td><textarea name="data[code]"><?php echo $data['code']; ?></textarea></td>
			</tr>
		</tbody>
	</table>	