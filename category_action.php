<?php

//category_action.php

include('rms.php');

$object = new rms();

$con = $object->connect();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('category_name', 'category_status');

		$output = array();

		$main_query = "
		SELECT * FROM product_category_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE category_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR category_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY category_id DESC ';
		}

		$limit_query = '';

		if($_POST["length"] != -1)
		{
			$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$combinequery = mysqli_query($con,$main_query . $search_query . $order_query);

		$filtered_rows=mysqli_num_rows($combinequery);

		$combinequery = mysqli_query($con,$main_query . $search_query . $order_query . $limit_query);

		$main_query2 = mysqli_query($con,"
    SELECT * FROM product_category_table ");

		$total_rows = mysqli_num_rows($main_query2);

        $data = array();

		while($result = mysqli_fetch_array($combinequery))
		{
			$sub_array = array();
			$sub_array[] = html_entity_decode($result["category_name"]);
			$status = '';
			if($result["category_status"] == 'Enable')
			{
				$status = '<button type="button" name="status_button" class="btn btn-primary btn-sm status_button" data-id="'.$result["category_id"].'" data-status="'.$result["category_status"].'">Enable</button>';
			}
			else
			{
				$status = '<button type="button" name="status_button" class="btn btn-danger btn-sm status_button" data-id="'.$result["category_id"].'" data-status="'.$result["category_status"].'">Disable</button>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$result["category_id"].'"><i class="fas fa-edit"></i></button>
			&nbsp;
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$result["category_id"].'" data-status="'.$result["category_status"].'"><i class="fas fa-times"></i></button>
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

		$category_name = $_POST["category_name"];

		$res=mysqli_query($con,"
		SELECT * FROM product_category_table 
		WHERE category_name = '$category_name'
		");

		$data=mysqli_num_rows($res);

		if($data > 0)
		{
			$error = '<div class="alert alert-danger">Category Already Exists</div>';
		}
		else
		{
			        
			$category_name        =  $object->clean_input($_POST["category_name"]);
            $category_status  =  'Enable';

            mysqli_query($con,"
            INSERT INTO product_category_table 
            (category_name, category_status) 
            VALUES ('$category_name', '$category_status')
            ");

			$success = '<div class="alert alert-success">Category Added</div>';
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{

		$query=mysqli_query($con,"
		SELECT * FROM product_category_table 
		WHERE category_id = '".$_POST["category_id"]."'
		");

		$data = array();

		foreach($query as $row)
		{
			$data['category_name'] = $row['category_name'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{

		$error = '';

		$success = '';

		$category_name = $_POST['category_name'];
		$category_id	=	$_POST['hidden_id'];

		$query=mysqli_query($con,"
		SELECT * FROM product_category_table 
		WHERE category_name = '$category_name' 
		AND category_id != '$category_id'
		");

		$data=mysqli_num_rows($query);

		if($data > 0)
		{
			$error = '<div class="alert alert-danger">Category Already Exists</div>';
		}
		else
		{

			$category_name = $_POST['category_name'];

			mysqli_query($con,"
            UPDATE product_category_table 
            SET category_name = '$category_name' 
            WHERE category_id = '".$_POST['hidden_id']."'
            ");

			$success = '<div class="alert alert-success">Category Updated</div>';
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'change_status')
	{
		$category_status		=	$_POST['next_status'];

		mysqli_query($con,"
		UPDATE product_category_table 
		SET category_status = '$category_status' 
		WHERE category_id = '".$_POST["id"]."'
		");

		echo '<div class="alert alert-success">Category Status change to '.$_POST['next_status'].'</div>';
	}

	if($_POST["action"] == 'delete')
	{
		mysqli_query($con,"
		DELETE FROM product_category_table 
		WHERE category_id = '".$_POST["id"]."'
		");

		echo '<div class="alert alert-success">Category Deleted</div>';
	}
}

?>