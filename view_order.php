<?php

//view_order.php

if(isset($_GET["pdf"]) && isset($_GET['order_id']))
{
	require_once 'pdf.php';
	include('database_connection.php');
	include('function.php');
	if(!isset($_SESSION['type']))
	{
		header('location:login.php');
	}
	$output = '';
	$statement = $connect->prepare("
		SELECT * FROM inventory_order 
		WHERE inventory_order_id = :inventory_order_id
		LIMIT 1
	");
	$statement->execute(
		array(
			':inventory_order_id'       =>  $_GET["order_id"]
		)
	);
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output .= '
		<table width="100%" border="0.1" cellpadding="0" cellspacing="0" style="font-size: 11px;">
			<tr>
				<td colspan="2" align="center" style="font-size:18px"><b>Invoice</b></td>
			</tr>
			<tr>
				<td colspan="2">
				<table width="100%" cellpadding="5">
					<tr>
						<td width="65%">
							To,<br />
							<b>RECEIVER (BILL TO)</b><br />
							Name : '.$row["inventory_order_name"].'<br />	
							Billing Address : '.$row["inventory_order_address"].'<br />
						</td>
						<td width="35%">
							Reverse Charge<br />
							Invoice No. : '.$row["inventory_order_id"].'<br />
							Invoice Date : '.$row["inventory_order_date"].'<br />
						</td>
					</tr>
				</table>
				<br />
				<table width="100%" border="1" cellpadding="5" cellspacing="0">
					<tr>
						<th rowspan="2">Sr No.</th>
						<th rowspan="2">Product</th>
						<th rowspan="2">Desc</th>
						<th rowspan="2">Batch No</th>
						<th rowspan="2">HSN</th>
						<th rowspan="2">Exp</th>
						<th rowspan="2">Qty</th>
						<th rowspan="2">Price</th>
						<th rowspan="2">Dis (%)</th>
						<th rowspan="2">Actual Amt.</th>
						<th rowspan="2">Taxable Total</th>
						<th colspan="2">SGST (%)</th>
						<th colspan="2">CGST (%)</th>
						<th rowspan="2">Total</th>
					</tr>
					<tr>
						<th>Rate</th>
						<th>Amt.</th>
						<th>Rate</th>
						<th>Amt.</th>
					</tr>
					
		';
		$statement = $connect->prepare("
			SELECT * FROM inventory_order_product 
			WHERE inventory_order_id = :inventory_order_id
		");
		$statement->execute(
			array(
				':inventory_order_id'       =>  $_GET["order_id"]
			)
		);
		$product_result = $statement->fetchAll();
		$count = 0;
		$total = 0;
		$total_actual_amount = 0;
		$total_taxable_amount = 0;
		$total_tax_amount = 0;
		foreach($product_result as $sub_row)
		{
			$count = $count + 1;
			$product_data = fetch_product_details($sub_row['product_id'], $connect);
			$actual_amount = $sub_row["quantity"] * $sub_row["price"];
			$discount_amount = ($actual_amount * $sub_row["discount"])/100;
			$total_product_amount = $actual_amount - $discount_amount;
			$tax_amount = ($total_product_amount * $sub_row["sgst"])/100;
			$total_actual_amount = $total_actual_amount + $actual_amount;
			$total_product_amount_tax = $total_product_amount + $tax_amount;
			$total_taxable_amount = $total_taxable_amount + $total_product_amount;
			$total_tax_amount = $total_tax_amount + $tax_amount;
			$total = $total + $total_product_amount_tax;
			$output .= '
				<tr>
					<td>'.$count.'</td>
					<td>'.$product_data['product_name'].'</td>
					<td>'.$product_data['product_description'].'</td>
					<td>'.$product_data['batch_no'].'</td>
					<td>'.$product_data['hsn'].'</td>
					<td>'.$product_data['expiry_date'].'</td>
					<td>'.$sub_row["quantity"].'</td>
					<td aling="right">'.$sub_row["price"].'</td>
					<td aling="right">'.$sub_row["discount"].'</td>
					<td aling="right">'.number_format($actual_amount, 2).'</td>
					<td align="right">'.number_format($total_product_amount, 2).'</td>
					<td>'.$sub_row["sgst"].'%</td>
					<td align="right">'.number_format($tax_amount, 2).'</td>
					<td>'.$sub_row["cgst"].'%</td>
					<td align="right">'.number_format($tax_amount, 2).'</td>
					<td align="right">'.number_format($total_product_amount_tax, 2).'</td>
				</tr>
			';
		}
		$output .= '
		<tr>
			<td align="right" colspan="9"><b>Total</b></td>
			<td align="right"><b>'.number_format($total_actual_amount, 2).'</b></td>
			<td align="right"><b>'.number_format($total_taxable_amount, 2).'</b></td>
			<td>&nbsp;</td>
			<td align="right" colspan="1"><b>'.number_format($total_tax_amount, 2).'</b></td>
			<td>&nbsp;</td>
			<td align="right" colspan="1"><b>'.number_format($total_tax_amount, 2).'</b></td>
			<td align="right"><b>'.number_format($total, 2).'</b></td>
		</tr>
		';
		$output .= '
						</table>
						<br />
						<br />
						<br />
						<br />
						<br />
						<br />
						<p align="right">----------------------------------------<br />Receiver Signature</p>
						<br />
						<br />
						<br />
					</td>
				</tr>
			</table>
		';
	}
	$pdf = new Pdf();
	$file_name = 'Order-'.$row["inventory_order_id"].'.pdf';
	$pdf->loadHtml($output);
	$pdf->render();
	$pdf->stream($file_name, array("Attachment" => false));
}

?>