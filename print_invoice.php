<?php
//print_invoice.php
ini_set('display_errors', 1);

if (isset($_GET['pdf']) && isset($_GET['id'])) {
	
	require_once 'fpdf/fpdf.php';

	include 'my_db.class.php';

	$orders_keys = "*";
	$orders_tbl = "cust_orders_tbl";
	$query_condtn = "WHERE inv_id = :inv_id";
	$bind_inv_val['inv_id'] = addslashes(floatval(trim($_GET['id'])));
	$cust_order = $query->select_assoc_bind($orders_keys,$orders_tbl,$query_condtn,$bind_inv_val,$pdo);

	$invoice_keys = "*";
	$invoice_tbl = "sales_inv_tbl";
	$query_condtn = "WHERE inv_id = :inv_id LIMIT 1";
	$bind_inv_val['inv_id'] = addslashes(floatval(trim($_GET['id'])));

	$invoice_detail = $query->select_assoc_bind($invoice_keys,$invoice_tbl,$query_condtn,$bind_inv_val,$pdo);
	foreach ($invoice_detail as $inv_dtl) {
		$inv_no = $inv_dtl['inv_no'];
		$inv_date = $inv_dtl['inv_date'];
		$inv_datetime = $inv_dtl['inv_datetime'];
		$cust_name =$inv_dtl['cust_name'];
		$sales_person = $inv_dtl['sales_person'];
		$inv_sub_ttl = $inv_dtl['inv_sub_ttl'];
		$inv_dsc_ttl = $inv_dtl['inv_dsc_ttl'];
		$inv_fnl_ttl = $inv_dtl['inv_fnl_ttl'];
	}


	class myPDF extends FPDF
	{
		
		function header (){
			$this->SetFont('Arial','B',14);
			$this->Cell('',5,'GABRIEL NONSO GABIT LTD',0,0,'C');
			$this->Ln();
			$this->SetFont('Times','',12);
			$this->Cell('',5,'Web Application Developer and Programmer',0,0,'C');
			$this->Ln(20);
		}
		function footer(){
			$this->SetY(-15);
			$this->SetFont('Arial','',8);
			$this->Cell(0,10,'page '.$this->PageNo().'/{nb}',0,0,'C');
		}

		function invoice_info($invoice_detail){			
			foreach ($invoice_detail as $inv_dtl) {	
				$this->SetFont('Times','B',12);
				$this->Cell('',10,'SALES INVOICE',0,2,'C');
				$this->Cell(80,10,'Bill to (Reciever),',0,2);
				$this->Cell(90,5,'Name : '.$inv_dtl['cust_name'],0,0);
				$this->Cell(100,5,'Invoice No. : '.$inv_dtl['inv_no'],0,1,'L');
				$this->Cell(90,5,'Phone no. : '.$inv_dtl['cust_mobile_no'],0,0);
				$this->Cell(100,5,'Invoice Datetime : '.date_format(date_create($inv_dtl['inv_datetime']),"d/m/y H:i:s a"),0,1,'L');
				$this->Cell(90,5,'Address : '.$inv_dtl['cust_address'],0,0);
				$this->Cell(100,5,'Issued By : '.$inv_dtl['sales_person'],0,1,'L');
				$this->Ln();
			}
		}

		function orders_tbl_header(){
			$this->SetFont('Times','B',12);
			$this->Cell(10,10,'S/N',1,0,'C');
			$this->Cell(80,10,'Goods Item',1,0,'C');
			$this->Cell(20,10,'Qty',1,0,'C');
			$this->Cell(35,10,'Price',1,0,'C');
			$this->Cell(45,10,'Amount',1,0,'C');
			$this->Ln();

		}
		function orders($cust_order){
			$this->SetFont('Times','',12);
			$sr_no = 0;
			foreach ($cust_order as $order) {
				$sr_no++;
				$this->SetFont('Times','B',12);
				$this->Cell(10,10,$sr_no,1,0,'C');
				$this->Cell(80,10,$order['order_item'],1,0,'L');
				$this->Cell(20,10,$order['order_qty'].' '.$order['qty_type'],1,0,'L');
				$this->Cell(35,10,'N '.number_format($order['order_price']),1,0,'L');
				$this->Cell(45,10,'N '.number_format($order['order_amt']),1,0,'L');
				$this->Ln();
				
			}
		}

		function invoice_ttl($invoice_detail){			
			foreach ($invoice_detail as $inv_dtl) {	
				$this->SetFont('Times','B',12);
				$this->Cell(135,7,'Sub. Total = ',0,0,'R');
				$this->Cell(50,7,'N '.number_format($inv_dtl['inv_sub_ttl']),0,1,'C');
				$this->Cell(135,7,'Discount = ',0,0,'R');
				$this->Cell(50,7,'N '.number_format($inv_dtl['inv_dsc_ttl']),0,1,'C');
				$this->Cell(135,7,'Final Total = ',0,0,'R');
				$this->Cell(50,7,'N '.number_format($inv_dtl['inv_fnl_ttl']),0,0,'C');
				$this->Ln();
			}
		}
	}




//create pdf object
	$pdf = new myPDF();
	$pdf->AliasNbPages();
	$pdf->AddPage('P','A4',0);
	$pdf->invoice_info($invoice_detail);
	$pdf->orders_tbl_header();
	$pdf->orders($cust_order);
	$pdf->invoice_ttl($invoice_detail);
	//$pdf->SetFont('Arial');
	//$pdf->WriteHTML($output);
	$pdf->Output();

}




?>