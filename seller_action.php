<?php

//seller_action.php

include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO seller (seller_name) 
		VALUES (:seller_name)
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':seller_name'	=>	$_POST["seller_name"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'seller Name Added';
		}
	}
	
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "SELECT * FROM seller WHERE seller_id = :seller_id";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':seller_id'	=>	$_POST["seller_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$output['seller_name'] = $row['seller_name'];
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$query = "
		UPDATE seller set seller_name = :seller_name  
		WHERE seller_id = :seller_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':seller_name'	=>	$_POST["seller_name"],
				':seller_id'		=>	$_POST["seller_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'seller Name Edited';
		}
	}
	if($_POST['btn_action'] == 'delete')
	{
		$status = 'active';
		if($_POST['status'] == 'active')
		{
			$status = 'inactive';	
		}
		$query = "
		UPDATE seller 
		SET seller_status = :seller_status 
		WHERE seller_id = :seller_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':seller_status'	=>	$status,
				':seller_id'		=>	$_POST["seller_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'seller status change to ' . $status;
		}
	}
}

?>