<?php

require 'Magic.php';

// $folder_name = 'folder001/';
// $file_name = 'testFile1.txt';
$userId = 1;
$bucket = AWS_S3_BUCKET;

?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="container">
    <div class="row">
        <div class="public_section">
            <label for="public">
                <input type="radio" class="root" name="root_folder" id="public" value="<?php echo 'public/'.$userId.'/'; ?>" />
                Public
            </label>
            <div id="public_folder" class="html_section">
                
            </div>
        </div>
        <div class="private_section">
            <label for="private">
                <input type="radio" class="root" name="root_folder" id="private" value="<?php echo 'private/'.$userId.'/'; ?>" />
                Private
            </label>
            <div id="private_folder" class="html_section"></div>
        </div>
    </div>
</div>
<style>
.hide-element {
    display: none;
}
</style>
<script>

$(".root").on("click", function() {
    $(".html_section").html('');
    var selectedRoot = $(this).val();
    var getAttrId = $(this).attr('id');
    if (getAttrId == 'private') {
        $(".public_section").hide();
    } else {
        $(".private_section").hide();
    }
    $.ajax({
        url: "Magic.php",
        data: {
            root: selectedRoot.trim(),
            bucket: '<?php echo $bucket; ?>'
        },
        success: function( result ) {
            $("#" + getAttrId + "_folder").html('');
            $("#" + getAttrId + "_folder").html(result);
            childFunction(selectedRoot, getAttrId);
        }
    });
});

function childFunction(selectedRoot, getAttrId) {
    $('#create_folder').click(function() {
        var new_folder_name = '';
        var newFolder = $(".folder_name").val();
        new_folder_name = selectedRoot.trim() + newFolder.trim() + '/';

        $.ajax({
            url: "Magic.php",
            data: {
                newfoldername: new_folder_name.trim(),
                bucket: '<?php echo $bucket; ?>'
            },
            success: function( result ) {
                $(".folder_name").val('');
                $("#" + getAttrId + "_folder").html('');
                $("#" + getAttrId + "_folder").html(result);
                childEventFunction(selectedRoot);
            }
        });
    });
}

function childEventFunction(selectedRoot) {
    
    // var selectedRoot = $(this).val();
    // var getAttrId = $(this).attr('id');
    // $.ajax({
    //     url: "Magic.php",
    //     data: {
    //         newfoldername: childFolder.trim(),
    //         bucket: '<?php echo $bucket; ?>'
    //     },
    //     success: function( result ) {
    //         $("#" + getAttrId + "_folder").html(result);
    //         childFunction(selectedRoot);
    //     }
    // });
}

$(document).ready(function() {
    // code
});
</script>

