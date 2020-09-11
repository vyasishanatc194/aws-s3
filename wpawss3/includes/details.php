<?php
function wpawss3_shortcodes() {
    echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">';
    ?>
    <div class="col-md-6">
        <h3>Shortcodes</h3><hr/>
        <table class="col-md-10">
            <tr valign="top" class="col-md-12">
                <th scope="row"><label >For datatable listing</label></th>
                <td>[wpawss3-listing]</td>
            </tr>
            <tr valign="top" class="col-md-12">
                <th scope="row"><label >For upload files and folders</label></th>
                <td>[wpawss3-service]</td>
            </tr>
        </table>
    </div>
    <?php
}