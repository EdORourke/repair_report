<?php

    class RepairReport {


        protected $id;
        public $date;
        protected $company;
        public $address;
        public $city;
        public $state;
        public $zip;
        public $phone;
        public $contact;
        public $machine;
        public $diagnosis;
        public $mid;

        function __construct($id){

            $this->id = $id;

            if($this->id){
                $this->connectDB($this->id);
            }
            $_SESSION['idpassed'] = $id;
            $_SESSION['idinclass'] = $this->id;
        }
        
        function connectDB($id){

            require("../connect.php");

            if($query = $dbc->query("SELECT
            t.id AS tid, t.date AS tdate, t.cust_id AS tcid, t.mach_id AS tmid, 
            t.issue AS issue, t.status AS status, t.tech AS tech,
            x.id AS xid, x.name AS xname, 
            c.id AS cid, c.company AS company, c.address AS address, c.city AS city,
            c.state AS state, c.zip AS zip, c.phone AS phone, c.contact AS contact,
            m.id AS mid, m.name AS mname, m.parts AS mparts 
            FROM Tickets t
            LEFT OUTER JOIN Technicians x
            ON t.tech = x.id
            LEFT OUTER JOIN Customers c
            ON t.cust_id = c.id
            LEFT OUTER JOIN Machines m
            ON t.mach_id = m.id
            WHERE t.id = $id")){

                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $this->id =  $row['tid'];
                    $this->date =  $row['tdate'];
                    $this->company = $row['company'];
                    $this->address = $row['address'];
                    $this->city = $row['city'];
                    $this->state = $row['state'];
                    $this->zip = $row['zip'];
                    $this->phone = $row['phone'];
                    $this->contact = $row['contact'];
                    $this->machine = $row['mname'];
                    $this->diagnosis =  $row['issue'];
                    $this->mid = $row['mid'];
                }
            }else{
                echo "query failed";
            }
        }


        public function getParts($id){
            require("../connect.php");

            if($query = $dbc->query("SELECT
                a.id AS ppid, a.prog_id AS prog_id, a.part_id AS part_id, a.qty AS qty,
                b.id AS id, b.name AS name
                FROM progparts a
                LEFT OUTER JOIN Parts b
                ON a.part_id = b.id
                WHERE a.prog_id = '$id'
            ")){
                if($query->rowCount() > 0){
                    echo 'Parts Used:<br>';
                    while($row = $query->fetch(PDO::FETCH_ASSOC)){
                        echo '<div class="part-block group">
                        <form action="actions.php" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this part?\');">
                            <span><strong>'.$row['qty']. '</strong> ' . $row['name'] . '</span>
                            <input type="hidden" name="pid" value="'.$row['ppid'].'">
                            <input type="hidden" name="tid" value="'.substr($id, 1, strpos($id, 'p') - 1).'">
                            <input type="submit" name="deleteThisPart" class="part-block-submit" value="">
                            </form></div>';
                    }
                }else{
                    echo 'No parts used in this work unit.';
                }

            }else{
                echo 'parts query failed';
            }

        }

        public function getProgress($id){

                require("../connect.php");
                //include "progress.php";

                if($query = $dbc->query("SELECT 
                    p.pid AS pid, p.t_id AS ptid, p.tech_id AS techid, p.date AS date, p.summary AS summary, p.hours AS hours,
                    t.id AS tid, t.name AS name
                    FROM Progress p
                    LEFT OUTER JOIN Technicians t
                    ON p.tech_id = t.id
                    WHERE p.t_id = $id")){

                    if ($query->rowCount() > 0){
                        while($row = $query->fetch(PDO::FETCH_ASSOC)){

                            $date = strtotime( $row['date'] );
                            $date = date( 'm-d-y', $date );


                            if(($_SESSION['prog_editing'] == 1) && ($_SESSION['prog_edit_id'] == $row['pid'])){

                                if($_SESSION['missing']){
                                    echo '<div class="prog-missing">';
                                    foreach ($_SESSION['missing'] as $missingItem) {
                                        echo $missingItem . '<br>';
                                    }
                                    echo '</div>';
                                }

                                if($_SESSION['match']){
                                    echo '<div class="prog-match">';
                                    foreach ($_SESSION['match'] as $matchItem) {
                                        echo $matchItem . '<br>';
                                    }
                                    echo '</div>';
                                }
/* Edit Propgress */
                                echo '<div class="dd-progress group" id="'.$row['pid'].'">
                                        <form action="actions.php" method="POST">
                                            <input type="text" name="edit_prog_date" class="add-prog-date" id="datepicker-8" placeholder="Date" value="'.$_SESSION['edit_date'].'">
                                            <textarea name="edit_prog_summary" class="add-prog-text" rows="6" cols="80" >'.$_SESSION['edit_summary'].'</textarea>
                                                <span>HRS: </span><input type="text" name="edit_prog_hrs" class="add-prog-hrs" size="4" placeholder="HRS." value="'.$_SESSION['edit_hours'].'">
                                            <input type="hidden" name="prog_id" value="'.$_SESSION['prog_edit_id'].'">
                                            <input type="hidden" name="ticket_id" value="'.$row['ptid'].'">
                                
                                        <input type="submit" name="saveEditProgSubmit" class="update-progress" value="SAVE CHANGES">
                                        </form>
                                        <form action="actions.php" method="POST">
                                            <input type="hidden" name="ticket_id" value="'.$row['ptid'].'">
                                            <input type="submit" name="cancelEdit" class="cancel-edit" value="CANCEL">
                                        </form>';
                                echo      $this->getParts($row['pid']);
                                echo      '</div>';



                                
                                // echo '<script>function onClickParts(){
                                //     document.getElementById("add-parts-form").style.display = "block";
                                // }</script>';
                                // echo '<button id="add-parts-button" onclick="onClickParts()">Add a part</button>';
                                // echo '<div id="add-parts-form">';
                                // include "progress.php";
                                // $a = new Progress;
                                // echo $a->addParts('4', $row['pid']);
                                // echo '</div>';









                                
                                
                                unset($_SESSION['prog_editing']);
                            }else{

                                echo '<div class="dd-progress group" id="'.$row['pid'].'">
                                        <div class="dd-progress-header">

                                            <span class="prog-date">'.$date.'</span>
                                            <span class="prog-tech">'.$row['name'].'</span>
                                        </div>
                                        <div class="dd-progress-body group">
                                            <p class="prog-summary">'.$row['summary'].'</p>
                                            <p class="prog-time">Time Spent: '.$row['hours'].' hours </p>

                                            <form action="actions.php" method="POST">
                                                <input type="hidden" name="pid" value="'.$row['pid'].'">
                                                <input type="submit" name="editProgSubmit" value="EDIT" class="edit-prog-submit">
                                            </form>

                                        </div><!--dd-progress-body-->

                                        <div class="prog-parts group">';
                                        echo $this->getParts($row['pid']);
                                      echo '</div><!--prog-parts-->
                                    

                                    

                                    <form action="actions.php" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this work unit?\');">
                                        <input type="hidden" name="pid" value="'.$row['pid'].'">
                                        <input type="hidden" name="tid" value="'.substr($row['pid'], 1, strpos($row['pid'], 'p') - 1).'">
                                        <input type="submit" name="deleteProgSubmit" value="DELETE" class="delete-prog-submit">
                                    </form>
                                </div><!--dd-progress-->';
                            }
                        }
                    }else{
                        echo '<div class="dd-progress">
                                    <div class="dd-progress-header">
                                        <span>Work has not begun on this unit</span>
                                    </div>
                                </div>';
                    }

                }else{
                    echo "getProgress failed";
                }

                
        }


        public function createReport($progOpen='abc'){

            $_SESSION['progtest'] = $progOpen;
            $_SESSION['idtest'] = $this->id;

            $date = strtotime( $this->date );
            $date = date( 'm-d-y', $date );

            echo '  <div class="dd-info">
                    <h2>Repair Report</h2>
                    <span class="dd-id">ID # '.$this->id.'</span>
                    <span class="dd-date">Initiated on: '.$date.'</span>
                    <span class="dd-cust-label">CUSTOMER</span>
                    <span class="dd-cust">'.$this->company.'</span>
                    <span class="dd-cust-address">'.$this->address.'<br>'.$this->city.', '.$this->state.' '.$this->zip.'</span>
                    <span class="dd-cust-phone">'.$this->phone.'</span>
                    <span class="dd-cust-contact">Contact: '.$this->contact.'</span>
                    <span class="dd-mach-label">MACHINE</span>
                    <span class="dd-mach">'.$this->machine.'</span>
                    <span class="dd-diagnosis-label">DIAGNOSIS</span>
                    <span class="dd-diagnosis">'.$this->diagnosis.'</span>';
            echo    '</div><!--dd-info-->';

                    if($this->id){
                        echo $this->getProgress($this->id);
                    }
           
            if($progOpen == 1){

                  
                echo    '<div id="add-progress-form-open">';
                         
                         if($this->id){
                            include "progress.php";
                            $prog = new Progress($this->id);
                            $prog->machine = $this->mid;
                            $prog->createForm();
                        }
                         
                echo     '</div>';


            }else{

                echo    '<button id="add-progress">Add Progress</button>
                         <div id="add-progress-form">';
                         
                         if($this->id){
                            include "progress.php";
                            $prog = new Progress($this->id);
                            $prog->machine = $this->mid;
                            $prog->createForm();
                        }
                         
                echo     '</div>';
            }        
            echo    '<div class="report-footer group">
                        <form action="actions.php" method="post">
                            <input type="hidden" name="close_id" value="'.$this->id.'">
                            <input type="submit" name="close_submit" id="close-submit" value="Close Ticket">
                        </form>
                    </div>';
        }



        private function closedProgress($id){

        require("../connect.php");
            try {
                $sql = "SELECT
                        a.pid AS prog_id, a.t_id AS ticket_id, a.tech_id AS prog_tech_id, a.date AS prog_date, a.summary AS summary, a.hours AS hours,
                        b.id AS pp_id, b.prog_id AS pp_prog_id, b.part_id AS pp_part_id, b.qty AS qty, b.tid AS pp_ticket_id,
                        c.id AS part_id, c.name AS part_name,
                        d.id AS tech_id, d.name AS tech_name
                        FROM Progress a
                        LEFT OUTER JOIN progparts b
                        ON b.prog_id = a.pid
                        LEFT OUTER JOIN Parts c
                        ON c.id = b.part_id
                        LEFT OUTER JOIN Technicians d
                        ON d.id = a.tech_id
                        WHERE a.t_id = :id
                ";
                $stmt = $dbc->prepare($sql);
                $stmt->execute([id => $id]);
                $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

                    $date = strtotime( $row['date'] );
                    $date = date( 'm-d-y', $date );

                    echo '<div class="dd-progress group">
                        <div class="dd-progress-header">

                            <span class="prog-date">'.$date.'</span>
                            <span class="prog-tech">'.$row['tech_name'].'</span>
                        </div>
                        <div class="dd-progress-body group">
                            <p class="prog-summary">'.$row['summary'].'</p>
                            <p class="prog-time">Time Spent: '.$row['hours'].' hours </p>

                        </div><!--dd-progress-body-->

                        <div class="prog-parts group">';
                        echo $this->getParts($row['pid']);
                      echo '</div><!--prog-parts-->';

                }
            }
            catch (PDOException $e) {
                printf("Problem " . $e->getMessage());
            }


        }


        public function closedReport(){

            $date = strtotime( $this->date );
            $date = date( 'm-d-y', $date );

            echo '  <div class="dd-info">
                    <h2>Repair Report</h2>
                    <span class="dd-id">ID # '.$this->id.'</span>
                    <span class="dd-date">Initiated on: '.$date.'</span>
                    <span class="dd-cust-label">CUSTOMER</span>
                    <span class="dd-cust">'.$this->company.'</span>
                    <span class="dd-cust-address">'.$this->address.'<br>'.$this->city.', '.$this->state.' '.$this->zip.'</span>
                    <span class="dd-cust-phone">'.$this->phone.'</span>
                    <span class="dd-cust-contact">Contact: '.$this->contact.'</span>
                    <span class="dd-mach-label">MACHINE</span>
                    <span class="dd-mach">'.$this->machine.'</span>
                    <span class="dd-diagnosis-label">DIAGNOSIS</span>
                    <span class="dd-diagnosis">'.$this->diagnosis.'</span>';
            echo    '</div><!--dd-info-->';

                    if($this->id){
                        echo $this->closedProgress($this->id);
                    }


        }









    }




