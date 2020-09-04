<?php
function wpawss3_process() {
    if ( is_admin()){
        $userId = 1;
    }
    if( is_user_logged_in() ) {
	$userId = get_current_user_id();
	}
    $bucket = get_option('wpawss3_s3_bucket');

    ?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap.min.css">    
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://editor.datatables.net/extensions/Editor/css/editor.dataTables.min.css">
<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<!-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> -->
<!-- <script src="https://sdk.amazonaws.com/js/aws-sdk-2.1.24.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.1.24.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
<script src="https://editor.datatables.net/extensions/Editor/js/dataTables.editor.min.js"></script>

<div class="fuild-container">
	<div class="row">
		<div class="col-lg-6">
			<form name="save_process_form" id="save_process_form">			 
	            <div class="form-group">
	                <label for="folder"><?php _e('Folder', 'Folder')?></label>
	                <select id="folder" class="form-control" name="folder" required>
	                </select>
	            </div>
	            <div class="form-group process_radio">
	               <div class="form-check">
					  <input class="form-check-input process" type="radio" name="process" value="process_file" id="process1">
					  <label class="form-check-label" for="process1">
					    <?php _e('Process File', 'Process File')?>
					  </label>
					</div>
					<div class="form-check">
					  <input class="form-check-input process" type="radio"  name="process" value="process_folder" id="process2">
					  <label class="form-check-label" for="process2">
					    <?php _e('Process Folder', 'Process Folder')?>
					  </label>
					</div>
	            </div>
	            <div class="form-group select_file">
	                <label for="file"><?php _e('File', 'File')?></label>
	                <select id="file" class="form-control" name="file">
	                </select>
	            </div>

	            <div class="form-group select_app_radio">
	               <div class="form-check">
					  <input class="form-check-input select_app" type="radio" name="select_app" value="2" id="select_app1">
					  <label class="form-check-label" for="select_app1">
					    <?php _e('SAFD', 'SAFD')?>
					  </label>
					</div>
					<div class="form-check">
					  <input class="form-check-input select_app" type="radio"  name="select_app" value="3" id="select_app2">
					  <label class="form-check-label" for="select_app2">
					    <?php _e('COMP', 'COMP')?>
					  </label>
					</div>
					<div class="form-check">
					  <input class="form-check-input select_app" type="radio"  name="select_app" value="4" id="select_app2">
					  <label class="form-check-label" for="select_app2">
					    <?php _e('FEDE', 'FEDE')?>
					  </label>
					</div>
	            </div>
	            <div class="form-group select_idAppPar">
	            	<label for="idAppPar"><?php _e('IdAppPar', 'IdAppPar')?></label>
	                <select id="idAppPar" class="form-control" name="idAppPar">
	                </select>
	            </div>
	            <div class="form-group comment_process">
	                <label for="comment"><?php _e('Comment', 'Comment')?></label>
	                <textarea name="comment" id="comment" maxlength="100"></textarea>
	              <!--   <input type="text" name="comment" id="comment" maxlength="100"> -->
	            </div>
	            <button type="submit" class="btn btn-primary" id="save_record">Save</button>
	        </form>
		</div>
		
	</div>
</div>
<script type="text/javascript">

	getCompletedFolderList();

	jQuery(".process_radio").hide();
	jQuery(".select_app_radio").hide();
	jQuery(".select_file").hide();
	jQuery("#save_record").hide();
	jQuery(".comment_process").hide();
	jQuery("#comment").val("");
	jQuery(".select_idAppPar").hide();

	jQuery( "#folder" ).change(function () {   
		jQuery('input[name="process"]').prop('checked', false);
	  	jQuery('input[name="select_app"]').prop('checked', false);
	  	jQuery(".select_file").hide();
	  	jQuery(".select_app_radio").hide();
	  	jQuery("#save_record").hide();
	  	jQuery(".comment_process").hide();
	  	jQuery("#comment").val("");
	  	jQuery(".select_idAppPar").hide();
     if(jQuery(this).val()){
	   jQuery(".process_radio").show();
	  }else{
	  	jQuery(".process_radio").hide();
	  	jQuery(".select_app_radio").hide();
	  	jQuery(".comment_process").hide();
	  	jQuery("#comment").val("");
	  	jQuery(".select_idAppPar").hide();
	  }
	});  

	jQuery( ".process" ).change(function () {   
		
		if(jQuery(this).val() == 'process_folder'){
			jQuery(".select_app_radio").show();
			jQuery(".select_file").hide();
		}else{
			CompletedfileList();
			jQuery(".select_app_radio").hide();
			jQuery('input[name="select_app"]').prop('checked', false);
			jQuery(".select_file").show();
			jQuery("#save_record").hide();
			jQuery(".comment_process").hide();
			jQuery("#comment").val("");
			jQuery(".select_idAppPar").hide();
		}
	}); 

	jQuery( "#file" ).change(function () { 
		jQuery('input[name="select_app"]').prop('checked', false);
		if(jQuery(this).val()){
			jQuery(".select_app_radio").show();
		}else{
			jQuery("#save_record").hide();
			jQuery(".select_app_radio").hide();
			jQuery(".comment_process").hide();
			jQuery("#comment").val("");
			jQuery(".select_idAppPar").hide();
		}
	}); 

	jQuery( ".select_app" ).change(function () { 
		if(jQuery(this).val()){
			getidAppPar();
			jQuery(".comment_process").show();
			jQuery(".select_idAppPar").show();
		 	jQuery("#save_record").show();
		}else{
			jQuery("#save_record").hide();
			jQuery(".select_idAppPar").hide();
		}
	});


	function getCompletedFolderList() {

		var folderPath = localStorage.getItem('FirstFolderName')+'<?php echo '/'.$userId.'/' ?>'+localStorage.getItem('folderName')+'/';
        jQuery("#dynamic_hidden_folder_name").val(folderPath);
        baseURL = folderPath;
        jQuery("#dynamic_folder_name").text(localStorage.getItem('folderName'));
        var destinationDir = folderPath;
        var html = '';
        $.ajax({
            url: pw1_script_vars.ajaxurl,
            dataType: "json",
            type: "POST",
            error: function(e){},
            data: {
                action: 'magic_funcs',
                security: pw1_script_vars.security,
                wpawss3_desti: destinationDir.trim(),
                wpawss3_getDBFolderList: true,
                wpawss3_bucket: '<?php echo $bucket; ?>'
            },
            beforeSend: function() {},
            success: function( result, xhr ) {
				if (result.data.success) {	
					html += "<option value=''> Please select folder</option>";
					$.each(result.data.data, function(i, item) {
						html += "<option value='"+item.idFolder+"'>"+item.folderName+"</option>";
						
					});
				}else{
					html = "<option value=''>"+result.data+"</option>"
				}
				jQuery("#folder").append(html);
				
			},
            complate: function() {}
        });
    }
    function CompletedfileList(){

    	var folderPath = localStorage.getItem('FirstFolderName')+'<?php echo '/'.$userId.'/' ?>'+localStorage.getItem('folderName')+'/';
        jQuery("#dynamic_hidden_folder_name").val(folderPath);
        baseURL = folderPath;
        jQuery("#dynamic_folder_name").text(localStorage.getItem('folderName'));
        var destinationDir = folderPath;
        var html = '';
        var folderhas = jQuery("#folder").val();
        $.ajax({
            url: pw1_script_vars.ajaxurl,
            dataType: "json",
            type: "POST",
            error: function(e){},
            data: {
                action: 'magic_funcs',
                security: pw1_script_vars.security,
                wpawss3_desti: destinationDir.trim(),
                wpawss3_getfileList: true,
                wpawss3_folderhas: folderhas,
                wpawss3_bucket: '<?php echo $bucket; ?>'
            },
            beforeSend: function() {},
            success: function( result, xhr ) {
				if (result.data.success) {	
					html += "<option value=''> Please select file</option>";
					$.each(result.data.data, function(i, item) {
						
						html += "<option value='"+item.idFile+"'>"+item.filename.split('/').pop()+"</option>";					
					});
				}else{
					html = "<option value=''>"+result.data+"</option>"
				}
				jQuery("#file").html(html);
				
			},
            complate: function() {}
        });
    }

    function getidAppPar(){

    	var par_idApp = jQuery(".select_app:checked").val();
    	var html = '';
    	$.ajax({
            url: pw1_script_vars.ajaxurl,
            dataType: "json",
            type: "POST",
            error: function(e){},
            data: {
                action: 'get_id_app_par',
                security: pw1_script_vars.security,
                par_idApp: par_idApp
            },
            beforeSend: function() {},
            success: function( result, xhr ) {
            	if (result.data.success) {	
            		
            		if(par_idApp == 2){
					
						$.each(result.data.idAppPar, function(i, item) {
							
							html += "<option value='"+item.idAppParSaf+"'>"+item.label+"</option>";					
						});
					}else if(par_idApp == 3){

						$.each(result.data.idAppPar, function(i, item) {
							
							html += "<option value='"+item.idAppParCmp+"'>"+item.label+"</option>";					
						});
					}else{
						$.each(result.data.idAppPar, function(i, item) {
							
							html += "<option value='"+item.idAppParFed+"'>"+item.label+"</option>";					
						});
					}
            	}
				
				jQuery("#idAppPar").html(html);
				jQuery(".select_idAppPar label").text(result.data.label);
					
			},
            complate: function() {}
        });
    }

    jQuery("#save_process_form").submit(function(e) {
    e.preventDefault();
   
    var folderhas = jQuery("#folder").val();
    var process_radio = jQuery(".process:checked").val();
    var filehas = jQuery("#file").val();
    var par_idApp = jQuery(".select_app:checked").val();
    var idAppPar = jQuery("#idAppPar").val();
    var comment = jQuery("#comment").val();

    	$.ajax({
            url: pw1_script_vars.ajaxurl,
            dataType: "json",
            type: "POST",
            error: function(e){},
            data: {
                action: 'store_process',
                security: pw1_script_vars.security,
                folderhas: folderhas,
                process_radio: process_radio,
                filehas: filehas,
                par_idApp: par_idApp,
                idAppPar:idAppPar,
                comment: comment,
            },
            beforeSend: function() {},
            success: function( result, xhr ) {
				if (result.data.success) {	
					toastr.success(result.data.data.message);
				}else{
					toastr.error(result.data.data.message);
				}
				$('#folder option:first').prop('selected',true);
				jQuery('input[name="process"]').prop('checked', false);
			  	jQuery('input[name="select_app"]').prop('checked', false);
			  	jQuery(".select_file").hide();
			  	jQuery(".process_radio").hide();
			  	jQuery(".select_app_radio").hide();
			  	jQuery("#save_record").hide();
			  	jQuery("#comment").val("");
			  	jQuery(".comment_process").hide();
			  	jQuery(".select_idAppPar").hide();
			},
            complate: function() {}
        });
    
    
	});



</script>
<?php
}
add_shortcode('wpawss3process', 'wpawss3_process');
?>