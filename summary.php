<?php

    require("../connect.php");

    class Summary {

        protected $id;
        protected $startDate;
        public $endDate;
        protected $company;
        protected $address;
        protected $city;
        protected $state;
        protected $zip;
        protected $phone;
        protected $contact;
        protected $machine;
        protected $diagnosis;
        protected $machineID;
        protected $newProgNum;
        protected $newProgID;

        private function noHTML($input){
            return htmlentities($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }


        function __construct($id){
            $this->id = $id;
            if($this->id){
                $this->connectDB($this->id);
            }
        }
        
        function connectDB($id){
            require("../connect.php");
            if($query = $dbc->query("SELECT
            t.id AS tid, t.date AS tdate, t.cust_id AS tcid, t.mach_id AS tmid, 
            t.issue AS issue, t.status AS status, t.tech AS tech,
            x.id AS xid, x.name AS xname, 
            c.id AS cid, c.company AS company, c.address AS address, c.city AS city,
            c.state AS state, c.zip AS zip, c.phone AS phone, c.contact AS contact,
            m.id AS mid, m.name AS mname 
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
                    $this->startDate =  $row['tdate'];
                    $this->company = $row['company'];
                    $this->address = $row['address'];
                    $this->city = $row['city'];
                    $this->state = $row['state'];
                    $this->zip = $row['zip'];
                    $this->phone = $row['phone'];
                    $this->contact = $row['contact'];
                    $this->machine = $row['mname'];
                    $this->diagnosis =  $row['issue'];
                    $this->machineID = $row['mid'];
                }
            }else{
                echo "failed to connect.";
            }
        }

        public function getProgress($id){
                require("../connect.php");
                if($query = $dbc->query("SELECT 
                    p.pid AS pid, p.t_id AS ptid, p.tech_id AS techid, p.date AS date, p.summary AS summary, p.hours AS hours,
                    t.id AS tid, t.name AS name
                    FROM Progress p
                    LEFT OUTER JOIN Technicians t
                    ON p.tech_id = t.id
                    WHERE p.t_id = $id
                    ORDER BY date")){

                    if ($query->rowCount() > 0){
                        while($row = $query->fetch(PDO::FETCH_ASSOC)){

                            $date = strtotime( $row['date'] );
                            $date = date( 'm-d-y', $date );

                            /* If Progess Block is open for editing */
                            if(($_SESSION['prog_editing'] == 1) && ($_SESSION['prog_edit_id'] == $row['pid'])){

                                if($_SESSION['emissing']){
                                    echo '<div class="prog-missing">';
                                    foreach ($_SESSION['emissing'] as $missingItem) {
                                        echo $missingItem . '<br>';
                                    }
                                    echo '</div>';
                                }

                                if($_SESSION['ematch']){
                                    echo '<div class="prog-match">';
                                    foreach ($_SESSION['ematch'] as $matchItem) {
                                        echo $matchItem . '<br>';
                                    }
                                    echo '</div>';
                                }
/* Edit Propgress */
                                echo '<div class="dd-progress group" id="'.$this->noHTML($row['pid']).'">
                                        <form action="actions.php" method="POST">
                                            <input type="text" name="edit_prog_date" class="add-prog-date" id="datepicker-8" placeholder="Date" value="'.$this->noHTML($_SESSION['edit_date']).'">
                                            <textarea name="edit_prog_summary" class="add-prog-text" rows="6" cols="80" >'.$this->noHTML($_SESSION['edit_summary']).'</textarea>
                                                <span>HRS: </span><input type="text" name="edit_prog_hrs" class="add-prog-hrs" size="4" placeholder="HRS." value="'.$this->noHTML($_SESSION['edit_hours']).'">
                                            <input type="hidden" name="prog_id" value="'.$this->noHTML($_SESSION['prog_edit_id']).'">
                                            <input type="hidden" name="ticket_id" value="'.$this->noHTML($row['ptid']).'">
                                
                                        <input type="submit" name="saveEditProgSubmit" class="update-progress" value="SAVE CHANGES">
                                        </form>
                                        <form action="actions.php" method="POST">
                                            <input type="hidden" name="ticket_id" value="'.$this->noHTML($row['ptid']).'">
                                            <input type="hidden" name="prog_id" value="'.$this->noHTML($row['pid']).'">
                                            <input type="submit" name="cancelEdit" class="cancel-edit" value="CANCEL">
                                        </form>';
                                echo      $this->getParts($row['pid']);

                                echo    $this->addParts($this->machineID, $row['pid']);
                                echo      '</div>';
                                
                               
                                
                                unset($_SESSION['prog_editing']);
                            }else{

                                echo '<div class="dd-progress group" id="'.$this->noHTML($row['pid']).'">
                                        <div class="dd-progress-header">

                                            <span class="prog-date">'.$this->noHTML($date).'</span>
                                            <span class="prog-tech">'.$this->noHTML($row['name']).'</span>
                                        </div>
                                        <div class="dd-progress-body group">
                                            <p class="prog-summary">'.$this->noHTML($row['summary']).'</p>
                                            <p class="prog-time">Time Spent: <strong>'.$this->noHTML($row['hours']);
                                            echo $row['hours']>1 ? ' hours.' : ' hour.';
                                            echo '</strong></p>

                                            <form action="actions.php" method="POST">
                                                <input type="hidden" name="pid" value="'.$this->noHTML($row['pid']).'">
                                                <input type="submit" name="editProgSubmit" value="EDIT" class="edit-prog-submit">
                                            </form>

                                        </div><!--dd-progress-body-->

                                        <div class="dd-progress-parts group">';
                                        echo $this->getParts($row['pid']);
                                        echo $this->addParts($this->machineID, $row['pid']);
                                      echo '</div><!--dd-progress-parts-->';
                                    
                                    echo '<div class="dd-progress-footer">
                                    <form action="actions.php" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this work unit?\');">
                                        <input type="hidden" name="pid" value="'.$this->noHTML($row['pid']).'">
                                        <input type="hidden" name="tid" value="'.substr($row['pid'], 1, strpos($row['pid'], 'p') - 1).'">
                                        <input type="submit" name="deleteProgSubmit" value="DELETE" class="delete-prog-submit">
                                    </form>
                                    </div><!--dd-progress-footer-->
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






        protected function getTechs(){
            require("../connect.php");
            if($query = $dbc->query("SELECT * FROM Technicians")){
                echo'<select name="add_prog_tech" class="add-prog-tech">';
                    while($row = $query->fetch(PDO::FETCH_ASSOC)){
                        echo '<option value="'.$this->noHTML($row['id']).'">'.$this->noHTML($row['name']).'</option>';
                    }
                echo '<select>';
            }
        }

        protected function getParts($id){
            require("../connect.php");
            if($query = $dbc->query("SELECT 
            a.id AS ppid, a.prog_id AS progid, a.part_id AS partid, a.qty AS qty, a.tid AS tid,
            b.id AS pid, b.name AS name, b.price AS price
            FROM progparts a
            LEFT OUTER JOIN Parts b
            ON a.part_id = b.id WHERE a.prog_id='$id'
            ")){
                echo '<div class="prog-part-list">';
                if($query->rowCount() < 1){echo 'No parts added.';}else{echo 'Parts:';}
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    echo '<div class="part-block group">';
                    echo '<form action="actions.php" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this part?\');">';    
                    echo '<input type="hidden" name="id" value="'.$this->noHTML($row['ppid']).'">';
                    echo '<input type="hidden" name="tid" value="'.$this->noHTML($row['tid']).'">';
                    echo '<input type="hidden" name="pid" value="'.$this->noHTML($row['progid']).'">';
                    echo '<p class="part-block-qty">'.$this->noHTML($row['qty']).'</p>';
                    echo '<p class="part-block-text">'.$this->noHTML($row['name']).'</p>';
                    echo '<input type="submit" name="deletePartSubmit"  class="part-block-submit" value="">';
                    echo '</form></div>';
                }
                echo '</div>';
            }else{
                echo "No parts";
            }
        }

        protected function addParts($mid, $prog){
            echo '<div class="add-parts-container group">';
            echo '<button class="add-parts-button">ADD A PART</button>';
            echo '<div class="add-parts-form group">';
            echo '<form action="actions.php" method="POST">';
            echo '<input type="text" name="qty" class="parts-qty" placeholder="qty">';
            require("../connect.php");
            if($machineParts = $dbc->query("SELECT
                a.id AS pid, a.name AS name,
                b.part_id AS part_id, b.mach_id AS mid
                FROM Parts a
                LEFT OUTER JOIN partsmach b
                ON a.id = b.part_id
                WHERE b.mach_id = '$mid'
            ")){
                echo '<select name="part" class="part-input">';
                while($row = $machineParts->fetch(PDO::FETCH_ASSOC)){
                    echo '<option value="'.$this->noHTML($row['pid']).'">' . $this->noHTML($row['name']) . '</option>';
                }
                echo '</select>';
            }
            echo '<input type="hidden" name="prog" value="'.$this->noHTML($prog).'">';
            echo '<input type="submit" name="addPartSubmit" value="ADD" class="add-part-submit">';
            echo '</form></div>';

                if($_SESSION['part_missing']){
                    echo '<div class="part-missing">';
                    foreach ($_SESSION['part_missing'] as $partMissing) {
                        echo $partMissing . '<br>';
                    }
                    echo '</div>';
                }

            echo '</div>';
        }

        protected function addPartsToProg($mid, $prog){

            require("../connect.php");

            if($machineParts = $dbc->query("SELECT
                a.id AS pid, a.name AS name,
                b.part_id AS part_id, b.mach_id AS mid
                FROM Parts a
                LEFT OUTER JOIN partsmach b
                ON a.id = b.part_id
                WHERE b.mach_id = '$mid'
            ")){
            echo '<button id="add-parts-reveal" type="button" class="btn btn-secondary btn-sm">Add Parts</button>';
            echo '<div id="add-parts-to-progress">';
            echo '<p id="add-parts-to-prog-info">Enter the quantity of any parts used in this work unit:</p>';
                if($machineParts->rowCount() > 0){
                    $n = 1;
                    echo '<input type="hidden" name="hasParts" value="true">';
                    echo '<input type="hidden" name="numParts" value="'.$machineParts->rowCount().'">';
                    while($row = $machineParts->fetch(PDO::FETCH_ASSOC)){
                        echo '<div class="part-row">';
                        echo '<input type="text" name="p'.$n.'_qty" class="qty-part">';
                        echo '<span>'.$this->noHTML($row['name']).'</span>';
                        echo '<input type="hidden" name="p'.$n.'_partID" value="'.$this->noHTML($row['pid']).'">';
                        echo '</div>';
                        $n++;
                    }
                }else{
                    echo 'No parts available for this machine.';
                    echo '<input type="hidden" name="hasParts" value="false">';
                }

            }
            echo '</div>';
        }


        public function createForm($id){

            require("../connect.php");
            if($checkProgs = $dbc->query("SELECT 
                a.pid AS pid, a.t_id AS t_id,
                b.id AS tid, b.mach_id AS mid 
                FROM Progress a
                LEFT OUTER JOIN Tickets b
                ON a.t_id = b.id 
                WHERE a.t_id='$id'
                ")){
                if($checkProgs->rowCount() < 1){
                    $this->progNum = 1;
                }else{
                    $c=0;
                    while($row = $checkProgs->fetch(PDO::FETCH_ASSOC)){
                        if(substr($row['pid'], strpos($row['pid'], 'p')+1) > $c){
                            $c = substr($row['pid'], strpos($row['pid'], 'p')+1);
                        }
                    }
                    $this->newProgNum = $c + 1;
                   
                }
                $this->newProgID = 't' . $this->id . 'p' . $this->newProgNum;
            }else{
                echo "Progress not initialized";
            }



            echo '
            <div class="dd-progress-add group" id="'.$this->newProgID.'">';  /*-----------------------------------------------------*/

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

            if($_SESSION['part_match']){
                echo '<div class="prog-match">';
                foreach ($_SESSION['part_match'] as $partMatchItem) {
                    echo $partMatchItem . '<br>';
                }
                echo '</div>';
            }


            echo  '<form action="actions.php" method="post">
                       <div class="dd-progress-add-header">
                           <input type="text" name="add_prog_date" class="add-prog-date" id="datepicker-8" placeholder="Date" value="'.$this->noHTML($_SESSION['add-prog-date']).'">';
            echo $this->getTechs();        
            echo        '</div>
                <textarea name="add_prog_text" class="add-prog-text" rows="6" cols="80" placeholder="Summary">'.$this->noHTML($_SESSION['add_prog_text']).'</textarea>

                <div class="add-time">
                    <input type="text" name="add_prog_hrs" class="add-prog-hrs" size="4" placeholder="HRS." value="'.$this->noHTML($_SESSION['add_prog_hrs']).'">
                </div>

                <input type="hidden" name="ticket_id" value="'.$this->noHTML($this->id).'">
                <input type="hidden" name="prog_id" value="'.$this->noHTML($this->newProgID).'">';

            //echo '<button id="add-parts-reveal" type="button" class="btn btn-secondary btn-sm">Add Parts</button>';
            //echo '<div id="add-parts-to-progress">';    
            echo $this->addPartsToProg($this->machineID, $this->newProgID);
            //echo '</div>';

            echo '<br>
                <input type="submit" name="addProgSubmit" id="add-prog-submit" value="Save Progress" type="button" class="btn btn-info">
                </form>';

            // if($_SESSION['part_missing']){
            //     echo '<div class="prog-missing">';
            //     foreach ($_SESSION['part_missing'] as $partMissingItem) {
            //         echo $partMissingItem . '<br>';
            //     }
            //     echo '</div>';
            // }

            echo '</div>';
        }

        public function createReport($progOpen){

            $date = strtotime( $this->startDate );
            $date = date( 'm-d-y', $date );

            echo '  <div class="dd-info">
                    <h2>Repair Report</h2>
                    <span class="dd-id">ID # '.$this->noHTML($this->id).'</span>
                    <span class="dd-date">Initiated on: '.$this->noHTML($date).'</span>
                    <span class="dd-cust-label">CUSTOMER</span>
                    <span class="dd-cust">'.$this->noHTML($this->company).'</span>
                    <span class="dd-cust-address">'.$this->noHTML($this->address).'<br>'.$this->noHTML($this->city).', '.$this->noHTML($this->state).' '.$this->noHTML($this->zip).'</span>
                    <span class="dd-cust-phone">'.$this->noHTML($this->phone).'</span>
                    <span class="dd-cust-contact">Contact: '.$this->noHTML($this->contact).'</span>
                    <span class="dd-mach-label">MACHINE</span>
                    <span class="dd-mach">'.$this->noHTML($this->machine).'</span>
                    <span class="dd-diagnosis-label">DIAGNOSIS</span>
                    <span class="dd-diagnosis">'.$this->noHTML($this->diagnosis).'</span>';
            echo    '</div><!--dd-info-->';

                    if($this->id){
                        echo $this->getProgress($this->id);
                    }
           
                    if($progOpen == 1){
                        echo    '<div id="add-progress-form-open">';
                    }else{
                        echo    '<button id="add-progress" class="btn btn-secondary btn-sm">Add Progress</button>
                                 <div id="add-progress-form">';
                    }
            echo    $this->createForm($this->id);      
            echo    '</div>';
            echo    '<div class="report-footer group">
                        <form action="actions.php" method="post">
                            <input type="hidden" name="close_id" value="'.$this->noHTML($this->id).'">
                            <input type="submit" name="close_submit" id="close-submit" value="Close Ticket" type="button" class="btn btn-outline-secondary">
                        </form>
                    </div>';
        }



        protected function closedParts($id){
            require("../connect.php");
            if($query = $dbc->query("SELECT 
            a.id AS ppid, a.prog_id AS progid, a.part_id AS partid, a.qty AS qty, a.tid AS tid,
            b.id AS pid, b.name AS name, b.price AS price
            FROM progparts a
            LEFT OUTER JOIN Parts b
            ON a.part_id = b.id WHERE a.prog_id='$id'
            ")){
                echo '<div class="prog-part-list">';
                if($query->rowCount() < 1){echo 'No parts added.';}else{echo 'Parts:';}
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    echo '<div class="closed-part-block group">';
                    echo '<span class="part-block-qty">'.$this->noHTML($row['qty']).'</span>';
                    echo '<span class="part-block-text">'.$this->noHTML($row['name']).'</span>';
                    echo '</div>';
                }
                echo '</div>';
            }else{
                echo "No parts";
            }
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

                    $date = strtotime( $row['prog_date'] );
                    $date = date( 'm-d-y', $date );

                    echo '<div class="dd-progress group">
                        <div class="dd-progress-header">

                            <span class="prog-date">'.$this->noHTML($date).'</span>
                            <span class="prog-tech">'.$this->noHTML($row['tech_name']).'</span>
                        </div>
                        <div class="dd-progress-body group">
                            <p class="prog-summary">'.$this->noHTML($row['summary']).'</p>
                            <p class="prog-time">Time Spent: <strong>'.$this->noHTML($row['hours']).' hours </strong></p>

                        </div><!--dd-progress-body-->

                        <div class="prog-parts group">';
                        echo $this->closedParts($row['prog_id']);
                      echo '</div><!--prog-parts-->';
                      echo '</div>';

                }
            }
            catch (PDOException $e) {
                printf("Problem " . $e->getMessage());
            }


        }


        public function closedReport(){

            $date = strtotime( $this->startDate );
            $date = date( 'm-d-Y', $date );

            echo '  <div class="dd-info">
                    <h2>Repair Report</h2>
                    <span class="dd-id">ID # '.$this->noHTML($this->id).'</span>
                    <span class="dd-date">Initiated on: '.$this->noHTML($date).'</span>
                    <span class="dd-cust-label">CUSTOMER</span>
                    <span class="dd-cust">'.$this->noHTML($this->company).'</span>
                    <span class="dd-cust-address">'.$this->noHTML($this->address).'<br>'.$this->noHTML($this->city).', '.$this->noHTML($this->state).' '.$this->noHTML($this->zip).'</span>
                    <span class="dd-cust-phone">'.$this->noHTML($this->phone).'</span>
                    <span class="dd-cust-contact">Contact: '.$this->noHTML($this->contact).'</span>
                    <span class="dd-mach-label">MACHINE</span>
                    <span class="dd-mach">'.$this->noHTML($this->machine).'</span>
                    <span class="dd-diagnosis-label">DIAGNOSIS</span>
                    <span class="dd-diagnosis">'.$this->noHTML($this->diagnosis).'</span>';
            echo    '</div><!--dd-info-->';

                    if($this->id){
                        echo $this->closedProgress($this->id);
                    }
            echo    '<div id="gen-inv">
                        <form action="reportPDF.php" method="POST" target="_blank">
                            <input type="hidden" name="tid" value="'.$this->noHTML($this->id).'">
                            <button id="gen-inv-submit" name="genInvSubmit" type="submit" class="btn btn-secondary btn-sm">Generate Invoice</button>
                        </form>
                    </div>';


        }



    }




