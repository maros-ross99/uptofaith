<div id="menu">
<ul id="nav" class="dropdown dropdown-horizontal">
	<li class="first">
		<p><?php echo lang('menu-shown-event'); ?>
			<select id="shown_event" style="width:80px;">
				<?php	foreach ($events as $event): ?>
				<option value="<?php echo $event['id'];?>" <?php if ($event['id'] == $event_active['id']) echo "selected"; ?>><?php echo $event['name'];?></option>
				<?php endforeach; ?>
			</select>
		</p>
	</li>

	<?php if ($admin['rights'][RIGHTS_NAME_STATISTICS] != RIGHTS_ACCESS_NONE): ?>
		<li><?php echo anchor("admin/statistics/", lang('menu-statistics')); ?></li>
	<?php endif; ?>
	
	<?php if ($admin['rights'][RIGHTS_NAME_NEWS] != RIGHTS_ACCESS_NONE): ?>
		<li><?php echo anchor("admin/news/", lang('menu-news')); ?></li>
	<?php endif; ?>
	
	<?php if ($admin['rights'][RIGHTS_NAME_MESSAGES] != RIGHTS_ACCESS_NONE): ?>
		<li><?php echo anchor("admin/messages/", lang('menu-messages')); ?></li>
	<?php endif; ?>
	
	<?php if ($admin['rights'][RIGHTS_NAME_PARTICIPANTS] != RIGHTS_ACCESS_NONE): ?>
		<li class="dir"><?php echo lang('menu-participants'); ?>
			<ul>
				<li class="first"><?php echo anchor("admin/participants/", lang('menu-participants-participants')); ?></li>
				<li class="last"><?php echo anchor("admin/groups/", lang('menu-participants-groups')); ?></li>	
			</ul>
		</li>
	<?php endif; ?>
	
	<?php if ($admin['rights'][RIGHTS_NAME_MEDIA] != RIGHTS_ACCESS_NONE): ?>
		<li class="dir"><?php echo lang('menu-media'); ?>
			<ul>
				<li class="first"><?php echo anchor("admin/photogalleries/", lang('menu-media-photogalleries')); ?></li>
				<li class="last"><?php echo anchor("admin/videos/", lang('menu-media-videos')); ?></li>	
			</ul>
		</li>
	<?php endif; ?>
	
	<?php if ($admin['rights'][RIGHTS_NAME_EVENTS] != RIGHTS_ACCESS_NONE): ?>
		<li><?php echo anchor("admin/events/", lang('menu-events')); ?></li>
	<?php endif; ?>
	
	<?php if ($admin['rights'][RIGHTS_NAME_CATALOG] != RIGHTS_ACCESS_NONE): ?>
		<li class="dir"><?php echo lang('menu-catalog'); ?>
			<ul>
				<li class="first"><?php echo anchor("admin/countries/", lang('menu-catalog-countries')); ?></li>
				<li><?php echo anchor("admin/cities/", lang('menu-catalog-cities')); ?></li>
				<li class="last"><?php echo anchor("admin/churches/", lang('menu-catalog-churches')); ?></li>		
			</ul>
		</li>
	<?php endif; ?>
	
	<?php if ($admin['rights'][RIGHTS_NAME_ADMINS] != RIGHTS_ACCESS_NONE): ?>
		<li><?php echo anchor("admin/admins/", lang('menu-admins')); ?></li>
	<?php endif; ?>
	
	<?php if ($admin['rights'][RIGHTS_NAME_OPTIONS] != RIGHTS_ACCESS_NONE): ?>	
		<li><?php echo anchor("admin/options/", lang('menu-options')); ?></li>
	<?php endif; ?>

	<li><?php echo anchor_img("admin/profile/", "images/admin/wrench_orange.png", lang('menu-profile')); ?></li>
	<li class="last"><?php echo anchor_img("admin/authentication/logout/", "images/admin/door_out.png", lang('menu-logout')); ?></li>
</ul>

</div>