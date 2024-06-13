<?php

//order_action.php

include('rms.php');

$object = new rms();

$con = $object->connect();

if(isset($_POST["action"]))
{

	if($_POST["action"] == 'reset')
	{
		$table_result=mysqli_query($con,"
		SELECT * FROM table_data 
		WHERE table_status = 'Enable' 
		ORDER BY table_id ASC
		");

		$html = '';

		foreach($table_result as $table)
		{
			$order_result=mysqli_query($con,"
			SELECT * FROM order_table 
			WHERE order_table = '".$table['table_name']."' 
			AND order_status = 'In Process'
			");
			
			$data=mysqli_num_rows($order_result);

			if($data > 0)
        	{

				foreach($order_result as $order)
				{
					$html .= '
					<button type="button" name="table_button" id="table_'.$table["table_id"].'" class="btn btn-warning mb-4 table_button" data-index="'.$table["table_id"].'" data-order_id="'.$order["order_id"].'" data-table_name="'.$table["table_name"].'">'.$table["table_name"].'<br />'.$table["table_capacity"].' Person</button>
					';
				}
			}
			else
			{
				$html .= '
				<button type="button" name="table_button" id="table_'.$table["table_id"].'" class="btn btn-secondary mb-4 table_button" data-index="'.$table["table_id"].'" data-order_id="0" data-table_name="'.$table["table_name"].'">'.$table["table_name"].'<br />'.$table["table_capacity"].' Person</button>
				';
			}
		}
		echo $html;
	}

	if($_POST["action"] == 'load_product')
	{
		$result=mysqli_query($con,"
		SELECT * FROM product_table 
		WHERE category_name = '".$_POST['category_name']."' 
		AND product_status = 'Enable'
		");
		$html = '<option value="">Select Product</option>';
		foreach($result as $row)
		{
			$html .= '<option value="'.$row["product_name"].'" data-price="'.$row["product_price"].'">'.$row["product_name"].'</option>';
		}
		echo $html;
	}

	if($_POST["action"] == 'Add')
	{

		if($_POST['hidden_order_id'] > 0)
        {
        
            $product_amount = $_POST['product_quantity'] * $_POST['hidden_product_rate'];

            session_start();

                $order_id         =  $_POST['hidden_order_id'];

                $_SESSION['order_id'] = $order_id;

                $product_name     =  $_POST['product_name'];
                $product_quantity =  $_POST['product_quantity'];
                $product_rate     =  $_POST['hidden_product_rate'];
                $product_amount   =  $product_amount;

            mysqli_query($con,"
            INSERT INTO order_item_table 
            (order_id, product_name, product_quantity, product_rate, product_amount) 
            VALUES ('$order_id', '$product_name', '$product_quantity', '$product_rate', '$product_amount')
            ");

        //     echo '
        // <script>
        //     alert("Item added to order");</script>
        //     <script>
        //     window.location.href="order.php";</script>';

            // echo $_POST['hidden_order_id'];

        }
        else
        {

                $order_number         =  $object->Generate_order_no();
                $order_table          =  $_POST['hidden_table_name'];
                $order_gross_amount   =  0;
                $order_tax_amount     =  0;
                $order_net_amount     =  0;

                $today_date=date("y-m-d");
                date_default_timezone_set('Asia/Calcutta');
                $time=date("H:i:sa");

                $order_date           =  $today_date;
                $order_time           =  $time;
                $order_waiter         =  $object->Get_user_name($_SESSION['user_id']);
                $order_cashier        =  '';
                $order_status         =  'In Process';

            mysqli_query($con,"
            INSERT INTO order_table 
            (order_number, order_table, order_gross_amount, order_tax_amount, order_net_amount, order_date, order_time, order_waiter, order_cashier, order_status) 
            VALUES ('$order_number', '$order_table', '$order_gross_amount', '$order_tax_amount', '$order_net_amount', '$order_date', '$order_time', '$order_waiter', '$order_cashier', '$order_status')
            ");

            $order_id = $con->insert_id;

            $_SESSION['order_id'] = $order_id;

            $product_amount = $_POST['product_quantity'] * $_POST['hidden_product_rate'];

                $order_id         =  $order_id;
                $product_name     =  $_POST['product_name'];
                $product_quantity =  $_POST['product_quantity'];
                $product_rate     =  $_POST['hidden_product_rate'];
                $product_amount   =  $product_amount;

            mysqli_query($con,"
            INSERT INTO order_item_table 
            (order_id, product_name, product_quantity, product_rate, product_amount) 
            VALUES ('$order_id', '$product_name', '$product_quantity', '$product_rate', '$product_amount')
            ");

        //     echo '
        // <script>
        //     alert("Order placed successfully");</script>
        //     <script>
        //     window.location.href="order.php";</script>';

            // echo $order_id;

        }

	}

	if($_POST["action"] == "fetch_order")
	{

		// // echo $_SESSION['order_id'];

		$query = mysqli_query($con,"
		SELECT * FROM order_item_table WHERE order_id = '".$_POST['order_id']."' ORDER BY order_item_id ASC");

		$html = '
		<table class="table table-striped table-bordered">
			<tr>
				<th>Item Name</th>
				<th>Quantity</th>
				<th>Rate</th>			
				<th>Amount</th>
				<th>Action</th>
			</tr>
		';

		if($query){
			
			foreach($query as $row)
           	{

           		$product_name = $row['product_name'];
				$order_item_id = $row['order_item_id'];
				$product_rate = $row['product_rate'];
				$order_id = $row['order_id'];
				$product_quantity = $row['product_quantity'];
				$product_amount_ = $row['product_amount'];

				// echo $product_name;

				$html .= '
				<tr>
					<td>'.$row["product_name"].'</td>
					<td><input type="number" class="form-control product_quantity" data-item_id="'.$row["order_item_id"].'" data-order_id="'.$row["order_id"].'" data-rate="'.$row["product_rate"].'" min="1" max="25" value="'.$row["product_quantity"].'" /></td>
					<td>'.$object->Get_currency_symbol().' '.$row["product_rate"].'</td>
					<td><span id="product_amount_'.$row["order_item_id"].'">'.$object->Get_currency_symbol().' '.$row["product_amount"].'</span></td>
					<td><button type="button" name="remove" class="btn btn-danger btn-sm remove_item" data-item_id="'.$row["order_item_id"].'" data-order_id="'.$row["order_id"].'"><i class="fas fa-minus-square"></i></button></td>
				</tr>
				';

            }

		}

		$html .= '
		</table>
		';
		echo $html;
	}


	if($_POST["action"] == "fetch_order2")
	{

		// // echo $_SESSION['order_id'];

		$query = mysqli_query($con,"
		SELECT * FROM order_item_table WHERE order_id = '".$_SESSION['order_id']."' ORDER BY order_item_id ASC");

		$html = '
		<table class="table table-striped table-bordered">
			<tr>
				<th>Item Name</th>
				<th>Quantity</th>
				<th>Rate</th>
				<th>Amount</th>
				<th>Action</th>
				
			</tr>
		';

		if($query){
			
			foreach($query as $row)
           	{

           		$product_name = $row['product_name'];
				$order_item_id = $row['order_item_id'];
				$product_rate = $row['product_rate'];
				$order_id = $row['order_id'];
				$product_quantity = $row['product_quantity'];
				$product_amount_ = $row['product_amount'];

				// echo $product_name;

				$html .= '
				<tr>
					<td>'.$row["product_name"].'</td>
					<td><input type="number" class="form-control product_quantity" data-item_id="'.$row["order_item_id"].'" data-order_id="'.$row["order_id"].'" data-rate="'.$row["product_rate"].'" min="1" max="25" value="'.$row["product_quantity"].'" /></td>
					<td>'.$object->cur . $row["product_rate"].'</td>
					<td><span id="product_amount_'.$row["order_item_id"].'">'.$object->cur . $row["product_amount"].'</span></td>
					<td><button type="button" name="remove" class="btn btn-danger btn-sm remove_item" data-item_id="'.$row["order_item_id"].'" data-order_id="'.$row["order_id"].'"><i class="fas fa-minus-square"></i></button></td>
				</tr>
				';

            }

		}

		$html .= '
		</table>
		';
		echo $html;
	}

	if($_POST['action'] == 'change_quantity')
	{
		mysqli_query($con,"
		UPDATE order_item_table 
		SET product_quantity = '".$_POST["quantity"]."', 
		product_amount = '".$_POST["quantity"] * $_POST["rate"]."' 
		WHERE order_id = '".$_POST["order_id"]."' 
		AND order_item_id = '".$_POST["item_id"]."'
		");
		// $object->execute();
	}

	if($_POST['action'] == 'remove_item')
	{
		mysqli_query($con,"
		DELETE FROM order_item_table 
		WHERE order_id = '".$_POST["order_id"]."' 
		AND order_item_id = '".$_POST["item_id"]."'
		");

		$res=mysqli_query($con,"
		SELECT order_item_id FROM order_item_table 
		WHERE order_id = '".$_POST["order_id"]."'
		");

		$data=mysqli_num_rows($res);

        if($data == 0)
        {
			mysqli_query($con,"
			DELETE FROM order_table 
			WHERE order_id = '".$_POST["order_id"]."'
			");

		}
	}

	if($_POST["action"] == 'dashboard_reset')
	{
		$table_result=mysqli_query($con,"
		SELECT * FROM table_data 
		WHERE table_status = 'Enable' 
		ORDER BY table_id ASC
		");

		$html = '<div class="row">';

		foreach($table_result as $table)
      	{

			$order_result=mysqli_query($con,"
			SELECT * FROM order_table 
			WHERE order_table = '".$table['table_name']."' 
			AND order_status = 'In Process'
			");
			
			$data=mysqli_num_rows($order_result);

	        if($data > 0)
	        {

				foreach($order_result as $order)
				{
					$html .= '
					<div class="col-lg-2 mb-3">
						<div class="card bg-info text-white shadow">
							<div class="card-body">
								'.$table["table_name"].'
								<div class="mt-1 text-white-50 small">Booked</div>
							</div>
						</div>
					</div>
					';
				}
			}
			else
			{
				$html .= '
				<div class="col-lg-2 mb-3">
					<div class="card bg-light text-black shadow">
						<div class="card-body">
							'.$table["table_name"].'
							<div class="mt-1 text-black-50 small">'.$table["table_capacity"].' Person</div>
						</div>
					</div>
				</div>
				';
			}
		}
		echo $html;
	}

}

?>