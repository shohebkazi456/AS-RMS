<?php

//table_action.php

include('rms.php');

$object = new rms();

$con = $object->connect();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('table_name', 'table_capacity', 'table_status');

		$output = array();

		$main_query = "
		SELECT * FROM table_data ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE table_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR table_capacity LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR table_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY table_id DESC ';
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
	    SELECT * FROM table_data ");

		$total_rows = mysqli_num_rows($main_query2);

		$data = array();

		while($row = mysqli_fetch_array($combinequery))
		{
			$sub_array = array();
			$sub_array[] = html_entity_decode($row["table_name"]);
			$sub_array[] = $row["table_capacity"] . ' Person';
			$status = '';
			if($row["table_status"] == 'Enable')
			{
				$status = '<button type="button" name="status_button" class="btn btn-primary btn-sm status_button" data-id="'.$row["table_id"].'" data-status="'.$row["table_status"].'">Enable</button>';
			}
			else
			{
				$status = '<button type="button" name="status_button" class="btn btn-danger btn-sm status_button" data-id="'.$row["table_id"].'" data-status="'.$row["table_status"].'">Disable</button>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["table_id"].'"><i class="fas fa-edit"></i></button>
			&nbsp;
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["table_id"].'"><i class="fas fa-times"></i></button>
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

		$table_name = $_POST["table_name"];

		$res=mysqli_query($con,"SELECT * FROM table_data WHERE table_name = '$table_name'");

		$data=mysqli_num_rows($res);

		if($data > 0)
		{
			$error = '<div class="alert alert-danger">Table Already Exists</div>';
		}
		else
		{
			     
			$table_name   =  $object->clean_input($_POST["table_name"]);
            $table_capacity  =  $object->clean_input($_POST["table_capacity"]);
            $table_status = 'Enable';

			mysqli_query($con,"
            INSERT INTO table_data 
            (table_name, table_capacity, table_status) 
            VALUES ('$table_name', '$table_capacity', '$table_status')
            ");

			$success = '<div class="alert alert-success">Table Added</div>';
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
		SELECT * FROM table_data 
		WHERE table_id = '".$_POST["table_id"]."'
		");

		$data = array();

		foreach($query as $row)
		{
			$data['table_name'] = $row['table_name'];
			$data['table_capacity'] = $row['table_capacity'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$table_name	=	$_POST["table_name"];
		$table_capacity	=	$_POST["table_capacity"];
		$table_id		=	$_POST['hidden_id'];

		$query=mysqli_query($con,"
		SELECT * FROM table_data 
		WHERE table_name = '$table_name' 
		AND table_id != '$table_id'
		");

		$data=mysqli_num_rows($query);

		if($data > 0)
		{
			$error = '<div class="alert alert-danger">Table Already Exists</div>';
		}
		else
		{

			$table_name		=	$object->clean_input($_POST["table_name"]);

			mysqli_query($con,"
			UPDATE table_data 
			SET table_name = '$table_name',
			table_capacity = '$table_capacity'
			WHERE table_id = '".$_POST['hidden_id']."'
			");

			$success = '<div class="alert alert-success">Table Updated</div>';
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'change_status')
	{
		$table_status		=	$_POST['next_status'];

		mysqli_query($con,"
		UPDATE table_data 
		SET table_status = '$table_status' 
		WHERE table_id = '".$_POST["id"]."'
		");

		echo '<div class="alert alert-success">Table Status change to '.$_POST['next_status'].'</div>';
	}

	if($_POST["action"] == 'delete')
	{
		mysqli_query($con,"
		DELETE FROM table_data 
		WHERE table_id = '".$_POST["id"]."'
		");

		echo '<div class="alert alert-success">Table Deleted</div>';
	}
}

?>