<?php
//seller.php

include('database_connection.php');

if(!isset($_SESSION['type']))
{
	header('location:login.php');
}

if($_SESSION['type'] != 'master')
{
	header("location:index.php");
}

include('header.php');

?>

	<span id="alert_action"></span>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
                <div class="panel-heading">
                    <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                        <div class="row">
                            <h3 class="panel-title">Seller List</h3>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                        <div class="row" align="right">
                             <button type="button" name="add" id="add_button" data-toggle="modal" data-target="#sellerModal" class="btn btn-success btn-xs">Add</button>   		
                        </div>
                    </div>
                    <div style="clear:both"></div>
                </div>
                <div class="panel-body">
                    <div class="row">
                    	<div class="col-sm-12 table-responsive">
                    		<table id="seller_data" class="table table-bordered table-striped">
                    			<thead><tr>
									<th>ID</th>
									<th>Seller Name</th>
									<th>Status</th>
									<th>Edit</th>
									<th>Delete</th>
								</tr></thead>
                    		</table>
                    	</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="sellerModal" class="modal fade">
    	<div class="modal-dialog">
    		<form method="post" id="seller_form">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Add seller</h4>
    				</div>
    				<div class="modal-body">
    					<label>Enter seller Name</label>
						<input type="text" name="seller_name" id="seller_name" class="form-control" required />
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="seller_id" id="seller_id"/>
    					<input type="hidden" name="btn_action" id="btn_action"/>
    					<input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
    					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    				</div>
    			</div>
    		</form>
    	</div>
    </div>
<script>
$(document).ready(function(){

	$('#add_button').click(function(){
		$('#seller_form')[0].reset();
		$('.modal-title').html("<i class='fa fa-plus'></i> Add seller");
		$('#action').val('Add');
		$('#btn_action').val('Add');
	});

	$(document).on('submit','#seller_form', function(event){
		event.preventDefault();
		$('#action').attr('disabled','disabled');
		var form_data = $(this).serialize();
		$.ajax({
			url:"seller_action.php",
			method:"POST",
			data:form_data,
			success:function(data)
			{
				$('#seller_form')[0].reset();
				$('#sellerModal').modal('hide');
				$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
				$('#action').attr('disabled', false);
				sellerdataTable.ajax.reload();
			}
		})
	});

	$(document).on('click', '.update', function(){
		var seller_id = $(this).attr("id");
		var btn_action = 'fetch_single';
		$.ajax({
			url:"seller_action.php",
			method:"POST",
			data:{seller_id:seller_id, btn_action:btn_action},
			dataType:"json",
			success:function(data)
			{
				$('#sellerModal').modal('show');
				$('#seller_name').val(data.seller_name);
				$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit seller");
				$('#seller_id').val(seller_id);
				$('#action').val('Edit');
				$('#btn_action').val("Edit");
			}
		})
	});

	var sellerdataTable = $('#seller_data').DataTable({
		"processing":true,
		"serverSide":true,
		"order":[],
		"ajax":{
			url:"seller_fetch.php",
			type:"POST"
		},
		"columnDefs":[
			{
				"targets":[3, 4],
				"orderable":false,
			},
		],
		"pageLength": 25
	});
	$(document).on('click', '.delete', function(){
		var seller_id = $(this).attr('id');
		var status = $(this).data("status");
		var btn_action = 'delete';
		if(confirm("Are you sure you want to change status?"))
		{
			$.ajax({
				url:"seller_action.php",
				method:"POST",
				data:{seller_id:seller_id, status:status, btn_action:btn_action},
				success:function(data)
				{
					$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
					sellerdataTable.ajax.reload();
				}
			})
		}
		else
		{
			return false;
		}
	});
});
</script>

<?php
include('footer.php');
?>


				