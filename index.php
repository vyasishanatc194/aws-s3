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
        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">

        <!-- Custom styles -->
        <link href="css/jquery.dm-uploader.min.css" rel="stylesheet">
        <link href="css/styles.css" rel="stylesheet">
        <script src="https://sdk.amazonaws.com/js/aws-sdk-2.1.24.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <style>
            .hide-element { display: none; }
        </style>
    </head>
    <body>
        <div id="app"></div>

        <section class="form-folder-section">
            <div class="container">
                <div class="form-folder-div">
                    <div class="form-new-folder-root" id="_create_folder_section">
                        <div class="center-div">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <label class="error"></label>
                                    <div class="heading-div">
                                        <h2>Create new folder in S3</h2>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-12">
                                            <div class="form-group">
                                                <input type="text" placeholder="Create a new folder" name="folder_name" class="folder_name form-control" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-12">
                                            <div class="radio-group-div row mlr-5">
                                                <div class="radio-group-box public_section col-lg-6 plr-5">
                                                    <label for="public">
                                                        <input type="radio" class="root checkbox-input" name="root_folder" id="public" value="<?php echo 'public/'.$userId.'/'; ?>" />
                                                        <span class="text-span"> Public </span>
                                                    </label>
                                                </div>
                                                <div class="radio-group-box private_section col-lg-6 plr-5">
                                                    <label for="private">
                                                        <input type="radio" class="root checkbox-input" name="root_folder" id="private" value="<?php echo 'private/'.$userId.'/'; ?>" />
                                                        <span class="text-span"> Private </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                    
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <input type="hidden" value="<?php echo $bucket; ?>" id="bucketName" />
                                            <input type="hidden" id="radio_option" />
                                            <button type="button" name="create_folder" id="create_folder" class="btn btn-primary">Create a New Folder</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-new-folder-root" id="_folder">
                        <main role="main" class="main-container w-100">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="heading-div">
                                        <button type="button" class="btn btn-default" id="back_to_main">
                                            <i class="fa fa-arrow-left" aria-hidden="true"></i> Back To Main Page
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="heading-div">
                                        <h2>Upload file in S3 folder: <span id="dynamic_folder_name">My Folder</span></h2>
                                        <input type="hidden" value="" id="dynamic_hidden_folder_name" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-12 mb-20">
                                    <!-- Our markup, the important part here! -->
                                    <div id="drag-and-drop-zone" class="dm-uploader p-5">
                                        <h3 class="mb-5 mt-5 text-muted">Drag &amp; drop files here</h3>

                                        <div class="btn btn-primary btn-block mb-5">
                                            <span>Open the file Browser</span>
                                            <input type="file" id="fileUpload" title='Click to add Files' />
                                        </div>
                                    </div><!-- /uploader -->
                                </div>
                                <div class="col-md-6 col-sm-12 mb-20">
                                    <div class="card h-100">
                                        <div class="card-header">File List</div>
                                        <ul class="list-unstyled p-2 d-flex flex-column col" id="files">
                                            <li class="text-muted text-center empty">No files uploaded.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div><!-- /file list -->
                        </main> <!-- /container -->
                    </div>

                </div>
            </div>
        </section>
        
        <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="js/app.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>

        <script src="js/jquery.dm-uploader.min.js"></script>
        <script src="js/demo-ui.js"></script>
        <script src="js/demo-config.js"></script>

        <!-- File item template -->
        <script type="text/html" id="files-template">
            <li class="media">
                <div class="media-body mb-1">
                <p class="mb-2">
                    <strong>%%filename%%</strong> - Status: <span class="text-muted">Waiting</span>
                </p>
                <div class="progress mb-2">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                    role="progressbar"
                    style="width: 0%" 
                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <hr class="mt-1 mb-1" />
                </div>
            </li>
        </script>

        <script>

        $("#_folder").hide();            

        function s3upload(files) {
            // $("#upload").on("click", function(){
            var $destination = $("#dynamic_hidden_folder_name").val();
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
            // var files = document.getElementById('fileUpload').files;
            console.log('files', files);
            if (files) {
                var file = files.data;
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
                    saveRecordInDb(filePath, files, data);
                }).on('httpUploadProgress', function (progress) {                    
                    // var uploaded = parseInt((progress.loaded * 100) / progress.total);
                    // $("progress").attr('value', uploaded);
                });
            }
            // });
        }

        function saveRecordInDb(filePath, files, data) {
            var file = files.data;
            var fileName = file.name;
            var destinationDir = filePath;

            $.ajax({
                url: "Magic.php",
                data: {
                    destinationDir: destinationDir.trim(),
                    fileName: fileName,
                    bucket: '<?php echo $bucket; ?>'
                },
                success: function( result ) {
                    files.onSuccess(data);
                    // alert('Successfully Uploaded!');
                    // location.reload();
                }
            });
        }

        </script>
    </body>
</html>

