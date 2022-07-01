<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Up to Faith - administrace > <?php echo $caption; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="author" content="Maros Vano" />
<meta name="robots" content="noindex,nofollow">


<link href="<?php echo site_url("css/admin/default.css"); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo site_url("css/admin/jqplot/jquery.jqplot.min.css"); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo site_url("css/admin/jquery-ui/smoothness/jquery-ui-1.8.12.custom.css"); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo site_url("css/admin/checktree/jquery.checktree.css"); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo site_url("css/admin/fancybox/jquery.fancybox-1.3.4.css"); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo site_url("css/UItoTop/ui.totop.css"); ?>" rel="stylesheet" type="text/css" />


<script language="javascript" type="text/javascript" src="<?php echo site_url("ckeditor/ckeditor.js"); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo site_url("ckeditor/adapters/jquery.js"); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/kcfinder.js"); ?>"></script>

<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/jquery-1.7.2.min.js"); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/jquery-ui-1.8.12.custom.min.js"); ?>"></script>
<!--[if lt IE 7]>
<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/jquery.dropdown.js"); ?>"></script>
<![endif]-->
<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/jqplot/excanvas.js"); ?>"></script><![endif]-->
<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/jqplot/jquery.jqplot.min.js"); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/jqplot/plugins/jqplot.barRenderer.min.js"); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/jqplot/plugins/jqplot.pointLabels.min.js"); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/jqplot/plugins/jqplot.pieRenderer.min.js"); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/jqplot/plugins/jqplot.donutRenderer.min.js"); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/jqplot/plugins/jqplot.dateAxisRenderer.min.js"); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/jqplot/plugins/jqplot.highlighter.min.js"); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/jqplot/plugins/jqplot.cursor.min.js"); ?>"></script>

<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/checktree/jquery.checktree.js"); ?>"></script>

<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/timepicker/jquery-ui-timepicker-addon.js"); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/timepicker/jquery.ui.datepicker-cs.js"); ?>"></script>

<script language="javascript" type="text/javascript" src="<?php echo site_url("js/admin/fancybox/jquery.fancybox-1.3.4.pack.js"); ?>"></script>

<script language="javascript" type="text/javascript" src="<?php echo site_url("js/UItoTop/easing.js"); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo site_url("js/UItoTop/jquery.ui.totop.min.js"); ?>"></script>


<script language="javascript" type="text/javascript">

$(document).ready(function()
{
	CKEDITOR.editorConfig = function(config)
	{
		config.language = 'cs';
	
	   config.toolbar_site =
		[
			['Source','-','Preview'],
			['Undo','Redo','-','Find','Replace'],
			['Cut','Copy','Paste','PasteText','PasteFromWord'],			
			['Maximize', 'ShowBlocks', '-', 'SelectAll','RemoveFormat'],
			'/',
			['Format','Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
			['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			['TextColor','BGColor'],
			'/',
			['NumberedList','BulletedList','-','Outdent','Indent'],
			['Link','Unlink','Anchor'],
			['Image','Table','HorizontalRule','Smiley','SpecialChar'],
		];
		 
		config.toolbar = 'site';
		
	   config.fullPage = false;
	   config.htmlEncodeOutput = true;
		config.filebrowserBrowseUrl = "<?php echo site_url("kcfinder/browse.php?type=files"); ?>";
	   config.filebrowserImageBrowseUrl = "<?php echo site_url("kcfinder/browse.php?type=images"); ?>";
	   config.filebrowserFlashBrowseUrl = "<?php echo site_url("kcfinder/browse.php?type=flash"); ?>";
	   config.filebrowserUploadUrl = "<?php echo site_url("kcfinder/upload.php?type=files"); ?>";
	   config.filebrowserImageUploadUrl = "<?php echo site_url("kcfinder/upload.php?type=images"); ?>";
	   config.filebrowserFlashUploadUrl = "<?php echo site_url("kcfinder/upload.php?type=flash"); ?>";
	};
	
	$('textarea.ckeditor').each(function()
	{
		CKEDITOR.replace( $(this).attr('name') );
	});
	
	
	$('#check_all').click(function()
   {
        $("input[name='" + $(this).attr('value') + "']").attr('checked', $(this).is(':checked'));
   });

   $('input[type=button]').click(function()
   {
		window.location.href = "<?php echo base_url(); ?>" + $(this).attr('name');
    	return false;
	});
	
	$('input[type=submit]').click(function()
	{
		$(this).val("<?php echo lang('please-wait'); ?>");
		//$(this).attr("disabled", "true");
	});

	$().UItoTop({ easingType: 'easeOutQuart' });
	
	$('#shown_event').bind('change', function()
	{
		var url = $(this).val(); // get selected value
		
		if (url)
		{
			window.location = "<?php echo site_url("admin/events/set"); ?>/" + url; // redirect
		}
		
		return false;
	});

});
</script>

<?php
session_start();
$_SESSION['KCFINDER'] = array();
$_SESSION['KCFINDER']['disabled'] = false;
?>

</head>
<body>

<div id="logo">
<img class="logo" src="<?php echo site_url("images/admin/logo.png"); ?>" alt="">
</div>
