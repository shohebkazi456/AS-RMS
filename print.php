<?php

//print.php

use Dompdf\Dompdf;

ob_start();  

require_once 'dompdf/autoload.inc.php';

include('rms.php');

$object = new rms();

$con = $object->connect();

if(!$object->is_login())
{
    header("location:".$object->base_url."");
}

if(!$object->is_cashier_user() && !$object->is_master_user())
{
    header("location:".$object->base_url."dashboard.php");
}

$file_name = '';

if(isset($_GET["order_id"]))
{
	$output = '
	<table width="100%" border="0" cellpadding="5" cellspacing="5" style="font-family:Arial, san-sarif">';

	$restaurant_data=mysqli_query($con,"
		SELECT * FROM restaurant_table
	");

	foreach($restaurant_data as $row)
	{
		$output .= '
		<tr>
			<td align="center">
				<b style="font-size:32px">'.$row["restaurant_name"].'</b>
				<br />
				<span style="font-size:20px;">'.$row["restaurant_tag_line"].'</span>
				<br /><br />
				<span style="font-size:16px;">'.$row["restaurant_address"].'</span>
				<br />
				<span style="font-size:16px;"><b>Contact No. - </b>'.$row["restaurant_contact_no"].'</span>
				<br />
				<span style="font-size:16px;"><b>Email - </b>'.$row["restaurant_email"].'</span>
				<br /><br />
			</td>
		</tr>
		';
	}

	$order_result=mysqli_query($con,"
		SELECT * FROM order_table 
		WHERE order_id = '".$_GET["order_id"]."'
	");

	foreach($order_result as $order)
	{
		$file_name = $order["order_number"] . '.pdf';
		$output .= '
		<tr>
			<td>
				<table width="100%" border="0" cellpadding="4" cellspacing="4">
					<tr>
						<td width="25%"><b>Bill No:- </b>'.$order["order_number"].'</td>
						<td width="25%"><b>Table No:- </b>'.$order["order_table"].'</td>
						<td width="25%" align="right"><b>Date:- </b>'.$order["order_date"].'</td>
						<td width="25%" align="right"><b>Time:- </b>'.$order["order_time"].'</td>
					</tr>
				</table>
			</td>
		</tr>
		';

		$order_item_result=mysqli_query($con,"
			SELECT * FROM order_item_table 
			WHERE order_id = '".$_GET["order_id"]."' 
			ORDER BY order_item_id ASC
		");

		$output .= '
			<tr>
				<td>
					<table width="100%" border="1" cellpadding="10" cellspacing="0">
						<tr>
							<th width="10%">Sr#</th>
							<th width="45%">Item</th>
							<th width="10%">Qty.</th>
							<th width="20%">Price</th>
							<th width="15%">Amount</th>
						</tr>';
		$count = 0;
		foreach($order_item_result as $item)
		{
			$count++;
			$output .= '
						<tr>
							<td>'.$count.'</td>
							<td>'.$item["product_name"].'</td>
							<td>'.$item["product_quantity"].'</td>
							<td style="font-family: DejaVu Sans, sans-serif;">'.$object->Get_currency_symbol().' ' . $item["product_rate"].'</td>
							<td style="font-family: DejaVu Sans, sans-serif;">'.$object->Get_currency_symbol().' ' . $item["product_amount"].'</td>
						</tr>
			';
		}

		$tax_result=mysqli_query($con,"
		SELECT * FROM order_tax_table 
		WHERE order_id = '".$_GET["order_id"]."'
		");

		// $tax_result = $object->execute();

		$total_tax_row = mysqli_num_rows($tax_result);

		$rowspan = 2 + $total_tax_row;

		// $tax_result = $object->statement_result();

		$output .= '
						<tr>
							<td rowspan="'.$rowspan.'" colspan="3">
							<b>Cashier : </b>'.$order["order_cashier"].'
							</td>
							<td align="right"><b>Gross Total</b></td>
							<td style="font-family: DejaVu Sans, sans-serif;">'.$object->Get_currency_symbol().' '  . $order["order_gross_amount"].'</td>
						</tr>
		';

		foreach($tax_result as $tax)
		{
			$output .= '
						<tr>
							<td align="right"><b>'.$tax["order_tax_name"].' ('.$tax["order_tax_percentage"].'%)</b></td>
							<td style="font-family: DejaVu Sans, sans-serif;">'.$object->Get_currency_symbol().' '  . $tax["order_tax_amount"].'</td>
						</tr>
			';
		}

		$output .= '
						<tr>
							<td align="right"><b>Net Amount</b></td>
							<td style="font-family: DejaVu Sans, sans-serif;">'.$object->Get_currency_symbol().' '  . $order["order_net_amount"].'</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="center">Thank you, Please come again</td>
			</tr>
			';
	}

	$output .= '</table>';

	// $pdf = new Pdf();

	// $pdf->loadHtml($output, 'UTF-8');
	// $pdf->render();
	// $pdf->stream($file_name, array( 'Attachment'=>0 ));
	// exit(0);


// require_once 'dompdf/autoload.inc.php';
// reference the Dompdf namespace

// use Dompdf\Dompdf;

// instantiate and use the dompdf class
$document = new Dompdf();

$document->loadHtml($output, 'UTF-8');

// (Optional) Setup the paper size and orientation
$document->setPaper('A4', 'portrait');

// Render the HTML as PDF
$document->render();

// Output the generated PDF to Browser
$document->stream($file_name, array( 'Attachment'=>0 ));

}

?>