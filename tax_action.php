<?php

//tax_action.php

include('rms.php');

$object = new rms();

$con = $object->connect();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('tax_name', 'tax_percentage', 'tax_status');

		$output = array();

		$main_query = "
		SELECT * FROM tax_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE tax_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR tax_percentage LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR tax_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY tax_id DESC ';
		}

		$limit_query = '';

		if($_POST["length"] != -1)
		{
			$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$combinequery = mysqli_query($con, $main_query . $search_query . $order_query);

		$filtered_rows=mysqli_num_rows($combinequery);

		$combinequery = mysqli_query($con, $main_query . $search_query . $order_query . $limit_query);

		$main_query2 = mysqli_query($con,"
    	SELECT * FROM tax_table ");

		$total_rows = mysqli_num_rows($main_query2);

        $data = array();

		while($row = mysqli_fetch_array($combinequery))
		{
			$sub_array = array();
			$sub_array[] = html_entity_decode($row["tax_name"]);
			$sub_array[] = $row["tax_percentage"] . ' %';
			$status = '';
			if($row["tax_status"] == 'Enable')
			{
				$status = '<button type="button" name="status_button" class="btn btn-primary btn-sm status_button" data-id="'.$row["tax_id"].'" data-status="'.$row["tax_status"].'">Enable</button>';
			}
			else
			{
				$status = '<button type="button" name="status_button" class="btn btn-danger btn-sm status_button" data-id="'.$row["tax_id"].'" data-status="'.$row["tax_status"].'">Disable</button>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["tax_id"].'"><i class="fas fa-edit"></i></button>
			&nbsp;
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["tax_id"].'"><i class="fas fa-times"></i></button>
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

		$tax_name = $_POST["tax_name"];

		$res=mysqli_query($con,"
      SELECT * FROM tax_table 
      WHERE tax_name = '$tax_name'
      ");

		$data=mysqli_num_rows($res);

        if($data > 0)
		{
			$error = '<div class="alert alert-danger">Tax Already Exists</div>';
		}
		else
		{

			$tax_name     =  $object->clean_input($_POST["tax_name"]);
          $tax_percentage =  $object->clean_input($_POST["tax_percentage"]);
          $tax_status   =  'Enable';

			mysqli_query($con,"
        INSERT INTO tax_table 
        (tax_name, tax_percentage, tax_status) 
        VALUES ('$tax_name', '$tax_percentage', '$tax_status')
        ");

			$success = '<div class="alert alert-success">Tax Added</div>';
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
		SELECT * FROM tax_table 
		WHERE tax_id = '".$_POST["tax_id"]."'
		");

		$data = array();

		foreach($query as $row)
		{
			$data['tax_name'] = $row['tax_name'];
			$data['tax_percentage'] = $row['tax_percentage'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$tax_name		=	$_POST["tax_name"];
		$tax_id		=	$_POST['hidden_id'];

		$query=mysqli_query($con,"
		SELECT * FROM tax_table 
		WHERE tax_name = '$tax_name' 
		AND tax_id != '$tax_id'
		");

		$data=mysqli_num_rows($query);

		if($data > 0)
		{
			$error = '<div class="alert alert-danger">Tax Already Exists</div>';
		}
		else
		{

			
			$tax_name			=	$object->clean_input($_POST["tax_name"]);
			$tax_percentage	=	$object->clean_input($_POST["tax_percentage"]);
			

			mysqli_query($con,"
			UPDATE tax_table 
			SET tax_name = '$tax_name', 
			tax_percentage = '$tax_percentage'  
			WHERE tax_id = '".$_POST['hidden_id']."'
			");

			$success = '<div class="alert alert-success">Tax Updated</div>';
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'change_status')
	{
		$tax_status		=	$_POST['next_status'];
	
		mysqli_query($con,"
		UPDATE tax_table 
		SET tax_status = '$tax_status' 
		WHERE tax_id = '".$_POST["id"]."'
		");

		echo '<div class="alert alert-success">Tax Status change to '.$_POST['next_status'].'</div>';
	}

	if($_POST["action"] == 'delete')
	{
		mysqli_query($con,"
		DELETE FROM tax_table 
		WHERE tax_id = '".$_POST["id"]."'
		");

		echo '<div class="alert alert-success">Tax Deleted</div>';
	}
}

?>