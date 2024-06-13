<?php

//product_action.php

include('rms.php');

$object = new rms();

$con = $object->connect();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('category_name', 'product_name', 'product_price', 'product_status');

		$output = array();

		$main_query = "
		SELECT * FROM product_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE category_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR product_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR product_price LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR product_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY product_id DESC ';
		}

		$limit_query = '';

		if($_POST["length"] != -1)
		{
			$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$combinequery = mysqli_query($con, $main_query . $search_query . $order_query);

    $filtered_rows=mysqli_num_rows($combinequery);

    $combinequery = mysqli_query($con,$main_query . $search_query . $order_query . $limit_query);

    $main_query2 = mysqli_query($con,"
    SELECT * FROM product_table ");

    $total_rows = mysqli_num_rows($main_query2);

    $data = array();

    while($row = mysqli_fetch_array($combinequery))
    {
			$sub_array = array();
			$sub_array[] = html_entity_decode($row["product_name"]);
			$sub_array[] = $object->Get_currency_symbol().' ' . $row["product_price"];
			$sub_array[] = $row["category_name"];
			$status = '';
			if($row["product_status"] == 'Enable')
			{
				$status = '<button type="button" name="status_button" class="btn btn-primary btn-sm status_button" data-id="'.$row["product_id"].'" data-status="'.$row["product_status"].'">Enable</button>';
			}
			else
			{
				$status = '<button type="button" name="status_button" class="btn btn-danger btn-sm status_button" data-id="'.$row["product_id"].'" data-status="'.$row["product_status"].'">Disable</button>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["product_id"].'"><i class="fas fa-edit"></i></button>
			&nbsp;
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["product_id"].'"><i class="fas fa-times"></i></button>
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

	if($_POST["action"] == 'Add')
	{
		$error = '';

		$success = '';

		$category_name	=	$_POST["category_name"];
		$product_name	= $_POST["product_name"];

		$query = mysqli_query($con,"
		SELECT * FROM product_table 
		WHERE category_name = '$category_name' 
		AND product_name = '$product_name'
		");

		$data=mysqli_num_rows($query);

    	if($data > 0)
		{
			$error = '<div class="alert alert-danger">Product Already Exists</div>';
		}
		else
		{
			$category_name  =  $_POST["category_name"];
	        $product_name   =  $object->clean_input($_POST["product_name"]);
	        $product_price  =  $object->clean_input($_POST["product_price"]);
	        $product_status =  'Enable';

	      	mysqli_query($con,"
	      	INSERT INTO product_table 
	      	(category_name, product_name, product_price, product_status) 
	      	VALUES ('$category_name', '$product_name', '$product_price', '$product_status')
	      	");

			$success = '<div class="alert alert-success">Product Added</div>';
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{
		$result = mysqli_query($con,"
		SELECT * FROM product_table 
		WHERE product_id = '".$_POST["product_id"]."'
		");

		$data = array();

		foreach($result as $row)
		{
			$data['category_name'] = $row['category_name'];
			$data['product_name'] = $row['product_name'];
			$data['product_price'] = $row['product_price'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		
		$category_name	=	$_POST["category_name"];
		$product_name		=	$_POST["product_name"];
		$product_id		=	$_POST['hidden_id'];

		$query = mysqli_query($con,"
		SELECT * FROM product_table 
		WHERE category_name = '$category_name' 
		AND product_name = '$product_name'
		AND product_id != '$product_id'
		");

		$data = mysqli_num_rows($query);

		if($data > 0)
		{
			$error = '<div class="alert alert-danger">Product Already Exists</div>';
		}
		else
		{
			
			$category_name	=	$_POST["category_name"];
			$product_name		=	$object->clean_input($_POST["product_name"]);
			$product_price	=	$object->clean_input($_POST["product_price"]);
			
			mysqli_query($con,"
			UPDATE product_table 
			SET category_name = '$category_name', 
			product_name = '$product_name', 
			product_price = '$product_price'   
			WHERE product_id = '".$_POST['hidden_id']."'
			");

			$success = '<div class="alert alert-success">Product Updated</div>';
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'change_status')
	{
		
		$product_status		=	$_POST['next_status'];
		
		mysqli_query($con,"
		UPDATE product_table 
		SET product_status ='$product_status' 
		WHERE product_id = '".$_POST["id"]."'
		");

		echo '<div class="alert alert-success">Product Status change to '.$_POST['next_status'].'</div>';
	}

	if($_POST["action"] == 'delete')
	{
		mysqli_query($con,"
		DELETE FROM product_table 
		WHERE product_id = '".$_POST["id"]."'
		");

		echo '<div class="alert alert-success">Product Deleted</div>';
	}
}

?>