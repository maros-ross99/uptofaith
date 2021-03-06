<div class="cool-stred2 box">
	<form id="skupina" class="appnitro"  method="post" action="">

	<h2>Registrace - skupiny</h2>
	
	<ul>
	<li id="li_1" >	
		<label for="element_3_1">Událost</label>
		<div class="right">
		<select class="element select medium" id="element_3_6" name="element_3_6"> 
			<option value="" selected="selected"></option>
			<?php	foreach ($events as $event): ?>			
				<option value="<?php echo $event['id']; ?>" ><?php echo $event['name']; ?></option>
			<?php endforeach; ?>
			<option value="">vše</option>
		</select>
		</div>
	</li>
	
	<li id="li_2" >
		<label class="description" for="element_2">Jméno a příjmení zástupce</label>
		<span>
		<input id="element_2_1" name= "element_2_1" class="element text" maxlength="255" size="25" value=""/>
		</span>
	</li>
	
	<li id="li_4" >
		<label class="description" for="element_4">Email zástupce</label>
		<div>
		<input id="element_4" name="element_4" class="element text medium" type="text" maxlength="255" size="25" value=""/> 
		</div>		 
	</li>	
	
	<li id="li_2" >
		<label class="description" for="element_2">Počet osob </label>
		<span>
			<input id="element_2_1" name= "element_2_1" class="element text" maxlength="2" size="5" value=""/>
		</span>
	</li>
	
	<li id="li_3" >
	 	<label for="element_3_1">Stát</label>
		<div class="right">
		<select class="element select medium" id="element_3_6" name="element_3_6">
			<option value="" selected="selected"></option>
			<?php	foreach ($countries as $country): ?>			
				<option value="<?php echo $country['id']; ?>" ><?php echo $country['name']; ?></option>
			<?php endforeach; ?>
		</select>
		</div>
	</li>
	
	<li id="li_4" >
		<label for="element_3_1">Město</label>
		<div class="right">
		<select class="element select medium" id="element_3_6" name="element_3_6"> 
			<option value="" selected="selected"></option>
			<?php	foreach ($cities as $city): ?>			
				<option value="<?php echo $city['id']; ?>" ><?php echo $city['name']; ?></option>
			<?php endforeach; ?>
		</select>
		</div>
	</li>
	
	<li id="li_4" >
		<label for="element_3_1">Sbor</label>
		<div class="right">
		<select class="element select medium" id="element_3_6" name="element_3_6"> 
			<option value="" selected="selected"></option>
			<?php	foreach ($churches as $church): ?>			
				<option value="<?php echo $church['id']; ?>" ><?php echo $church['name']; ?></option>
			<?php endforeach; ?>
		</select>
		</div>
	</li>	

	<li id="li_5" >
		<label class="description" for="element_5">Poznámka </label>
		<div>
		<textarea name="note" cols="30" rows="3"></textarea> 
		</div> 
	</li>
			
	<li class="buttons">
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Registrovat" />
	</li>
	</ul>

	</form>	

	<!-- IMAGE FLOATING -->
    <p class="box"><br /><br />&nbsp;</p>    
</div><!-- /col-content -->