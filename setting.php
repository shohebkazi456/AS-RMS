<?php

include('rms.php');

$object = new rms();

if(!$object->is_login())
{
    header("location:".$object->base_url."");
}

if(!$object->is_master_user())
{
    header("location:".$object->base_url."dashboard.php");
}
else
{
   $con=mysqli_connect('localhost:3306','root','','as_rms');

    $query=mysqli_query($con,"
    SELECT * FROM restaurant_table");

    $data = array();

       while($row = mysqli_fetch_array($query))
       {

            $data['restaurant_name'] = $row['restaurant_name'];
            $data['restaurant_email'] =  $row['restaurant_email'];
            $data['restaurant_contact_no'] =  $row['restaurant_contact_no'];
            $data['restaurant_address'] =  $row['restaurant_address'];
            $data['restaurant_currency'] =  $row['restaurant_currency'];
            $data['restaurant_timezone'] =  $row['restaurant_timezone'];
            $data['restaurant_tag_line'] =  $row['restaurant_tag_line'];

            $data['restaurant_logo'] = $row['restaurant_logo'];
            $pic=$data['restaurant_logo'];

        }

}
include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Setting</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <form method="post" id="setting_form" enctype="multipart/form-data">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="m-0 font-weight-bold text-primary">Setting</h6>
                                    </div>
                                    <div clas="col" align="right">
                                        <button type="submit" name="edit_button" id="edit_button" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                                        &nbsp;&nbsp;
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Restaurant Name</label>
                                            <input type="tex" value="<?php echo $data['restaurant_name']; ?>" name="restaurant_name" id="restaurant_name" class="form-control" />
                                        </div>
                                        <div class="form-group">
                                            <label>Restaurant Email</label>
                                            <input type="tex" value="<?php echo $data['restaurant_email']; ?>" name="restaurant_email" id="restaurant_email" class="form-control" />
                                        </div>
                                        <div class="form-group">
                                            <label>Restaurant Contact No.</label>
                                            <input type="tex" value="<?php echo $data['restaurant_contact_no']; ?>" name="restaurant_contact_no" id="restaurant_contact_no" class="form-control" />
                                        </div>
                                        <div class="form-group">
                                            <label>Restaurant Address</label>
                                            <input type="tex" value="<?php echo $data['restaurant_address']; ?>" name="restaurant_address" id="restaurant_address" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tag Line</label>
                                            <input type="tex" value="<?php echo $data['restaurant_tag_line']; ?>" name="restaurant_tag_line" id="restaurant_tag_line" class="form-control" />
                                        </div>
                                        <div class="form-group">
                                            <label>Currency</label>
                                            <?php 

                                            echo '<select name="restaurant_currency" id="restaurant_currency" class="form-control" required>
                                                <option value="">Select Currency</option>';

                                            $data1 = $object->currency_array();

                                                foreach($data1 as $row)
                                                {

                                                    if($row['code']==$data['restaurant_currency'])
                                                    {

                                                        echo '<option value="'.$row["code"].'" selected>'.$row["name"].'</option>';

                                                    }

                                                    echo '<option value="'.$row["code"].'">'.$row["name"].'</option>';
                                                    
                                                }

                                            echo '</select>';

                                            ?>
                                        </div>
                                        <div class="form-group">
                                            <label>Timezone</label>
                                            <?php 

                                            echo '<select name="restaurant_timezone" id="restaurant_timezone" class="form-control" required>
                                            <option value="">Select Timezone</option>';

                                            $timezones = $object->Timezone_list_array();

                                                foreach($timezones as $keys => $values)
                                                {

                                                    if($keys == $data['restaurant_timezone'])
                                                    {

                                                       echo '<option value="'.$keys.'" selected>'.$values.'</option>';
                                                    } 

                                                    echo '<option value="'.$keys.'">'.$values.'</option>';

                                                }

                            
                                            echo '</select>';

                                            ?>
                                        </div>
                                        <div class="form-group">
                                            <label>Select Logo</label><br />
                                            <input type="file" name="restaurant_logo" id="restaurant_logo" />
                                            <br />
                                            <span class="text-muted">Only .jpg, .png file allowed for upload</span><br />
                                            <span id="uploaded_logo">
                                                <?php echo "<img src='".$pic."' class='img-thumbnail' width='100' >" ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php
                include('footer.php');
                ?>

<?php

if(isset($_POST['edit_button']))

{

    if(isset($_POST["restaurant_name"]))
    {

        $restaurant_name = $_POST['restaurant_name'];
        $restaurant_tag_line = $_POST['restaurant_tag_line'];
        $restaurant_address = $_POST['restaurant_address'];
        $restaurant_contact_no = $_POST['restaurant_contact_no'];
        $restaurant_email = $_POST['restaurant_email'];
        $restaurant_currency = $_POST['restaurant_currency'];
        $restaurant_timezone = $_POST['restaurant_timezone'];


        if($_FILES["restaurant_logo"]["name"] != '')
        {
            $filename=$_FILES['restaurant_logo']['name'];
            $temname=$_FILES['restaurant_logo']['tmp_name'];
            $folder='images/'.$filename;
            move_uploaded_file($temname, $folder);
            $restaurant_logo = $folder;
        }
        else
        {
            $restaurant_logo = $pic;
        }

        mysqli_query($con,"
        UPDATE restaurant_table 
    SET restaurant_name = '$restaurant_name', 
    restaurant_tag_line = '$restaurant_tag_line', 
    restaurant_address = '$restaurant_address', 
    restaurant_contact_no = '$restaurant_contact_no', 
    restaurant_email = '$restaurant_email', 
    restaurant_currency = '$restaurant_currency', 
    restaurant_timezone = '$restaurant_timezone', 
    restaurant_logo = '$restaurant_logo'
        ");

        echo '
        <script>
            alert("Restaurant Details Updated Successfully");
            window.location.href="setting.php";</script>';

    }

}

?>