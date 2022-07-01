	<input type="hidden" name="ids[]" value="<?php echo $data['id']; ?>">	
	<table id="view">
		<tbody>
			<tr>
			<th><?php echo lang('news-visible'); ?></th>
			<td><?php echo "<img src=\"" . (($data['visible']) ? site_url("images/admin/accept.png") : site_url("images/admin/cross.png")) . "\" />"; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('news-date'); ?></th>
			<td><?php echo custom_datetime($datetime_format, $data['date']); ?></td>
			</tr>
			<tr>
			<th><?php echo lang('news-title'); ?></th>
			<td><?php echo $data['title']; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('news-content'); ?></th>
			<td><?php echo unescape($data['content']); ?></td>
			</tr>			
		</tbody>
	</table>	