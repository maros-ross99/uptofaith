	<?php echo $message; ?>
	<?php echo validation_errors(); ?>
	<?php echo lang('options-note'); ?>
	<table id="form">
		<tbody>
			<tr>
			<th colspan="2" class="section_description"><?php echo lang('options-general-options'); ?></th>			
			</tr>
			<tr>
			<th><?php echo lang('options-site-title'); ?> *</th>
			<td><input type="text" name="data[site_title]" value="<?php echo $data['site_title']; ?>"/></td>
			</tr>
			<tr>		
			<th><?php echo lang('options-welcome-text'); ?> *<p class="small"><?php echo lang('options-welcome-text-note'); ?></p></th>
			<td><textarea class="ckeditor" name="data[site_welcome_text]"><?php echo $data['site_welcome_text']; ?></textarea></td>
			</tr>
			
			<tr>
			<th colspan="2" class="section_description"><?php echo lang('options-about-section'); ?></th>			
			</tr>
			<tr>		
			<th><?php echo lang('options-about-text'); ?> *</th>
			<td><textarea class="ckeditor" name="data[site_about_text]"><?php echo $data['site_about_text']; ?></textarea></td>
			</tr>
			
			<tr>
			<th colspan="2" class="section_description"><?php echo lang('options-contact-section'); ?></th>			
			</tr>
			<tr>		
			<th><?php echo lang('options-contact-text'); ?> *</th>
			<td><textarea class="ckeditor" name="data[site_contact_text]"><?php echo $data['site_contact_text']; ?></textarea></td>
			</tr>
			
			<tr>
			<th colspan="2" class="section_description"><?php echo lang('options-aside-section'); ?></th>			
			</tr>
			<tr>		
			<th><?php echo lang('options-aside-text'); ?><p class="small"><?php echo lang('options-aside-text-note'); ?></p></th>
			<td><textarea class="ckeditor" name="data[site_aside_text]"><?php echo $data['site_aside_text']; ?></textarea></td>
			</tr>
			
			<tr>
			<th colspan="2" class="section_description"><?php echo lang('options-messages-data'); ?></th>			
			</tr>
			<tr>
			<th><?php echo lang('options-messages-name'); ?> *<p class="small"><?php echo lang('options-messages-name-note'); ?></p></th>
			<td><input type="text" name="data[site_messages_name]" value="<?php echo $data['site_messages_name']; ?>"/></td>
			</tr>
			<tr>
			<th><?php echo lang('options-messages-email'); ?> *<p class="small"><?php echo lang('options-messages-email-note'); ?></p></th>
			<td><input type="text" name="data[site_messages_email]" value="<?php echo $data['site_messages_email']; ?>"/></td>
			</tr>

			<tr>
			<th colspan="2" class="section_description"><?php echo lang('options-search-information'); ?></th>			
			</tr>
			<tr>
			<th><?php echo lang('options-meta-keywords'); ?> *<p class="small"><?php echo lang('options-meta-keywords-note'); ?></p></th>
			<td><textarea name="data[site_meta_keywords]"><?php echo $data['site_meta_keywords']; ?></textarea></td>
			</tr>
			<tr>		
			<th><?php echo lang('options-meta-description'); ?> *<p class="small"><?php echo lang('options-meta-description-note'); ?></p></th>
			<td><textarea name="data[site_meta_description]"><?php echo $data['site_meta_description']; ?></textarea></td>
			</tr>
			
			<tr>
			<th colspan="2" class="section_description"><?php echo lang('options-save-settings'); ?></th>			
			</tr>
			<tr>
			<th><?php echo lang('options-password'); ?> *</th>
			<td><input type="password" name="data[password]" value="" /></td>
			</tr>
		</tbody>
	</table>	