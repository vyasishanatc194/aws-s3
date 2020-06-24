$(".root").on("click", function() {
  $("#radio_option").val($(this).val());
  var getAttrId = $(this).attr('id');
  if (getAttrId == 'private') {
      $("#public").parent().removeClass('active');
      $("#private").parent().addClass('active');
  } else {
    $("#private").parent().removeClass('active');
    $("#public").parent().addClass('active');
  }
});

$("#back_to_main").on("click", function(){
  $("#_create_folder_section").show();
  $("#_folder").hide();
  $(".error").text('').hide();
  $("label").removeClass('active');
});

$(".error").hide();

// function childFunction(selectedRoot, getAttrId) {
  $('#create_folder').click(function() {
      var selectedRoot = $("#radio_option").val();
      var newFolder = $(".folder_name").val();
      var new_folder_name = selectedRoot.trim() + newFolder.trim() + '/';

      if (newFolder.length == 0) { $("label.error").show().text("Please enter folder name."); return false; }
      else if (selectedRoot.length == 0) { $("label.error").show().text("Please select one root folder name."); return false; }
      else {
        $.ajax({
            url: "Magic.php",
            data: {
                newfoldername: new_folder_name.trim(),
                bucket: $("#bucketName").val()
            },
            success: function( result ) {
                $(".folder_name").val('');
                $("#dynamic_folder_name").text(JSON.parse(result).folderName);
                $("#dynamic_hidden_folder_name").val(JSON.parse(result).folderPath);
                $("#_create_folder_section").hide();
                $("#_folder").show();
            }
        });
      }
  });
// }