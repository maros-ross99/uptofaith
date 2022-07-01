<script type="text/javascript">
//<!--
var $checktree;

$(document).ready(function()
{
	$checktree = $('#selector').checkTree();
});
//-->
</script>

	<?php echo validation_errors(); ?>
	<table id="form">
		<tbody>
			<tr>
			<th><?php echo lang('messages-event'); ?></th>
			<td><?php echo $event_active['name']; ?></td>
			</tr>
			<tr>
			<th><?php echo lang('messages-recipients'); ?> *</th>
			<td style="float:none;">
			<ul id="selector" class="tree">
				<li><input type="checkbox" name="data[all]" value="true" /><label><?php echo lang('messages-recipients-all'); ?></label>
					<ul>
					<?php foreach ($countries as $country): ?>
					<li><input type="checkbox" name="data[countries][]" value="<?php echo $country['id']; ?>" /><label><?php echo $country['name']; ?></label>
						<ul>
						<li class="description"><?php echo lang('messages-recipients-cities'); ?></li>
						<?php foreach ($cities as $city): ?>
							<?php if ($country['id'] == $city['country_id']): ?>
								<li><input type="checkbox" name="data[cities][]" value="<?php echo $city['id']; ?>" /><label><?php echo $city['name']; ?></label></li>
							<?php endif; ?>
						<?php endforeach; ?>
						
						<li class="description"><?php echo lang('messages-recipients-churches'); ?></li>
						<?php foreach ($churches as $church): ?>
							<?php if ($country['id'] == $church['country_id']): ?>
								<li><input type="checkbox" name="data[churches][]" value="<?php echo $church['id']; ?>" /><label><?php echo $church['name']; ?></label></li>
							<?php endif; ?>
						<?php endforeach; ?>
						</ul>
					</li>
					<?php endforeach; ?>
					</ul>
				</li>
			</ul>
			</td>
			</tr>
			<tr>
			<th><?php echo lang('messages-subject'); ?> *</th>
			<td><input type="text" name="data[subject]" value="<?php echo $data['subject']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('messages-message'); ?> *</th>
			<td><textarea class="ckeditor" name="data[message]"><?php echo $data['message']; ?></textarea></td>
			</tr>
		</tbody>
	</table>	