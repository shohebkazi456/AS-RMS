                <?php

                include('rms.php');

				$object = new rms();

				if(!$object->is_login())
				{
				    header("location:".$object->base_url."");
				}

                if(!$object->is_waiter_user() && !$object->is_master_user())
                {
                    header("location:".$object->base_url."dashboard.php");
                }

                include('header.php');

                ?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Order Area</h1>

                    <div class="row">
                        <div class="col col-sm-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">Table Status</div>
                                <div class="card-body" id="table_status">
                                <?php

                                $table_result=mysqli_query($con,"
                                SELECT * FROM table_data 
                                WHERE table_status = 'Enable' 
                                ORDER BY table_id ASC
                                ");

                                foreach($table_result as $table)
                                {
                                    echo '
                                    <button type="button" name="table_button" id="table_'.$table["table_id"].'" class="btn btn-secondary table_button" data-index="'.$table["table_id"].'">'.$table["table_name"].'<br />'.$table["table_capacity"].' Person</button>
                                    ';
                                }

                                ?>
                                </div>
                            </div>
                        </div>
                        <div class="col col-sm-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">Order Status</div>
                                <div class="card-body">
                                    <div class="table-responsive" id="order_status">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php
                include('footer.php');
                ?>

<div id="orderModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="order_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title">Add Item</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <span id="form_message"></span>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_name" id="category_name" class="form-control" required data-parsley-trigger="change">
                            <option value="">Select Category</option>
                            <?php

                            $category_result=mysqli_query($con,"
                            SELECT category_name FROM product_category_table 
                            WHERE category_status = 'Enable' 
                            ORDER BY category_name ASC
                            ");

                            foreach($category_result as $category)
                            {
                                echo '<option value="'.$category["category_name"].'">'.$category["category_name"].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Product Name</label>
                        <select name="product_name" id="product_name" class="form-control" required>
                            <option value="">Select Product</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <select name="product_quantity" id="product_quantity" class="form-control" required>
                            <?php
                            for($i = 1; $i < 25; $i++)
                            {
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="hidden_table_id" id="hidden_table_id" />
                    <input type="hidden" name="hidden_order_id" id="hidden_order_id" />
                    <input type="hidden" name="hidden_product_rate" id="hidden_product_rate" />
                    <input type="hidden" name="hidden_table_name" id="hidden_table_name" />
                    <input type="hidden" name="action" id="action" value="Add" />
                    <input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>

$(document).ready(function(){

    reset_table_status();

    setInterval(function(){
        reset_table_status();
    }, 1000);

    function reset_table_status()
    {
        $.ajax({
            url:"order_action.php",
            method:"POST",
            data:{action:'reset'},
            success:function(data){
                $('#table_status').html(data);
            }
        });
    }

    function fetch_order_data(order_id)
    {
        $.ajax({
            url:"order_action.php",
            method:"POST",
            data:{order_id:order_id, action:'fetch_order' },
            success:function(data)
            {
                $('#order_status').html(data);
            }
        });
    }

    function fetch_order_data2(order_id)
    {
        $.ajax({
            url:"order_action.php",
            method:"POST",
            data:{order_id:order_id, action:'fetch_order2' },
            success:function(data)
            {
                $('#order_status').html(data);
            }
        });
    }
    

    $(document).on('change', '#category_name', function(){
        var category_name = $('#category_name').val();
        if(category_name != '')
        {
            $.ajax({
                url:"order_action.php",
                method:"POST",
                data:{action:'load_product', category_name:category_name},
                success:function(data)
                {
                    $('#product_name').html(data);
                }
            });
        }
    });

    $(document).on('change', '#product_name', function(){
        var rate = $('#product_name').find(':selected').data('price');
        $('#hidden_product_rate').val(rate);
    });

    var button_id = $(this).attr('id');

    $(document).on('click', '.table_button', function(){        
        var table_id = $(this).data('index');
        $('#hidden_table_id').val(table_id);
        $('#hidden_table_name').val($(this).data('table_name'));
        $('#orderModal').modal('show');
        $('#order_form')[0].reset();
        $('#order_form').parsley().reset();
        $('#submit_button').attr('disabled', false);
        $('#submit_button').val('Add');
        var order_id = $(this).data('order_id');
        $('#hidden_order_id').val(order_id);
        fetch_order_data(order_id);
    });

    $('#category_form').parsley();

    $('#product_form').parsley();

    $('#order_form').on('submit', function(event){
        event.preventDefault();
        if($('#order_form').parsley().isValid())
        {
            $.ajax({
                url:"order_action.php",
                method:"POST",
                data:$(this).serialize(),
                beforeSend:function(){
                    $('#submit_button').attr('disabled', 'disabled');
                    $('#submit_button').val('Wait...');
                },
                success:function(data)
                {
                    $('#submit_button').attr('disabled', false);
                    $('#submit_button').val('Add');
                    $('#'+button_id).addClass('btn-primary');
                    $('#'+button_id).removeClass('btn-secondary');
                    $('#order_form')[0].reset();
                    $('#orderModal').modal('hide');
                    fetch_order_data2(data);
                }
            }); 
        }
    });

    $(document).on('change', '.product_quantity', function(){
        var quantity = $(this).val();
        var item_id = $(this).data('item_id');
        var order_id = $(this).data('order_id');
        var rate = $(this).data('rate');
        $.ajax({
            url:"order_action.php",
            method:"POST",
            data:{order_id:order_id, item_id:item_id, quantity:quantity, rate:rate, action:'change_quantity'},
            success:function(data)
            {
                fetch_order_data(order_id);
            }
        });
    });

    $(document).on('click', '.remove_item', function(){
        if(confirm("Are you sure you want to remove it?"))
        {
            var item_id = $(this).data('item_id');
            var order_id = $(this).data('order_id');
            $.ajax({
                url:"order_action.php",
                method:"POST",
                data:{order_id:order_id, item_id:item_id, action:'remove_item'},
                success:function(data)
                {
                    fetch_order_data(order_id);
                }
            });
        }
    });

});

</script>

<?php

// if(isset($_POST["action"]))
// {

//     if($_POST["action"] == 'Add')
//     {

//         if($_POST['hidden_order_id'] > 0)
//         {
        
//             $product_amount = $_POST['product_quantity'] * $_POST['hidden_product_rate'];

//                 $order_id         =  $_POST['hidden_order_id'];

//                 session_start();

//                 $_SESSION["order_id"] = $order_id;

//                 $product_name     =  $_POST['product_name'];
//                 $product_quantity =  $_POST['product_quantity'];
//                 $product_rate     =  $_POST['hidden_product_rate'];
//                 $product_amount   =  $product_amount;

//             mysqli_query($con,"
//             INSERT INTO order_item_table 
//             (order_id, product_name, product_quantity, product_rate, product_amount) 
//             VALUES ('$order_id', '$product_name', '$product_quantity', '$product_rate', '$product_amount')
//             ");

//             echo '
//         <script>
//             alert("Item added to order");</script>
//             <script>
//             window.location.href="order.php";</script>';

//         }
//         else
//         {

//                 $order_number         =  $object->Generate_order_no();
//                 $order_table          =  $_POST['hidden_table_name'];
//                 $order_gross_amount   =  0;
//                 $order_tax_amount     =  0;
//                 $order_net_amount     =  0;

//                 $today_date=date("y-m-d");
//                 date_default_timezone_set('Asia/Calcutta');
//                 $time=date("H:i:sa");

//                 $order_date           =  $today_date;
//                 $order_time           =  $time;
//                 $order_waiter         =  $object->Get_user_name($_SESSION['user_id']);
//                 $order_cashier        =  '';
//                 $order_status         =  'In Process';

//             mysqli_query($con,"
//             INSERT INTO order_table 
//             (order_number, order_table, order_gross_amount, order_tax_amount, order_net_amount, order_date, order_time, order_waiter, order_cashier, order_status) 
//             VALUES ('$order_number', '$order_table', '$order_gross_amount', '$order_tax_amount', '$order_net_amount', '$order_date', '$order_time', '$order_waiter', '$order_cashier', '$order_status')
//             ");

//             $order_id = $con->insert_id;

//             $product_amount = $_POST['product_quantity'] * $_POST['hidden_product_rate'];

//                 $order_id         =  $order_id;

//                 $_SESSION["order_id"] = $order_id;

//                 $product_name     =  $_POST['product_name'];
//                 $product_quantity =  $_POST['product_quantity'];
//                 $product_rate     =  $_POST['hidden_product_rate'];
//                 $product_amount   =  $product_amount;

//             mysqli_query($con,"
//             INSERT INTO order_item_table 
//             (order_id, product_name, product_quantity, product_rate, product_amount) 
//             VALUES ('$order_id', '$product_name', '$product_quantity', '$product_rate', '$product_amount')
//             ");

//             echo '
//         <script>
//             alert("Order placed successfully");</script>
//             <script>
//             window.location.href="order.php";</script>';

//         }

//     } //end of adding order

// }





// echo $_SESSION["order_id"];

// $query = mysqli_query($con,"
//         SELECT * FROM order_item_table WHERE order_id = '".$_SESSION["order_id"]."' ORDER BY order_item_id ASC");

//         $html = '
//         <table class="table table-striped table-bordered">
//          <tr>
//              <th>Item Name</th>
//              <th>Quantity</th>
//              <th>Rate</th>
//              <th>Amount</th>
//              <th>Action</th>
//          </tr>
//         ';

//         if($query){
            
//          foreach($query as $row)
//              {

//                  $product_name = $row['product_name'];
//              $order_item_id = $row['order_item_id'];
//              $product_rate = $row['product_rate'];
//              $order_id = $row['order_id'];
//              $product_quantity = $row['product_quantity'];
//              $product_amount_ = $row['product_amount'];

//              $html .= '
//              <tr>
//                  <td>'.$row["product_name"].'</td>
//                  <td><input type="number" class="form-control product_quantity" data-item_id="'.$row["order_item_id"].'" data-order_id="'.$row["order_id"].'" data-rate="'.$row["product_rate"].'" min="1" max="25" value="'.$row["product_quantity"].'" /></td>
//                  <td>'.$object->cur . $row["product_rate"].'</td>
//                  <td><span id="product_amount_'.$row["order_item_id"].'">'.$object->cur . $row["product_amount"].'</span></td>
//                  <td><button type="button" name="remove" class="btn btn-danger btn-sm remove_item" data-item_id="'.$row["order_item_id"].'" data-order_id="'.$row["order_id"].'"><i class="fas fa-minus-square"></i></button></td>
//              </tr>
//              ';

//             }

//         }

//         $html .= '
//         </table>
//         ';
//         echo $html;

?>