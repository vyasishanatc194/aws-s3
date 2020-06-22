<?php

require 'Magic.php';

// $folder_name = 'folder001/';
// $file_name = 'testFile1.txt';
$bucket = AWS_S3_BUCKET;

$res = Magic::getAllFolderCB($bucket);
$arrResponse = [];
$resposne = Magic::makeAnArr($res);
?>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<div class="form_block">
    <!-- <input type="radio" class="primary_folder" name="primary_folder" id="private" value="private"/>  -->
    <label for="private">private</label>
    <div class="private_child_block">
    <ul>
    <?php
            foreach($resposne['private'] as $key=>$val) {
                if (!is_array($val)) {
                    $value = explode("/", $val);
            ?>            
                <li>
                    <input type="radio" class="folder_dir" name="folder_dir" id="<?php echo $val; ?>" value="<?php echo trim($val); ?>"/> 
                    <label for="<?php echo $val; ?>"><?php echo $value[count($value)-2]; ?></label>
                <?php } if (!is_numeric($key) && isset($resposne['private'][$key])) { ?>
                        <ul>
                            <?php foreach($resposne['private'][$key] as $k=>$v) { 
                                $childValue = explode("/", $v);
                            ?>
                                <li>
                                    <input type="radio" class="folder_dir" name="folder_dir" id="<?php echo $v; ?>" value="<?php echo trim($v); ?>"/> 
                                    <label for="<?php echo $v; ?>"><?php echo $childValue[count($childValue)-3]; ?></label>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </li>            
            <?php
            }
        ?>
    </ul>
    </div>
    <div class="public_child_block">
    <!-- <input type="radio" class="primary_folder" name="primary_folder" id="public" value="public"/>  -->
    <label for="public">public</label>
    <ul>
        <?php
            foreach($resposne['public'] as $key=>$val) {
                if (!is_array($val)) {
                    $value = explode("/", $val);
            ?>            
                <li>
                    <input type="radio" class="folder_dir" name="folder_dir" id="<?php echo $val; ?>" value="<?php echo trim($val); ?>"/> 
                    <label for="<?php echo $val; ?>"><?php echo $value[count($value)-2]; ?></label>
                <?php } if (!is_numeric($key) && isset($resposne['public'][$key])) { ?>
                        <ul>
                            <?php foreach($resposne['public'][$key] as $k=>$v) { 
                                $childValue = explode("/", $v);
                            ?>
                                <li>
                                    <input type="radio" class="folder_dir" name="folder_dir" id="<?php echo $v; ?>" value="<?php echo trim($v); ?>"/> 
                                    <label for="<?php echo $v; ?>"><?php echo $childValue[count($childValue)-3]; ?></label>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </li>            
            <?php
            }
        ?>
    </ul>
    </div>
</div>
<br/>
<input type="text" name="folder_name" class="folder_name hide-element" placeholder="Enter Folder Name" />
<button type="button" name="create_folder" id="create_folder">Create a New Folder</button>
<style>
.hide-element {
    display: none;
}
</style>
<script>
 $(document).ready(function() {
    var btn = $('#create_folder');
    btn.click(function() {

        var new_folder_name = '';
        var selectedDir = $(".folder_dir").val();
        var newFolder = $(".folder_name").val();
        new_folder_name = selectedDir + newFolder + '/';

        if ($(".folder_name").hasClass('hide-element')) {
            $(".folder_name").removeClass('hide-element');
        } else {
            $.ajax({
                url: "Magic.php",
                data: {
                    newfoldername: new_folder_name,
                    bucket: '<?php echo $bucket; ?>'
                },
                success: function( result ) {
                    console.log(result);
                    location.reload();
                    $(".folder_name").val('');
                }
            });
        }
    });
 });
</script>

