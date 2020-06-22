<?php
if(isset($_POST['genInvSubmit'])){
    if(!preg_match('/^[0-9]{0,10}$/', $_POST['tid'])){
        die();
    }else{
        $id = $_POST['tid'];
    }
    
	define(HOURLY_RATE, 50);
	$subtotal = 0.00;
	define(TAX_RATE, 0.0625);
	$total = 0.00;


	require "fpdf182/fpdf.php";
	require "../connect.php";

	$ticket_date = '';
	$company_name = '';
	$address = '';
	$city = '';
	$state = '';
	$zip = '';
	$phone = '';
	$machine = '';
	$issue = '';
	$progress = array();


	$issue = '';

	try {
	    $sql = $dbc->query("SELECT 
	    	a.id AS id, a.tech AS tech, a.date AS ticket_date, a.cust_id AS cust_id, a.mach_id AS t_mach_id, a.issue AS issue,
	    	b.id AS customer_id, b.company AS company_name, b.address AS address, b.city AS city, b.state AS state, b.zip AS zip, b.phone AS phone,
	    	c.id AS machine_id, c.name AS machine_name,
	    	d.pid AS progress_id, d.t_id AS t_id, d.tech_id AS tech_id, d.date AS progress_date, d.summary AS summary, d.hours AS hours
	    	FROM Tickets a
	    	LEFT OUTER JOIN Customers b
	    	ON a.cust_id = b.id
	    	LEFT OUTER JOIN Machines c
	    	ON a.mach_id = c.id
	    	LEFT OUTER JOIN Progress d
	    	ON d.t_id = a.id
	    	WHERE a.id = '$id'
	    	");

	    $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	    while($row = $sql->fetch(PDO::FETCH_ASSOC)){

	    	$ticket_id = $row['id'];
	    	$ticket_date = date("F j, Y", strtotime($row['ticket_date']));
			$company_name = htmlspecialchars_decode($row['company_name'], ENT_QUOTES);
			$address = $row['address'];
			$city = $row['city'];
			$state = $row['state'];
			$zip = $row['zip'];
			$phone = $row['phone'];
			$machine = $row['machine_name'];
			$issue = trim(htmlspecialchars_decode($row['issue'], ENT_QUOTES));
			$progress['date'][] = $row['progress_date'];
			$progress['summary'][] = $row['summary'];
			$progress['hours'][] = $row['hours'];
			$progress['id'][] = $row['progress_id'];

	    }

	}
	catch (PDOException $e) {
	    printf("Problem " . $e->getMessage());
	}


	class myPDF extends FPDF{

		function Header(){
			$this->SetMargins(2,2,2);
			$this->Image('images/ed_logo_bw.png', 8, 2, 40);
			$this->SetFont('Arial','B',14);
			// Move to the right
		    $this->Cell(60);
		    // Framed title
		    $this->Cell(60,7,'Machine Repair Report',0,1,'C');
		    // Line break
		    $this->Ln(7);
		}
		function Footer(){
			$this->SetFont('Times','',9);
			$this->Ln(30);
			$this->MultiCell(180,5,'*This is a sample invoice, intended solely for demonstrational purposes. This is not an attempt to claim any actual compensation.',0,1);
		}

	}

	$pdf = new myPDF();
	$pdf->AliasNbPages();
	$pdf->AddPage('P', 'A4', 0);
	$pdf->SetMargins(8,0,8);
	//spacer after page header
	$pdf->Cell(50,8,'',0,1);

	//INFORMATION HEADER ROW
	$pdf->SetFont('Times','',10);
	$pdf->SetTextColor(100,100,100);
	$pdf->SetFillColor(220,220,220);
	$pdf->Cell(70,6,'Customer:', 0,0, L,true);
	$pdf->Cell(50,6,'Machine:', 0,0, L,true);
	$pdf->Cell(35,6,'Date Submitted:', 0,0, L,true);
	$pdf->Cell(30,6,'Ticket Number:', 0,1, L,true);
	//Spacer after category headers
	$pdf->Cell(185,2,'',0,1);

	//INFORMATION TEXT
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('Times','',12);
	$pdf->Cell(70,5, $company_name,0,0);
	$pdf->Cell(50,5,$machine,0,0);
	$pdf->Cell(35,5, $ticket_date,0,0);
	$pdf->Cell(30,5, $ticket_id,0,1);
	$pdf->Cell(80,5, $address,0,1);
	$cityWidth = $pdf->GetStringWidth($city);
	$pdf->Cell($cityWidth + 3,5, $city . ', ',0,0);
	$pdf->Cell(8,5, $state,0,0);
	$pdf->Cell(18,5,$zip, 0,1);
	$pdf->Cell(50,5,$phone,0,1);

	$pdf->Cell(50,7,'',0,1);

	//Issue Header
    $pdf->SetFont('Times','',10);
	$pdf->SetTextColor(100,100,100);
	$pdf->SetFillColor(220,220,220);
	$pdf->Cell(185,6,'Repair Issue', 0,1, L,true);
	$pdf->Cell(185,2,'',0,1);
	$pdf->SetTextColor(0,0,0);

    $pdf->MultiCell(185,4, $issue,0,1);


    $pdf->Cell(50,10,'',0,1);

	//Work Performed Header
    $pdf->SetFont('Times','',10);
	$pdf->SetTextColor(100,100,100);
	$pdf->SetFillColor(220,220,220);
	$pdf->Cell(185,6,'Work Performed', 0,1, L,true);
	$pdf->Cell(185,2,'',0,1);
	$pdf->SetTextColor(0,0,0);

	if(!empty($progress['id'][0])){

		for($i=0;$i<count($progress['id']);$i++){

			$pdate = date("F j, Y", strtotime($progress['date'][$i]));
			$prog_parts_cost = 0.00;

			$pdf->SetFont('Times','B',10);
			$pdf->Cell(28,7, $pdate,0,1);
			$pdf->SetFont('Times','',10);
			$pdf->MultiCell(185,4, $progress['summary'][$i],0,1);
			if($progress['hours'][$i] > 1){
				$hrs = 'Time Spent: '. $progress['hours'][$i] . ' hours.';
			}else{
		        $hrs = 'Time Spent: '. $progress['hours'][$i] . ' hour.';
			}


			$pdf->Cell(185,3,'',0,1);
        	$pdf->SetFont('Times','',8);
        	$pdf->SetTextColor(8,8,8);
			$pdf->Cell(100,4,'',0,0);
			$pdf->SetFillColor(240,240,240);
			$pdf->Cell(85,4,'Parts and Labor for this Work Unit:','B',1,'L',true);
			$pdf->SetTextColor(0,0,0);

			$pdf->SetFont('Times','',10);
			$pdf->Cell(100,6,'',0,0); 
			$pdf->Cell(39,6,$hrs,'B',0);
			$pdf->Cell(25,6,'@    $'.HOURLY_RATE.'/hr','B',0);
			$pdf->Cell(2,6,'=','B',0);
			$subt_hours = ($progress['hours'][$i] * HOURLY_RATE);
			$subt_hours_display = '$'.number_format($subt_hours,2,'.',',');
			$pdf->Cell(19,6,$subt_hours_display,'B',1,'R');

		    	$prid = $progress['id'][$i];
				$partCheck = $dbc->query("SELECT  
					e.prog_id AS pp_prog_id, e.part_id AS pp_part_id, e.qty AS qty, e.tid AS pp_tid,
					f.id AS part_id, f.name AS part_name, f.price AS price 
					FROM progparts e
					LEFT OUTER JOIN Parts f
					ON e.part_id = f.id
					WHERE e.prog_id = '$prid'");

					$amount = $partCheck->rowCount();

					if($amount > 0){

						while($line = $partCheck->fetch(PDO::FETCH_ASSOC)){

							$pdf->Cell(100,5,'',0,0);
							$pdf->Cell(5, 5,$line['qty'],'B',0);
							$pdf->Cell(35,5,$line['part_name'],'B',0);
							$pdf->Cell(5,5,'@','B',0,'C');
							$price = $line['price'];
							$price_display = '$'.number_format($price,2,'.',',');
							$qty = $line['qty'];
							$subt_part = $qty * $price;
							$subt_part_display = '$'.number_format($subt_part,2,'.',',');
	                        $pdf->Cell(19,5,$price_display,'B',0);
	                        $pdf->Cell(2,5,'=','B',0);
							$pdf->Cell(19,5,$subt_part_display,'B',1,'R');
							$prog_parts_cost += $subt_part;
						}

					}else{
						$pdf->Cell(185,5,'No parts added', 0, 1, 'R');
					}

					
			$subt_prog = $prog_parts_cost + $subt_hours;
			$subtotal += $subt_prog; 
			$pdf->Cell(125,5,'',0,0);
			$pdf->Cell(40,5,'Total for this Work Unit',0,0);		
			$pdf->Cell(20,5,'$'.number_format($subt_prog,2,'.',','),0,1,'R');
			//under Progress spacer	
			$pdf->Cell(185,2,'',0,1);	
			$pdf->Cell(185,2,'','T',1);	

		}

	}else{

		$pdf->Cell(185,5,'No work done',1,1);
	}

	$pdf->Cell(130,5,'',0,0);
	$pdf->Cell(35,5,'Subtotal Parts & Labor:',1,0);
	$pdf->Cell(20,5,'$'.number_format($subtotal,2,'.',','),1,1,'R');

	$pdf->Cell(130,5,'',0,0);
	$pdf->Cell(35,5,'Tax:',1,0,'R');
	$tax = $subtotal * TAX_RATE;
	$pdf->Cell(20,5,'$'.number_format($tax,2,'.',','),1,1,'R');

	$pdf->Cell(130,5,'',0,0);
	$pdf->Cell(35,5,'Total*:',1,0,'R');
	$total = $subtotal + $tax;
	$pdf->Cell(20,5,'$'.number_format($total,2,'.',','),1,1,'R');

	$pdf->Output();


}
