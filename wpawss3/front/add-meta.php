<?php
function wpawss3_add_meta_data() {
    if ( is_admin()){
        $userId = 1;
    }
    if( is_user_logged_in() ) {
	$userId = get_current_user_id();
	}
    $bucket = get_option('wpawss3_s3_bucket');


    $dbname = get_option('wpawss3_db_name');
	$servername = "localhost:3306";
	$username = 'wpDataTables';
	$password = 'd903kdas;l390-f$jki43 i-0233kd023;% IKO3($*#kjdl';

    $conn = new PDO("mysql:host=$servername;dbname=processing", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql_stmt = "SELECT DISTINCT idSwath FROM processing.prs_files_metadata_swath"; 
    $stmt = $conn->prepare($sql_stmt);
	$stmt->execute();
	$results = $stmt->fetchAll();
	$swathArray = [];
	foreach($results as $result) {
	    $swathArray[] =  $result['idSwath'];
	}
	

    ?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap.min.css">    
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://editor.datatables.net/extensions/Editor/css/editor.dataTables.min.css">
<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.1.24.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
<script src="https://editor.datatables.net/extensions/Editor/js/dataTables.editor.min.js"></script>

<div class="fuild-container">
	<div class="row">
		<div class="col-lg-9">
			<form name="add_file_meta_data_form" id="add_file_meta_data_form">			 
	            <div class="form-group">
	                <label for="folder"><?php _e('Folder', 'Folder')?></label>
	                <select id="folder" class="form-control" name="folder" required>
	                </select>
	            </div>
	            
	            <div class="form-group select_file">
	                <label for="file"><?php _e('File', 'File')?></label>
	                <select id="file" class="form-control" name="file">
	                </select>
	            </div>

	            <div class="form-group mode_radio">
	            	<label for="mode"><?php _e('Mode', 'Mode')?></label>
	               <div class="form-check">
					  <input class="form-check-input mode" type="radio" name="mode" value="@FILE_METADATA_MODE_NEG" id="mode1">
					  <label class="form-check-label" for="mode1">
					    <?php _e('Negative', 'Negative')?>
					  </label>
					</div>
					<div class="form-check">
					  <input class="form-check-input mode" type="radio"  name="mode" value="@FILE_METADATA_MODE_POS" id="mode2">
					  <label class="form-check-label" for="mode2">
					    <?php _e('Positive', 'Positive')?>
					  </label>
					</div>
	            </div>

	            <div class="form-group has_swath">
	            	<label for="par_isSwath"><?php _e('Has Swath', 'Has Swath')?></label>
	               <div class="form-check">
					  <input class="form-check-input par_isSwath" type="radio" name="par_isSwath" value="@FILE_METADATA_ISSWATH_UPLOADED" id="par_isSwath1">
					  <label class="form-check-label" for="par_isSwath1">
					    <?php _e('True', 'True')?>
					  </label>
					</div>
					<div class="form-check">
					  <input class="form-check-input par_isSwath" type="radio"  name="par_isSwath" value="@FILE_METADATA_ISSWATH_FALSE" id="par_isSwath2">
					  <label class="form-check-label" for="par_isSwath2">
					    <?php _e('False', 'False')?>
					  </label>
					</div>
	            </div>

	            <div class="form-group swath_created_radio">
	            	<label for="swath_created"><?php _e('Is Swath Created', 'Is Swath Created')?></label>
	               <div class="form-check">
					  <input class="form-check-input swath_created" type="radio" name="swath_created" value="1" id="swath_created1">
					  <label class="form-check-label" for="swath_created1">
					    <?php _e('Yes', 'Yes')?>
					  </label>
					</div>
					<div class="form-check">
					  <input class="form-check-input swath_created" type="radio"  name="swath_created" value="0" id="swath_created2">
					  <label class="form-check-label" for="swath_created2">
					    <?php _e('No', 'No')?>
					  </label>
					</div>
	            </div>

	            <div class="form-group select_swathid">
	                <label for="file"><?php _e('Swath ID', 'Swath ID')?></label>
	                <select id="par_idSwath" class="form-control" name="par_idSwath">
	                	<?php
	                	foreach ($swathArray as  $swath) { ?>
	                	<option value="<?php echo $swath; ?>"> <?php echo $swath; ?> </option>
	                <?php	} ?>
	                </select>
	            </div>

	            <div class="form-group swathtable">
	            	<p>
						<input type="button" id="addRow" value="Add New" />
					</p>
					<div id="cont"></div>

	            </div>
	            
	            <button type="submit" class="btn btn-primary" id="save_record">Save</button>
	        </form>
		</div>
		
	</div>
</div>
<script type="text/javascript">

	getCompletedFolderList();
	jQuery(".mode_radio").hide();
	jQuery(".select_file").hide();
	jQuery(".has_swath").hide();
	jQuery(".swath_created_radio").hide();
	jQuery(".select_swathid").hide();
	jQuery(".swathtable").hide();
	jQuery("#swathTable").remove();
	jQuery("#save_record").hide();

	
	jQuery( "#folder" ).change(function () {   
		
	  	jQuery(".select_file").hide();
	  	jQuery(".mode_radio").hide();
	  	jQuery(".has_swath").hide();
	  	jQuery("#save_record").hide();
	  	uncheckModeRadio();
	  	uncheckHasSwathRadio();
	  	
     if(jQuery(this).val()){
     		CompletedfileList();
			jQuery(".select_file").show();
		}
	});  

	 

	jQuery( "#file" ).change(function () {
			
		uncheckModeRadio();
		uncheckHasSwathRadio();
		jQuery(".mode_radio").hide();
		jQuery(".has_swath").hide();
		jQuery(".select_swathid").hide();
		jQuery(".swathtable").hide();
		jQuery("#swathTable").remove();
		jQuery("#save_record").hide(); 
		
		if(jQuery(this).val()){
			jQuery(".mode_radio").show();
		}
	}); 

	jQuery( ".mode" ).change(function () { 
		uncheckHasSwathRadio();
		jQuery(".select_swathid").hide();
		jQuery(".swathtable").hide();
		jQuery("#swathTable").remove();
		jQuery(".swath_created_radio").hide();
		jQuery("#save_record").hide();
		if(jQuery(this).val()){
			jQuery(".has_swath").show();
		}else{
			jQuery(".has_swath").hide();
			
		}
	});

	jQuery( ".par_isSwath" ).change(function () { 
		jQuery("#save_record").hide();
		uncheckSwathCreatedRadio();
		if(jQuery(this).val() == "@FILE_METADATA_ISSWATH_UPLOADED"){
			jQuery(".swath_created_radio").show();
		}else{
			jQuery(".swath_created_radio").hide();
			jQuery(".select_swathid").hide();
			jQuery(".swathtable").hide();
			jQuery("#swathTable").remove();
		}
	});

	jQuery( ".swath_created" ).change(function () { 
		jQuery("#save_record").hide();
		if(jQuery(this).val() == 1){
			jQuery(".select_swathid").show();
			jQuery(".swathtable").hide();
			jQuery("#save_record").show();
			jQuery("#swathTable").remove();
		}else{
			jQuery(".swathtable").show();
			createTable();
			addRow();
			jQuery("#save_record").show();
			jQuery(".select_swathid").hide();

		}
	});

	function uncheckModeRadio(){
		jQuery('input[name="mode"]').prop('checked', false);
	}
	function uncheckHasSwathRadio(){
		jQuery('input[name="par_isSwath"]').prop('checked', false);
	}
	function uncheckSwathCreatedRadio(){
		jQuery('input[name="swath_created"]').prop('checked', false);
	}

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


	$("#addRow").click(function(){
  		addRow();
	});

    var arrHead = new Array();	// array for header.
    arrHead = ['','Swath Number', 'Q1 start Mass (Da)', 'Q12 stop Mass (Da)', 'Collision Energy Spread (V)'];

    // first create TABLE structure with the headers. 
    function createTable() {
        var swathTable = document.createElement('table');
        swathTable.setAttribute('id', 'swathTable'); // table id.

        var tr = swathTable.insertRow(-1);
        for (var h = 0; h < arrHead.length; h++) {
            var th = document.createElement('th'); // create table headers
            th.innerHTML = arrHead[h];
            tr.appendChild(th);
        }

        var div = document.getElementById('cont');
        div.appendChild(swathTable);  // add the TABLE to the container.
    }

    // now, add a new to the TABLE.
    function addRow() {
    	
    	var swathTab = document.getElementById('swathTable');

        var rowCnt = swathTab.rows.length;   // table row count.
        var tr = swathTab.insertRow(rowCnt); // the table row.
        tr = swathTab.insertRow(rowCnt);

        for (var c = 0; c < arrHead.length; c++) {
            var td = document.createElement('td'); // table definition.
            td = tr.insertCell(c);

            if (c == 0 && rowCnt != 1) {      // the first column.
                // add a button in every new row in the first column.
                var button = document.createElement('input');

                // set input attributes.
                button.setAttribute('type', 'button');
                button.setAttribute('value', 'Remove');

                // add button's 'onclick' event.
                button.setAttribute('onclick', 'removeRow(this)');

                td.appendChild(button);
            }
            else if(c == 1){
            	var ele = document.createElement('input');
                ele.setAttribute('type', 'number');
                ele.setAttribute('name', 'par_expIndex[]');
                ele.setAttribute('class', 'par_expIndex');
                ele.setAttribute('value', rowCnt);
                ele.setAttribute('readonly', 'readonly');
                td.appendChild(ele);
            }
            else if(c == 2){
                // 2nd, 3rd and 4th column, will have textbox.
                var ele = document.createElement('input');
                ele.setAttribute('type', 'number');
                ele.setAttribute('name', 'par_startMass[]');
                ele.setAttribute('required', 'true');
                ele.setAttribute('value', '');

                td.appendChild(ele);
            }else if(c == 3){
                // 2nd, 3rd and 4th column, will have textbox.
                var ele = document.createElement('input');
                ele.setAttribute('type', 'number');
                ele.setAttribute('name', 'par_stopMass[]');
                ele.setAttribute('required', 'true');
                ele.setAttribute('value', '');

                td.appendChild(ele);
            }else if(c == 4){
                // 2nd, 3rd and 4th column, will have textbox.
                var ele = document.createElement('input');
                ele.setAttribute('type', 'number');
                ele.setAttribute('name', 'par_ces[]');
                ele.setAttribute('required', 'true');
                ele.setAttribute('value', '');

                td.appendChild(ele);
            }
        }

        updateSwathnumber();
    }

    // delete TABLE row function.
    function removeRow(oButton) {
    	var swathTab = document.getElementById('swathTable');
        swathTab.deleteRow(oButton.parentNode.parentNode.rowIndex); 
        updateSwathnumber();
    }

    function updateSwathnumber(){
    	var i = 1;
    	jQuery( ".par_expIndex" ).each( function( index, element ){
		    jQuery( this ).val(i);
		     i++;
		});
    }

    jQuery("#add_file_meta_data_form").submit(function(e) {
    e.preventDefault();

    var folderhas = jQuery("#folder").val();
    var filehas = jQuery("#file").val();
    var mode = jQuery(".mode:checked").val();
    var par_isSwath = jQuery(".par_isSwath:checked").val();
    var swath_created = jQuery(".swath_created:checked").val();
    var par_idSwath = jQuery("#par_idSwath").val();
    

	    if(swath_created == 1){

			  	$.ajax({
	            url: pw1_script_vars.ajaxurl,
	            dataType: "json",
	            type: "POST",
	            error: function(e){},
	            data: {
	                action: 'add_meta_for_existing_swath',
	                security: pw1_script_vars.security,
	                folderhas: folderhas,
	                filehas: filehas,
	                mode: mode,
	                par_isSwath: par_isSwath,
	                swath_created: swath_created,
	                par_idSwath: par_idSwath,

	            },
	            beforeSend: function() {},
	            success: function( result, xhr ) {
					if (result.data.success) {	
						toastr.success(result.data.data.message);
						$('#folder option:first').prop('selected',true);
						    uncheckModeRadio();
					    	uncheckHasSwathRadio();
					    	uncheckSwathCreatedRadio();
					    	jQuery(".mode_radio").hide();
							jQuery(".select_file").hide();
							jQuery(".has_swath").hide();
							jQuery(".swath_created_radio").hide();
							jQuery(".select_swathid").hide();
							jQuery(".swathtable").hide();
							jQuery("#save_record").hide();
					}
				},
	            complate: function() {}
	        });
	    
	    }if(swath_created == 0){

	    	var par_expIndex =  jQuery("input[name='par_expIndex[]']").map(function(){return $(this).val();}).get();
	    	var par_startMass =  jQuery("input[name='par_startMass[]']").map(function(){return $(this).val();}).get();
	    	var par_stopMass =  jQuery("input[name='par_stopMass[]']").map(function(){return $(this).val();}).get();
	    	var par_ces =  jQuery("input[name='par_ces[]']").map(function(){return $(this).val();}).get();

	    	$.ajax({
	            url: pw1_script_vars.ajaxurl,
	            dataType: "json",
	            type: "POST",
	            error: function(e){},
	            data: {
	                action: 'add_meta_for_new_swath',
	                security: pw1_script_vars.security,
	                folderhas: folderhas,
	                filehas: filehas,
	                mode: mode,
	                par_isSwath: par_isSwath,
	                swath_created: swath_created,
	                par_expIndex:par_expIndex,
	                par_startMass:par_startMass,
	                par_stopMass:par_stopMass,
	                par_ces:par_ces,

	            },
	            beforeSend: function() {},
	            success: function( result, xhr ) {
					if (result.data.success) {	
						toastr.success(result.data.data.message);
						$('#folder option:first').prop('selected',true);
						    uncheckModeRadio();
					    	uncheckHasSwathRadio();
					    	uncheckSwathCreatedRadio();
					    	jQuery(".mode_radio").hide();
							jQuery(".select_file").hide();
							jQuery(".has_swath").hide();
							jQuery(".swath_created_radio").hide();
							jQuery(".select_swathid").hide();
							jQuery(".swathtable").hide();
							jQuery("#save_record").hide();
					}
				},
	            complate: function() {}
	        });





	    }
    
	});

</script>
<?php
}
add_shortcode('wpawss3addmetadata', 'wpawss3_add_meta_data');
?>