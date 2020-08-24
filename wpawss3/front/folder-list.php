<?php
function wpawss3_listing() {
    if ( is_admin()){
        return;
    }
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
<script src="<?php echo plugins_url('wpawss3') . '/assets/js/jquery.dataTables.min.js'; ?>"></script>
<script src="<?php echo plugins_url('wpawss3') . '/assets/js/dataTables.buttons.min.js'; ?>"></script>
<script src="<?php echo plugins_url('wpawss3') . '/assets/js/dataTables.select.min.js'; ?>"></script>
<script src="<?php echo plugins_url('wpawss3') . '/assets/js/dataTables.editor.min.js'; ?>"></script>

<style>
#faq_table_wrapper .dropdown-toggle { color: #333 !important; border-color: #333; }
#exampleModal { z-index: 999999; }
#exampleModalLabel { line-height: 0px; }
.modal-header{ padding: 0rem 1rem; }
.hideBtn { display:none; }
</style>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create New Entry</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form name="save_faq_form" id="save_faq_form">
            <div class="form-group">
                <label for="folder_name"><?php _e('Folder Name', 'FAQ_module')?></label>
                <input id="folder_name" name="folder_name" type="text" class="form-control col-md-12" size="200" placeholder="<?php _e('Folder Name', 'FAQ_module')?>" required>
            </div>
            <div class="form-group">
                <label for="folder_access"><?php _e('Folder Access', 'FAQ_module')?></label>
                <select id="folder_access" class="form-control" name="folder_access" required>
                    <option value="1">Public</option>
                    <option value="2">Private</option>
                </select>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="close_model" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="save_record">Save</button>
      </div>
    </div>
  </div>
</div>
<div class="fuild-container">
    <div class="row">
        <div class="col-md-12 go_back">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#exampleModal" style="color: #333 !important;">
                            <u>New Entry</u>
                        </button>
                    </div>
                </div>
            </div>
            <table id="faq_table" class="table">
                <thead>
                <tr>
                    <th>folderName</th>
                    <th>status</th>
                    <th>idUser</th>
                    <th>isPublic</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready( function($) {
        $('#faq_table').on('click', 'a.uploadBtn', function (e) {
            e.preventDefault();
            var folderName = $(this)[0].dataset['foldername'];
            var FirstFolderName = ($(this)[0].dataset['ispublic'] == 1) ? 'public' : 'private';
            localStorage.setItem('folderName', folderName);
            localStorage.setItem('FirstFolderName', FirstFolderName);
            uploadData(folderName, FirstFolderName);
        } );

        var table = $('#faq_table').DataTable( {
			"processing": true,
            ajax: {
                url: pw1_script_vars.ajaxurl + '?action=records_list&security='+pw1_script_vars.security
            },
            columns: [
                { data: 'folderName',},
                { data: 'status' },
                { data: 'idUser' },
                { data: 'isPublic' },
				{
					 sortable: false,
					 className: "center",
					 "render": function ( data, type, full, meta ) {
						 var buttonID = full.idFolder;
						 var folderName = full.folderName;
                         var isPublic = full.isPublic;
                         var hideBtn = (full.process == 0) ? 'hideBtn' : '|';
                         var pipeSign = (full.process == 1) ? '|' : '';
						 return '<a href="javascript:void(0);" data-value="'+buttonID+'" class="processBtn '+hideBtn+'" role="button">Process</a> '+ pipeSign +' <a href="javascript:void(0);" data-folderName="'+folderName+'" data-isPublic="'+isPublic+'" class="uploadBtn">Upload</a>';
					 }
				},
            ],
            order: [[ 0, "desc" ]]
        });

        $("#save_record").on("click", function(){
			if ($("#folder_name").val() == '') {
				toastr.error('Please enter folder name');
				return false;
			}
            var postedData = {
                action: 'create_folder',
                security: pw1_script_vars.security,
                formData: $("#save_faq_form").serializeArray()
            };
            $.post(pw1_script_vars.ajaxurl, postedData, function(response) {
				table.ajax.reload();
                if (response.data.success) {
					document.getElementById('exampleModal').click();
					toastr.success(response.data.data.message);
					$("#save_faq_form")[0].reset();
				} else {
					$("#save_faq_form")[0].reset();
					toastr.error(response.data.data.message);
				}
				var x = document.getElementsByTagName("BODY")[0];
  				x.style.paddingRight = "0px";
            });
        });
		$("#exampleModal").on('hidden.bs.modal', function() { 
			var x = document.getElementsByTagName("BODY")[0];
			x.style.paddingRight = "0px";
		});

        function uploadData(folderName, FirstFolderName) {
            location.href = '<?php echo get_option('wpawss3_s3_page_link'); ?>';
        }
    });
</script>
<?php
}
add_shortcode('wpawss3listing', 'wpawss3_listing');
?>