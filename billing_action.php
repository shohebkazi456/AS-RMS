<?php

//billing_action.php

include('rms.php');

$object = new rms();

$con = $object->connect();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('order_table', 'order_number', 'order_date', 'order_time', 'order_waiter', 'order_status');

		$output = array();

		$main_query = "
		SELECT * FROM order_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE order_table LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR order_number LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR order_date LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR order_time LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR order_waiter LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR order_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY order_id DESC ';
		}

		$limit_query = '';

		if($_POST["length"] != -1)
		{
			$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$combinequery = mysqli_query($con,$main_query . $search_query . $order_query);

		$filtered_rows = mysqli_num_rows($combinequery);

		$combinequery = mysqli_query($con,$main_query . $search_query . $order_query . $limit_query);

		$main_query2 = mysqli_query($con,"
    	SELECT * FROM order_table ");

    	$total_rows = mysqli_num_rows($main_query2);

        $data = array();

		while($result = mysqli_fetch_array($combinequery))
		{
			$sub_array = array();
			$sub_array[] = $result["order_table"];
			$sub_array[] = $result["order_number"];
			$sub_array[] = $result["order_date"];
			$sub_array[] = $result["order_time"];
			$sub_array[] = $result["order_waiter"];
			if($object->is_master_user())
			{
				$sub_array[] = $result["order_cashier"];
			}
			$status = '';
			$print = '';
			if($result["order_status"] == 'In Process')
			{
				$status = '<button type="button" name="status_button" class="btn btn-warning btn-sm">In Process</button>';
				$print = '';
			}
			else
			{
				$status = '<button type="button" name="status_button" class="btn btn-success btn-sm">Completed</button>';
				$print = '<a href="print.php?action=print&order_id='.$result["order_id"].'" class="btn btn-warning btn-sm btn-circle"><i class="fas fa-file-pdf"></i></a>&nbsp;';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-primary btn-circle btn-sm view_button" data-id="'.$result["order_id"].'"><i class="fas fa-eye"></i></button>
			&nbsp;
			'.$print.'
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$result["order_id"].'"><i class="fas fa-times"></i></button>
			</div>
			';
			$data[] = $sub_array;
		}

		$output = array(
			"draw"    			=> 	intval($_POST["draw"]),
			"recordsTotal"  	=>  $total_rows,
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
			
		echo json_encode($output);
	}

	if($_POST["action"] == 'fetch_single')
	{

		$query=mysqli_query($con,"
		SELECT * FROM order_item_table 
		WHERE order_id = '".$_POST['order_id']."' 
		ORDER BY order_item_id ASC
		");

		$html = '
		<table class="table table-striped table-bordered">
			<tr>
				<th>Sr#</th>
				<th>Item Name</th>
				<th>Quantity</th>
				<th>Rate</th>
				<th>Amount</th>
				<th>Action</th>
			</tr>
		';
		$count = 1;
		$gross_total = 0;
		$total_tax_amt = 0;
		$net_total = 0;
		foreach($query as $row)
		{
			$html .= '
			<tr>
				<td>'.$count.'</td>
				<td>'.$row["product_name"].'</td>
				<td><input type="number" class="form-control product_quantity" data-item_id="'.$row["order_item_id"].'" data-order_id="'.$row["order_id"].'" data-rate="'.$row["product_rate"].'" min="1" max="25" value="'.$row["product_quantity"].'" /></td>
				<td>'.$object->Get_currency_symbol().' ' . $row["product_rate"].'</td>
				<td><span id="product_amount_'.$row["order_item_id"].'">'.$object->Get_currency_symbol().' ' . $row["product_amount"].'</span></td>
				<td><button type="button" name="remove" class="btn btn-danger btn-sm remove_item" data-item_id="'.$row["order_item_id"].'" data-order_id="'.$row["order_id"].'"><i class="fas fa-minus-square"></i></button></td>
			</tr>
			';
			$count++;
			$gross_total += $row["product_amount"];
		}

		$html .= '
			<tr>
				<td colspan="4" class="text-right"><b>Total</b></td>
				<td colspan="2">'.$object->Get_currency_symbol().' ' . number_format((float)$gross_total, 2, '.', '').'</td>
			</tr>
		';

		$tax_result = mysqli_query($con,"
		SELECT * FROM tax_table 
		WHERE tax_status = 'Enable' 
		ORDER BY tax_id ASC
		");

		mysqli_query($con,"
		DELETE FROM order_tax_table 
		WHERE order_id = '".$_POST['order_id']."'
		");

		foreach($tax_result as $tax)
		{
			$tax_amt = ($gross_total * $tax["tax_percentage"])/100;
			$html .= '
			<tr>
				<td colspan="4" class="text-right"><b>'.$tax["tax_name"].' ('.$tax["tax_percentage"].'%)</b></td>
				<td colspan="2">'.$object->Get_currency_symbol().' ' . number_format((float)$tax_amt, 2, '.', '').'</td>
			</tr>
			';
			$total_tax_amt += $tax_amt;

			
			$order_id				=	$_POST['order_id'];
			$order_tax_name		=	$tax["tax_name"];
			$order_tax_percentage	=	$tax["tax_percentage"];
			$order_tax_amount		=	$tax_amt;
			
			mysqli_query($con,"
			INSERT INTO order_tax_table 
			(order_id, order_tax_name, order_tax_percentage, order_tax_amount) 
			VALUES ('$order_id', '$order_tax_name', '$order_tax_percentage', '$order_tax_amount')
			");

		}

		$net_total = $gross_total + $total_tax_amt;

		
		$order_gross_amount	=	$gross_total;
		$order_tax_amount		=	$total_tax_amt;
		$order_net_amount		=	$net_total;
		$order_cashier		=	$object->Get_user_name($_SESSION['user_id']);
	

		mysqli_query($con,"
		UPDATE order_table 
		SET order_gross_amount = '$order_gross_amount', 
		order_tax_amount = '$order_tax_amount', 
		order_net_amount = '$order_net_amount', 
		order_cashier = '$order_cashier' 
		WHERE order_id = '".$_POST["order_id"]."'
		");

		$html .= '
			<tr>
				<td colspan="4" class="text-right"><b>Net Amount</b></td>
				<td colspan="2">'.$object->Get_currency_symbol().' ' . number_format((float)$net_total, 2, '.', '').'</td>
			</tr>
		';

		$html .= '
		</table>
		';

		echo $html;
	}

	if($_POST["action"] == 'Edit')
	{
		$today_date=date("y-m-d");
        date_default_timezone_set('Asia/Calcutta');
        $order_time=date("H:i:sa");

		$order_cashier	=	$object->Get_user_name($_SESSION['user_id']);
		$order_status		=	'Completed';
		
		mysqli_query($con,"
		UPDATE order_table 
		SET order_date = '$order_date', 
		order_time = '$order_time', 
		order_cashier = '$order_cashier', 
		order_status = '$order_status' 
		WHERE order_id = '".$_POST["hidden_order_id"]."'
		");

		echo $_POST["hidden_order_id"];
	}

	if($_POST["action"] == 'remove_bill')
	{
		mysqli_query($con,"
		DELETE FROM order_table 
		WHERE order_id = '".$_POST["order_id"]."'
		");

		mysqli_query($con,"
		DELETE FROM order_item_table 
		WHERE order_id = '".$_POST["order_id"]."'
		");

		mysqli_query($con,"
		DELETE FROM order_tax_table 
		WHERE order_id = '".$_POST["order_id"]."'
		");
		
		echo '<div class="alert alert-success">Order Remove Successfully...</div>';
	}
}

?>