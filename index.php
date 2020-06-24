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
        <link href="/css/jquery.dm-uploader.min.css" rel="stylesheet">
        <link href="/css/styles.css" rel="stylesheet">
        <script src="https://sdk.amazonaws.com/js/aws-sdk-2.1.24.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <style>
            .hide-element { display: none; }
        </style>
    </head>
    <body>
        <div id="app"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <h2>Create New Folder</h2>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <input type="text" placeholder="Create a new folder" name="folder_name" class="folder_name form-control" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="public_section col-md-3">
                            <label for="public">
                                <input type="radio" class="root" name="root_folder" id="public" value="<?php echo 'public/'.$userId.'/'; ?>" />
                                Public
                            </label>
                            <div id="public_folder" class="html_section"></div>
                        </div>
                        <div class="private_section col-md-3">
                            <label for="private">
                                <input type="radio" class="root" name="root_folder" id="private" value="<?php echo 'private/'.$userId.'/'; ?>" />
                                Private
                            </label>
                            <div id="private_folder" class="html_section"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <button type="button" name="create_folder" id="create_folder" class="btn btn-primary">Create a New Folder</button>
                        </div>
                    </div>
                </div>
            </div>
            <main role="main" class="container">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <!-- Our markup, the important part here! -->
                        <div id="drag-and-drop-zone" class="dm-uploader p-5">
                            <h3 class="mb-5 mt-5 text-muted">Drag &amp; drop files here</h3>

                            <div class="btn btn-primary btn-block mb-5">
                                <span>Open the file Browser</span>
                                <input type="file" id="fileUpload" title='Click to add Files' />
                            </div>
                        </div><!-- /uploader -->
                    </div>
                    <div class="col-md-6 col-sm-12">
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
        
        <script type="text/javascript" src="/js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="/js/app.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>

        <script src="/js/jquery.dm-uploader.min.js"></script>
        <script src="/js/demo-ui.js"></script>
        <script src="/js/demo-config.js"></script>

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

        function s3upload(files) {
            // $("#upload").on("click", function(){
            var $destination = $("#destination").val() || 'private/1/x/';
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

