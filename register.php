
<?php

include('rms.php');

$object = new rms();

if($object->Is_set_up_done())
{
    if($object->is_login())
    {
        header("location:".$object->base_url."dashboard.php");
    }
    else
    {
        header("location:".$object->base_url."index.php");
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Restaurant Management System using PHP - Register</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="vendor/parsley/parsley.css"/>

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-9 col-lg-9 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <form method="post" id="register_form">
                            <div class="p-5">
                                <span id="message"></span>
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Set up Account</h1>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Restaurant Name</label>
                                            <input type="text" name="restaurant_name" id="restaurant_name" class="form-control" required data-parsley-maxlength="175" data-parsley-trigger="keyup" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Restaurant Address</label>
                                            <input type="text" name="restaurant_address" id="restaurant_address" class="form-control" required data-parsley-maxlength="250" data-parsley-trigger="keyup" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Restaurant Contact No.</label>
                                            <input type="text" name="restaurant_contact_no" id="restaurant_contact_no" class="form-control" required data-parsley-type="integer" data-parsley-maxlength="12" data-parsley-trigger="keyup" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Restaurant Tag Line</label>
                                            <input type="text" name="restaurant_tag_line" id="restaurant_tag_line" class="form-control" required data-parsley-maxlength="200" data-parsley-trigger="keyup" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Currency</label>
                                            <?php 
                                            echo $object->Currency_list();
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Timezone</label>
                                            <?php 
                                            echo $object->Timezone_list();
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email Address</label>
                                            <input type="text" name="user_email" id="user_email" class="form-control" required data-parsley-type="email" data-parsley-trigger="keyup" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="password" name="user_password" id="user_password" class="form-control" required data-parsley-trigger="keyup" />
                                        </div>
                                    </div>

                                    <div class="col-md-12" align="center">
                                        <div class="form-group">
                                            <br />
                                            <button type="submit" name="register_button" id="register_button" class="btn btn-primary btn-user">Set Up</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <script type="text/javascript" src="vendor/parsley/dist/parsley.min.js"></script>

</body>

</html>

<script>

$(document).ready(function(){

    $('#register_form').parsley();

});

</script>

<?php

    class Registration extends rms
    {

        public function insertRecordRegister($restaurant_name, $restaurant_tag_line, $restaurant_address, $restaurant_contact_no, $user_email, $restaurant_currency, $restaurant_timezone, $restaurant_logo)
        {
            $con=$this->connect();

            mysqli_query($con,"insert into restaurant_table values ('','$restaurant_name', '$restaurant_tag_line', '$restaurant_address', '$restaurant_contact_no', '$user_email', '$restaurant_currency', '$restaurant_timezone', '$restaurant_logo')");
        }

        public function insertRecordUserRegister($user_name, $user_contact_no, $user_email, $user_password, $user_profile, $user_type, $user_status, $user_created_on)
        {
            $con=$this->connect();

            mysqli_query($con,"insert into user_table values ('','$user_name', '$user_contact_no', '$user_email', '$user_password', '$user_profile', '$user_type', '$user_status', '$user_created_on')");
        }
 
    } // end of Register class

    $ob = new Registration();

    extract($_POST);

    if(isset($_POST["user_email"]))
    {

        $con=mysqli_connect('localhost:3306','root','','as_rms');

        $res=mysqli_query($con,"SELECT * FROM user_table WHERE user_email = '$user_email'");
        $data=mysqli_num_rows($res);

        if($data > 0)
        {
            echo '

        <script>
            alert("Email Already Exists");</script>';
        }

    }


    if(isset($register_button))
    {

        $restaurant_logo = '';

        $user_name= '';
        $user_contact_no= '';
        $user_email= $_POST["user_email"];
        $user_password= $_POST["user_password"];
        $user_type= 'Master';
        $user_status= 'Enable';

        $today_date=date("y-m-d");
        date_default_timezone_set('Asia/Calcutta');
        $time=date("H:i:sa");

        $user_created_on= $today_date." ".$time;

        // $user_profile = $object->make_avatar(strtoupper($_POST["user_email"][0]));
        // $user_profile = $user_profile;

        $user_profile= '';

        $ob->insertRecordRegister($restaurant_name, $restaurant_tag_line, $restaurant_address, $restaurant_contact_no, $user_email, $restaurant_currency, $restaurant_timezone, $restaurant_logo);

        $ob->insertRecordUserRegister($user_name, $user_contact_no, $user_email, $user_password, $user_profile, $user_type, $user_status, $user_created_on);
    
        echo '<br><center>Your Account Created, Now you can Login</center>

        <script>
            window.location.href="index.php";</script>';
        
    }

?>