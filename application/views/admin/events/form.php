	<?php echo validation_errors(); ?>
	<script type="text/javascript">
	$(function()
	{
		$('#timepicker_from').datetimepicker(
		{
		    onClose: function(dateText, inst)
		    {
		        var endDateTextBox = $('#timepicker_to');
		        if (endDateTextBox.val() != '')
		        {
		            var testStartDate = new Date(dateText);
		            var testEndDate = new Date(endDateTextBox.val());
		            if (testStartDate > testEndDate)
		                endDateTextBox.val(dateText);
		        }
		        else
		        {
		            endDateTextBox.val(dateText);
		        }
		    },
		    onSelect: function (selectedDateTime)
		    {
		        var start = $(this).datetimepicker('getDate');
		        $('#timepicker_to').datetimepicker('option', 'minDate', new Date(start.getTime()));
		    }
		});
		$('#timepicker_to').datetimepicker(
		{
		    onClose: function(dateText, inst)
		    {
		        var startDateTextBox = $('#timepicker_from');
		        if (startDateTextBox.val() != '')
		        {
		            var testStartDate = new Date(startDateTextBox.val());
		            var testEndDate = new Date(dateText);
		            if (testStartDate > testEndDate)
		                startDateTextBox.val(dateText);
		        }
		        else
		        {
		            startDateTextBox.val(dateText);
		        }
		    },
		    onSelect: function (selectedDateTime)
		    {
		        var end = $(this).datetimepicker('getDate');
		        $('#timepicker_from').datetimepicker('option', 'maxDate', new Date(end.getTime()) );
		    }
		});
		
		$('#timepicker_registration_from').datetimepicker(
		{
		    onClose: function(dateText, inst)
		    {
		        var endDateTextBox = $('#timepicker_registration_to');
		        if (endDateTextBox.val() != '')
		        {
		            var testStartDate = new Date(dateText);
		            var testEndDate = new Date(endDateTextBox.val());
		            if (testStartDate > testEndDate)
		                endDateTextBox.val(dateText);
		        }
		        else
		        {
		            endDateTextBox.val(dateText);
		        }
		    },
		    onSelect: function (selectedDateTime)
		    {
		        var start = $(this).datetimepicker('getDate');
		        $('#timepicker_registration_to').datetimepicker('option', 'minDate', new Date(start.getTime()));
		    }
		});
		$('#timepicker_registration_to').datetimepicker(
		{
		    onClose: function(dateText, inst)
		    {
		        var startDateTextBox = $('#timepicker_registration_from');
		        if (startDateTextBox.val() != '')
		        {
		            var testStartDate = new Date(startDateTextBox.val());
		            var testEndDate = new Date(dateText);
		            if (testStartDate > testEndDate)
		                startDateTextBox.val(dateText);
		        }
		        else
		        {
		            startDateTextBox.val(dateText);
		        }
		    },
		    onSelect: function (selectedDateTime)
		    {
		        var end = $(this).datetimepicker('getDate');
		        $('#timepicker_registration_from').datetimepicker('option', 'maxDate', new Date(end.getTime()) );
		    }
		});
	});
	</script>
	<table id="form">
		<tbody>
			<tr>
			<th colspan="2" class="section_description"><?php echo lang('events-basic-information'); ?></th>			
			</tr>
			<tr>		
			<th><?php echo lang('events-active'); ?><p class="small"><?php echo lang('events-active-note'); ?></p></th>
			<td><input type="checkbox" name="data[active]" value="1" <?php echo (!empty($data['active']) ? "checked" : ""); ?> /></td>
			</tr>
			<tr>
			<th><?php echo lang('events-name'); ?> *<p class="small"><?php echo lang('events-name-note'); ?> </p></th>
			<td><input type="text" name="data[name]" value="<?php echo $data['name']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('events-place'); ?> *<p class="small"><?php echo lang('events-place-note'); ?></p></th>
			<td><input type="text" name="data[place]" value="<?php echo $data['place']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('events-map'); ?><p class="small"><?php echo lang('events-map-note'); ?></p></th>
			<td><input type="text" name="data[place_map]" value="<?php echo $data['place_map']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('events-gps-coords'); ?></th>
			<td><input type="text" name="data[gps_coordinates]" value="<?php echo $data['gps_coordinates']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('events-from-date'); ?><p class="small"><?php echo lang('events-from-date-note'); ?></p></th>
			<td><input type="text" id="timepicker_from" style="width:120px;" name="data[from_date]" value="<?php echo ($data['from_date'] != 0) ? date($datetime_format, $data['from_date']) : ""; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('events-to-date'); ?></th>
			<td><input type="text" id="timepicker_to" style="width:120px;" name="data[to_date]" value="<?php echo ($data['to_date'] != 0) ? date($datetime_format, $data['to_date']) : ""; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('events-registration-from-date'); ?></th>
			<td><input type="text" id="timepicker_registration_from" style="width:120px;" name="data[registration_from_date]" value="<?php echo ($data['registration_from_date'] != 0) ? date($datetime_format, $data['registration_from_date']) : ""; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('events-registration-to-date'); ?></th>
			<td><input type="text" id="timepicker_registration_to" style="width:120px;" name="data[registration_to_date]" value="<?php echo ($data['registration_to_date'] != 0) ? date($datetime_format, $data['registration_to_date']) : ""; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('events-description'); ?></th>
			<td><textarea class="ckeditor" name="data[description]"><?php echo $data['description']; ?></textarea></td>
			</tr>
			<tr>
			<th colspan="2" class="section_description"><?php echo lang('events-search-information'); ?></th>			
			</tr>
			<tr>
			<th><?php echo lang('events-meta-keywords'); ?><p class="small"><?php echo lang('events-meta-keywords-note'); ?></p></th>
			<td><textarea name="data[meta_keywords]"><?php echo $data['meta_keywords']; ?></textarea></td>
			</tr>
			<tr>		
			<th><?php echo lang('events-meta-description'); ?><p class="small"><?php echo lang('events-meta-description-note'); ?></p></th>
			<td><textarea name="data[meta_description]"><?php echo $data['meta_description']; ?></textarea></td>
			</tr>
		</tbody>
	</table>	