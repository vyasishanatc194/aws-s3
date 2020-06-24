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
          }
      });
  });
}