<?php


class Closed {


	public $id = '0';
	public $company;
	public $machine;
	public $ticketDate;

    function __construct(){

    }

	private function getCompanies(){
		require('../connect.php');
		if($request = $dbc->query("SELECT id, company FROM Customers")){
			echo '<select name="company" id="company-list">';
			echo '<option value=""></option>';
			while($row = $request->fetch(PDO::FETCH_ASSOC)){
				echo '<option value="'.$row['id'].'">'.$row['company'].'</option>';
			}
			echo '</select>';
		}else{

		}
	}
	private function getMachines(){
		require('../connect.php');
		if($request = $dbc->query("SELECT id, name FROM Machines")){
			echo '<select name="machine" id="machine-list">';
			echo '<option value=""></option>';
			while($row = $request->fetch(PDO::FETCH_ASSOC)){
				echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
			}
			echo '</select>';
		}else{

		}
	}
	private function getTechs(){
		require('../connect.php');
		if($request = $dbc->query("SELECT id, name FROM Technicians")){
			echo '<select name="tech" id="tech-list">';
			echo '<option value=""></option>';
			while($row = $request->fetch(PDO::FETCH_ASSOC)){
				echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
			}
			echo '</select>';
		}else{

		}
	}

	public function searchForm(){
		echo'<form action="actions.php" method="POST">';
		echo $this->getCompanies();
		echo $this->getMachines();
		echo $this->getTechs();
		echo '<input type="submit" name="searchClosedSubmit" id="search-closed-submit" value="SEARCH">';
		echo '</form>';


	}

	public function generateTab($id){

		require('../connect.php');

		try {
			$sql = "SELECT 
			a.id AS id, a.tech AS tech_id, a.date AS date, a.cust_id AS cust_id, a.mach_id AS mach_id,
			b.id AS bid, b.company AS cname,
			c.id AS cid, c.name AS mname, 
			d.id AS did, d.name AS tname 
			FROM Tickets a
			LEFT OUTER JOIN Customers b
			ON b.id = a.cust_id
			LEFT OUTER JOIN Machines c
			ON c.id = a.mach_id
			LEFT OUTER JOIN Technicians d
			ON d.id = a.tech
			WHERE a.id = :id";
            $stmt = $dbc->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);   
            $stmt->execute();
            $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e) {
                printf("Problem " . $e->getMessage());
        }
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){



			echo '
				<div class="list-item group">
				<span class="li-id">'.$row['id'].'</span>
				<span class="li-co-label">COMPANY</span>
				<span class="li-co">'.$row['cname'].'</span>
				<span class="li-mach-label">EQUIPMENT</span>
				<span class="li-mach">'.$row['mname'].'</span>
				<span class="li-date">'.$row['date'].'</span>
				<form action="" method="post">
				<input type="hidden" name="li-id" value="'.$row['id'].'">
				<input type="submit" value="" name="submit" class="li-trigger">
				</form>
				</div><!--list-item-group-->
			';
        }

	}//gentabs



}//class
