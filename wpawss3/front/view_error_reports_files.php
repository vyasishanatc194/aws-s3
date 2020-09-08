<?php
function wpawss3_view_error_reports_files() {
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


<div class="fuild-container">
    <div class="row">
        <div class="col-md-12 go_back">
            
            <table id="faq_table" class="table">
                <thead>
                <tr>
                    <th>File Name</th>
                    <th>File Type</th>
                    <th>User</th>
                    <th>Brand</th>
                    <th>Status</th>
                    <th>Error Type</th>
                    <th>File Size</th>
                    <th>File Sibling</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
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
                url: pw1_script_vars.ajaxurl + '?action=view_error_files_list&security='+pw1_script_vars.security
            },
            columns: [
                { data: 'filename',},
                { data: 'filetypeLabel' },
                { data: 'label' },
                { data: 'brandLabel' },
                { data: 'statusLabel' },
                { data: 'ErrorTypeLabel' },
                { data: 'filesize' },
                { data: 'filesibling' },
                
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
        
        
        
        
    });
</script>
<?php
}
add_shortcode('wpawss3viewerrorreportsfiles', 'wpawss3_view_error_reports_files');
?>