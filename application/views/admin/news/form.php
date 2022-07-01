	<?php echo validation_errors(); ?>
	<table id="form">
		<tbody>
			<tr>		
			<th><?php echo lang('news-visible'); ?></th>
			<td><input type="checkbox" name="data[visible]" value="1" <?php echo (!empty($data['visible']) ? "checked" : ""); ?> /></td>
			</tr>
			<tr>
			<th><?php echo lang('news-title'); ?> *</th>
			<td><input type="text" name="data[title]" value="<?php echo $data['title']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('news-content'); ?> *</th>
			<td><textarea class="ckeditor" name="data[content]"><?php echo $data['content']; ?></textarea></td>
			</tr>
		</tbody>
	</table>	