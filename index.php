<?php
require 'Magic.php';
// $folder_name = 'folder001/';
// $file_name = 'testFile1.txt';
$userId = 1;
$bucket = AWS_S3_BUCKET;
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="icon" type="image/gif" href="/loader.gif"/>
        <script src="https://sdk.amazonaws.com/js/aws-sdk-2.1.24.min.js"></script>
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <style>
            .hide-element { display: none; }
        </style>
    </head>
    <body>
        <div id="app"></div>
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
    </body>
</html>

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
                s3upload(new_folder_name.trim(), getAttrId);
            }
        });
    });
}

function s3upload(newfoldername, getAttrId) {
    $("#upload").on("click", function(){
        var $destination = $("#destination").val() || newfoldername;
        var albumBucketName = "<?php echo AWS_S3_BUCKET; ?>";
        var bucketRegion = "<?php echo AWS_S3_REGION; ?>";
        var IdentityPoolId = '<?php echo IdentityPoolId; ?>';

        AWS.config.update({
            region: bucketRegion,
            credentials: new AWS.CognitoIdentityCredentials({
                IdentityPoolId: IdentityPoolId
            })
        });

        var s3 = new AWS.S3({
            apiVersion: "2006-03-01",
            params: { Bucket: albumBucketName }
        });
        var files = document.getElementById('fileUpload').files;
        if (files) {
            var file = files[0];
            var fileName = file.name;
            var filePath = $destination + fileName;
            s3.putObject({
                Key: filePath,
                Body: file,
                ACL: 'public-read'
            }, 
            function(err, data) {
                if(err) {
                    console.log(err);
                    return true;
                }
                // console.log(data);
                saveRecordInDb(filePath, file.name);
            }).on('httpUploadProgress', function (progress) {
                var uploaded = parseInt((progress.loaded * 100) / progress.total);
                $("progress").attr('value', uploaded);
            });
        }
    });
}

function saveRecordInDb(filePath, file_name) {
    var destinationDir = filePath;
    var fileName = file_name;

    $.ajax({
        url: "Magic.php",
        data: {
            destinationDir: destinationDir.trim(),
            fileName: fileName,
            bucket: '<?php echo $bucket; ?>'
        },
        success: function( result ) {
            alert('Successfully Uploaded!');
            location.reload();
        }
    });
}

</script>

