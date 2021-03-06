	<div class="msg-info"><?php echo lang('statistics-note'); ?></div>
	<table id="view">
		<tbody>
			<tr>
			<th colspan="2" class="section_description"><?php echo lang('statistics-total'); ?></th>			
			</tr>
			<tr>
			<td colspan="2"><?php printf(lang('statistics-total-text'), $count_all_participants); ?></td>
			</tr>
			<tr>
			<th colspan="2" class="section_description"><?php echo lang('statistics-last'); ?></th>			
			</tr>
			<tr>
			<td colspan="2"><?php printf(lang('statistics-last-day'), $last_day_added_participants, $last_day_added_groups); ?></td>
			</tr>
			<tr>
			<td colspan="2"><?php printf(lang('statistics-last-week'), $last_week_added_participants, $last_week_added_groups); ?></td>
			</tr>
			<tr>
			<td colspan="2"><?php printf(lang('statistics-last-month'), $last_month_added_participants, $last_month_added_groups); ?></td>
			</tr>
			
			<tr>
			<th colspan="2"><?php echo lang('statistics-course-registration'); ?></th>			
			</tr>
			<?php if (count($statistics_course_registration) > 0): ?>
				<tr>
					<?php
			      $data_array = array();
			      
			      foreach ($statistics_course_registration as $item)
			      	$data_array[] = "['" . date("d.m.Y", $item['date']) . "', " . $item['count'] . "]";
			      
			      $data_course = implode(",", $data_array);
			      ?>
					<script type="text/javascript" language="javascript">
					$(document).ready(function()
					{
						var data_course = [<?php echo $data_course; ?>];
						var plot_course = $.jqplot('chart_course_registration', [data_course], 
						{
							axesDefaults: { pad: 0, min: 0,},
							axes:
							{
								xaxis:
								{
									renderer: $.jqplot.DateAxisRenderer,
									tickOptions: { formatString: '%d.%m.%Y' },

									min: '<?php echo date("d.m.Y", $statistics_course_registration[0]['date']); ?>',
									max: '<?php echo date("d.m.Y", $statistics_course_registration[count($statistics_course_registration) - 1]['date']); ?>',
								},
								yaxis:
								{
									tickOptions: { formatString:'%d' },
									min: 0,	
								}
							},
							series:[{ lineWidth: 2 }],
							highlighter: 
							{
					        show: true,
					        sizeAdjust: 7.5,
					      },
					      cursor:
					      {
					        show: false,
					      }
						});
					});
					</script>
				<td colspan="2"><div id="chart_course_registration" class="chart" style="width:96%;height:250px;"></div></td>
				</tr>
			<?php endif; ?>
			
			<tr>
			<th><?php echo lang('statistics-gender'); ?></th>
			<th><?php echo lang('statistics-countries'); ?></th>			
			</tr>
			<?php if ((count($statistics_countries) > 0) || (count($statistics_gender) > 0)): ?>
				<tr>
					<?php
			      $data_array = array();
			      
			      foreach ($statistics_countries as $statistics_country)
			      	$data_array[] = "['" . $statistics_country['name'] . "', " . $statistics_country['count'] . "]";
			      
			      $data = implode(",", $data_array);
			      ?>
			      
			      <?php if (!empty($data)): ?>
						<script type="text/javascript" language="javascript">
						$(document).ready(function()
						{
							var data_countries = [<?php echo $data; ?>];
							  
							var plot_countries = jQuery.jqplot ('chart_countries', [data_countries], 
							{ 
								seriesDefaults:
								{
									// Make this a pie chart.
									renderer: jQuery.jqplot.PieRenderer, 
									rendererOptions:
									{
										// Put data labels on the pie slices.
										// By default, labels show the percentage of the slice.
										showDataLabels: true,
										dataLabels: 'value',
									}
								}, 
								legend: { show:true, location: 'e' }
							});
						});
						</script>
					<?php endif; ?>

					<script type="text/javascript" language="javascript">
					$(document).ready(function()
					{
						var data_gender = [<?php echo "['" . lang('statistics-gender-men') . "', " . $statistics_gender['men'] . "]" . ", ['" . lang('statistics-gender-women') . "', " . $statistics_gender['women'] . "]"; ?>];
						  
						var plot_gender = jQuery.jqplot ('chart_gender', [data_gender], 
						{ 
							seriesDefaults:
							{
								// Make this a pie chart.
								renderer: jQuery.jqplot.PieRenderer, 
								rendererOptions:
								{
									// Put data labels on the pie slices.
									// By default, labels show the percentage of the slice.
									showDataLabels: true,
									dataLabels: 'value',
								}
							}, 
							legend: { show:true, location: 'e' }
						});
					});
					</script>			
				<td><div id="chart_gender" class="chart" style="height:220px;"></div></td>
				<td><div id="chart_countries" class="chart" style="height:220px;"></div></td>
				</tr>
			<?php endif; ?>
			<tr>
			<th><?php echo lang('statistics-cities'); ?></th>
			<th><?php echo lang('statistics-churches'); ?></th>
			</tr>
			<tr>
			<td></td>
			<td><?php printf(lang('statistics-no-church'), $statistics_no_church); ?></td>
			</tr>
			<?php	foreach ($statistics_countries as $statistics_country): ?>
				<?php
		      $cities_data_array = array();
		      $churches_data_array = array();
		      
		      foreach ($statistics_cities as $statistics_city)
		      	if ($statistics_country['id'] == $statistics_city['country_id'])
		      		$cities_data_array[] = "[" . $statistics_city['count'] . ", '" . $statistics_city['name'] . "']";
		      
		      foreach ($statistics_churches as $statistics_church)
		      	if ($statistics_country['id'] == $statistics_church['country_id'])
						$churches_data_array[] = "[" . $statistics_church['count'] . ", '" . $statistics_church['name'] . "']";
		      
		      $cities_data = implode(",", $cities_data_array);
		      $churches_data = implode(",", $churches_data_array);
		      ?>
		      
		      <?php if (!empty($cities_data) || !empty($churches_data)): ?>
		      	<tr>
						<th colspan="2" class="section_description"><?php echo $statistics_country['name']; ?></th>			
					</tr>
					<tr>
					<script type="text/javascript" language="javascript">
					$(document).ready(function()
					{
						$.jqplot.config.enablePlugins = true;
					
						var data_city = [<?php echo $cities_data; ?>];
						  
						var plot_city = $.jqplot('chart_cities_<?php echo $statistics_country['id']; ?>', [data_city],
						{
							seriesDefaults:
							{
								renderer:$.jqplot.BarRenderer,
								// Show point labels to the right ('e'ast) of each bar.
								// edgeTolerance of -15 allows labels flow outside the grid
								// up to 15 pixels.  If they flow out more than that, they 
								// will be hidden.
								pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
								// Rotate the bar shadow as if bar is lit from top right.
								shadowAngle: 135,
								color: '#c5b47f',
								// Here's where we tell the chart it is oriented horizontally.
								rendererOptions: { barDirection: 'horizontal' }
							},
						  	axes:
						  	{
						  		xaxis:
						  		{
						  			min: 0,
						  			tickOptions: { formatString:'%d' },
						  		},
								yaxis: { renderer: $.jqplot.CategoryAxisRenderer }
						  	},
						  	highlighter: {show: false},
					      cursor: {show: false},
						});		  
	
	
						var data_church = [<?php echo $churches_data; ?>];
						  
						var plot_church = $.jqplot('chart_churches_<?php echo $statistics_country['id']; ?>', [data_church],
						{
							seriesDefaults:
							{
								renderer:$.jqplot.BarRenderer,
								// Show point labels to the right ('e'ast) of each bar.
								// edgeTolerance of -15 allows labels flow outside the grid
								// up to 15 pixels.  If they flow out more than that, they 
								// will be hidden.
								pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
								// Rotate the bar shadow as if bar is lit from top right.
								shadowAngle: 135,
								color: '#579575',
								// Here's where we tell the chart it is oriented horizontally.
								rendererOptions: { barDirection: 'horizontal' }
							},
						  	axes:
						  	{
						  		xaxis:
						  		{
						  			min: 0,
						  			tickOptions: { formatString:'%d' },
						  		},
								yaxis: { renderer: $.jqplot.CategoryAxisRenderer }
						  	},
						  	highlighter: {show: false},
					      cursor: {show: false},
						});		  
					});
					</script>
					<td><div id="chart_cities_<?php echo $statistics_country['id']; ?>" class="chart" style="height:<?php echo (30 + 48*count($cities_data_array)); ?>px;"></div><br />&nbsp;</td>
					<td><div id="chart_churches_<?php echo $statistics_country['id']; ?>" class="chart" style="height:<?php echo (30 + 68*count($churches_data_array)); ?>px;"></div><br />&nbsp;</td>
					</tr>
				<?php endif; ?>
			<?php endforeach; ?>
		</tbody>
	</table>	