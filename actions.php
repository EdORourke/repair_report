<?php session_start();
function goBack($loc=""){header("Location: main.php#");$_SESSION['scroll']=$loc;} ?>
<?php if(isset($_POST['close_submit'])){
        $id = $_POST['close_id'];
        $closed = (INT)1;
        require_once("../connect.php");
        try {
            $sql = "UPDATE Tickets SET status=:closed WHERE id=:id";
            $stmt = $dbc->prepare($sql);
            $stmt->execute(['closed' => $closed, 'id' => $id]);
            $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            header('Location: main.php');
        }
        catch (PDOException $e) {
            printf("Problem " . $e->getMessage());
        }
    }
    /* Add a part */

    if(isset($_POST['addPartSubmit'])){


        unset($_SESSION['part_missing']);
        unset($_SESSION['part_match']);

        if(!isset($_POST['prog']) || $_POST['prog'] === ''){
            $_SESSION['part_missing'][] = '<span>The Progress ID</span> is missing.';
        }else{
            if(!preg_match('/^[t][0-9]{0,12}[p][0-9]{0,12}$/', $_POST['prog'])){
                $_SESSION['part_match'][] = '<span>The Progress ID</span> is not properly formatted.';
            }else{
                $prog = $_POST['prog'];
            }
        }
        if(!isset($_POST['part']) || $_POST['part'] === ''){
            $_SESSION['part_missing'][] = '<span>Part</span> is missing.';
        }else{
            if(!preg_match('/^[0-9]{0,10}$/', $_POST['part'])){
                $_SESSION['part_match'][] = '<span>Part</span> is not properly formatted.';
            }else{
                $part = $_POST['part'];
            }
        }
        if(!isset($_POST['qty']) || $_POST['qty'] === '' || $_POST['qty'] == '0'){
            $_SESSION['part_missing'][] = '<span>QTY</span> must be greater than zero.';
        }else{
            if(!preg_match('/^[0-9]{1,2}$/', $_POST['qty'])){
                $_SESSION['part_match'][] = '<span>QTY</span> is not properly formatted.';
            }else{
                $qty = $_POST['qty'];
            }
        }
        $tid = substr($prog, 1, strpos($prog, 'p') - 1);


        if((!empty($_SESSION['part_missing'])) || (!empty($_SESSION['part_match']))){

            $_SESSION['partProblem'] = 1;
            $_SESSION['ticketId'] = $tid;
            $_SESSION['progOpen'] = 1;
            goBack($prog);
        }else{

            require_once("../connect.php");
            try {
                $sql = "INSERT INTO progparts (prog_id, part_id, qty, tid) VALUES (:prog_id, :part_id, :qty, :tid)";
                $stmt = $dbc->prepare($sql);
                $stmt->execute(['prog_id' => $prog, 'part_id' => $part, 'qty' => $qty, 'tid' => $tid]);
                $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $_SESSION['progOpen'] = 1;
                $_SESSION['ticketId'] = $tid;
                goBack($prog);
            }
            catch (PDOException $e) {
                printf("Problem " . $e->getMessage());
            }
        }
        
    }


    if(isset($_POST['deletePartSubmit'])){

        if(!preg_match('/^[0-9]{0,10}$/', $_POST['id'])){
            die();
        }else{
            $id = intval($_POST['id']);
        }
        if(!preg_match('/^[0-9]{0,10}$/', $_POST['tid'])){
            die();
        }else{
            $tid = intval($_POST['tid']);
        }
        if(!preg_match('/^[t][0-9]{0,12}[p][0-9]{0,12}$/', $_POST['pid'])){
            die();
        }else{
            $pid = $_POST['pid'];
        }
        require_once("../connect.php");
            try {
                $sql = "DELETE FROM progparts WHERE id =  :id";
                $stmt = $dbc->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);   
                $stmt->execute();
                $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $_SESSION['progOpen'] = 1;
                $_SESSION['ticketId'] = $tid;
                goBack($pid);
            }
            catch (PDOException $e) {
                printf("Problem " . $e->getMessage());
            }
    }


    /* Add Progress */
    if(isset($_POST['addProgSubmit'])){

        unset($_SESSION['missing']);
        unset($_SESSION['match']);


        if(!isset($_POST['ticket_id']) || $_POST['ticket_id'] === ''){
            $_SESSION['missing'][] = '<span>The Ticket ID</span> is missing.';
        }else{
            if(!preg_match('/^[0-9]{0,12}$/', $_POST['ticket_id'])){
                $_SESSION['match'][] = '<span>The Ticket ID</span> is not properly formatted.';
            }else{
                $ticket = $_POST['ticket_id'];
                $_SESSION['ticket'] = htmlentities($ticket, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        if(!isset($_POST['prog_id']) || $_POST['prog_id'] === ''){
            $_SESSION['missing'][] = '<span>The Progress ID</span> is missing.';
        }else{
            if(!preg_match('/^[t][0-9]{0,12}[p][0-9]{0,12}$/', $_POST['prog_id'])){
                $_SESSION['match'][] = '<span>The Progress ID</span> is not properly formatted.';
            }else{
                $prog_id = $_POST['prog_id'];
                $_SESSION['prog_id'] = htmlentities($prog_id, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        if(!isset($_POST['hasParts']) || $_POST['hasParts'] === ''){
            $_SESSION['missing'][] = '<span>Has Parts</span> is not indicated.';
        }else{
            if(!preg_match('/^(true|false)$/', $_POST['hasParts'])){
                die();
            }else{
                $hasParts = $_POST['hasParts'];
            }
        }
        if(!isset($_POST['numParts']) || $_POST['numParts'] === ''){
            
        }else{
            if(!preg_match('/^[0-9]{1,3}$/', $_POST['numParts'])){
                die();
            }else{
                $numParts = $_POST['numParts'];
            }
        }

        if(!isset($_POST['add_prog_date']) || $_POST['add_prog_date'] === ''){
            $_SESSION['missing'][] = '<span>Date</span> is missing.';
        }else{
            if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $_POST['add_prog_date'])){
                $_SESSION['match'][] = '<span>The Date</span> is not properly formatted.';
            }else{
                $add_prog_date = $_POST['add_prog_date'];
                
            }
        }

        if(!isset($_POST['add_prog_tech']) || $_POST['add_prog_tech'] === ''){
            $_SESSION['missing'][] = '<span>Tech</span> is missing.';
        }else{
            if(!preg_match('/^[0-9]{1,4}$/', $_POST['add_prog_tech'])){
                $_SESSION['match'][] = '<span>The Tech</span> is not properly formatted.';
            }else{
                $add_prog_tech = $_POST['add_prog_tech'];
                
            }
        }

        if(!isset($_POST['add_prog_text']) || $_POST['add_prog_text'] === ''){
            $_SESSION['missing'][] = '<span>Summary</span> is missing.';
        }else{
            $add_prog_text = $_POST['add_prog_text'];
        }


        if(!isset($_POST['add_prog_hrs']) || $_POST['add_prog_hrs'] === ''){
            $_SESSION['missing'][] = '<span>Hours</span> is missing.';
        }else{
            if(!preg_match('/^[0-9]{1,2}$/', $_POST['add_prog_hrs'])){
                $_SESSION['match'][] = '<span>Hours</span> is not properly formatted.';
            }else{

                if($_POST['add_prog_hrs'] > 24){
                    $_SESSION['match'][] = '<span>Hours</span> is too high.';
                }

                $add_prog_hrs = $_POST['add_prog_hrs'];
                
            }
        }

        if((!empty($_SESSION['missing'])) || (!empty($_SESSION['match']))){

            $_SESSION['ticketId'] = $ticket;
            $_SESSION['add_prog_date'] = $add_prog_date;
            $_SESSION['add_prog_tech'] = $add_prog_tech;
            $_SESSION['add_prog_text'] = $add_prog_text;
            $_SESSION['add_prog_hrs'] = $add_prog_hrs;
            $_SESSION['progOpen'] = 1;
            goBack($prog_id);
        }else{


            require("../connect.php");
            try {

                $sql = "INSERT INTO Progress (pid, t_id, tech_id, date, summary, hours) VALUES (:pid, :t_id, :tech_id, :date, :summary, :hours) ";
                $stmt = $dbc->prepare($sql);
                $stmt->execute(['pid' => $prog_id, 't_id' => $ticket, 'tech_id' => $add_prog_tech, 'date' => $add_prog_date, 'summary' => $add_prog_text, 'hours' => $add_prog_hrs]);
                $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $_SESSION['ticketId'] = $ticket;
                unset($_SESSION['add_prog_date']);
                unset($_SESSION['add_prog_tech']);
                unset($_SESSION['add_prog_text']);
                unset($_SESSION['add_prog_hrs']);
                $_SESSION['progAdded'] = 1;

                if($hasParts == 'true'){

                    for($i=1;$i<=$numParts;$i++){
                        if(isset($_POST['p'.$i.'_qty'])&&($_POST['p'.$i.'_qty'] > 0)){

                            if(!preg_match('/^[0-9]{1,3}$/', $_POST['p'.$i.'_qty'])){
                                die();
                            }else{
                                $qty = $_POST['p'.$i.'_qty'];
                            }

                            if(!preg_match('/^[0-9]{0,10}$/', $_POST['p'.$i.'_partID'])){
                                die();
                            }else{
                                $partID = $_POST['p'.$i.'_partID'];
                            }

                            $sql = "INSERT INTO progparts (prog_id, part_id, qty, tid) VALUES (:prog_id, :part_id, :qty, :tid)";
                            $stmt = $dbc->prepare($sql);
                            $stmt->execute(['prog_id' => $prog_id, 'part_id' => $partID, 'qty' => $qty, 'tid' => $ticket]);
                            $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        }else{
                            
                        }
                    }
                    goBack($prog_id);


                }else{
                    goBack($prog_id);
                }


                
            }
            catch (PDOException $e) {
                printf("Problem " . $e->getMessage());
            }
            
        }
       
    }


    if(isset($_POST['editProgSubmit'])){

        if(!preg_match('/^[t][0-9]{0,12}[p][0-9]{0,12}$/', $_POST['pid'])){
            $pid = 'BAD';
        }else{
            $pid = $_POST['pid'];
            $tid = substr($pid,1,strpos($pid,'p')-1);
        }

        require_once("../connect.php");
        try{
            $sql = "SELECT pid, t_id, tech_id, date, summary, hours FROM Progress WHERE pid = :pid";
            $stmt = $dbc->prepare($sql);
            $stmt->execute(['pid' => $pid]);
            $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

                $_SESSION['edit_date'] = htmlentities($row['date'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $_SESSION['edit_hours'] = htmlentities($row['hours'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $_SESSION['edit_summary'] = htmlentities($row['summary'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                
            }
            $_SESSION['prog_editing'] = 1;
            $_SESSION['prog_edit_id'] = $pid;
            $_SESSION['progOpen'] = 1;
            $_SESSION['ticketId'] = $tid;
            goBack($pid);

        }
        catch (PDOException $e){
            printf("Problem " . $e->getMessage());
        }
        
    }
/*------------------------------------------------------------------------------------------------*/


    if(isset($_POST['saveEditProgSubmit'])){

        unset($_SESSION['emissing']);
        unset($_SESSION['ematch']);


        if(!isset($_POST['ticket_id']) || $_POST['ticket_id'] === ''){
            $_SESSION['emissing'][] = '<span>The Ticket ID</span> is missing.';
        }else{
            if(!preg_match('/^[0-9]{0,12}$/', $_POST['ticket_id'])){
                $_SESSION['ematch'][] = '<span>The Ticket ID</span> is not properly formatted.';
            }else{
                $edit_ticket_id = $_POST['ticket_id'];
                $_SESSION['edit_ticket'] = htmlentities($edit_ticket_id, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        if(!isset($_POST['prog_id']) || $_POST['prog_id'] === ''){
            $_SESSION['emissing'][] = '<span>The Progress ID</span> is missing.';
        }else{
            if(!preg_match('/^[t][0-9]{0,12}[p][0-9]{0,12}$/', $_POST['prog_id'])){
                $_SESSION['ematch'][] = '<span>The Progress ID</span> is not properly formatted.';
            }else{
                $edit_prog_id = $_POST['prog_id'];
                $_SESSION['prog_edit_id'] = htmlentities($edit_prog_id, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        if(!isset($_POST['edit_prog_date']) || $_POST['edit_prog_date'] === ''){
            $_SESSION['emissing'][] = '<span>Date</span> is missing.';
            $_SESSION['edit_date'] = '';
        }else{
            if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $_POST['edit_prog_date'])){
                $_SESSION['ematch'][] = '<span>The Date</span> is not properly formatted.';
            }else{
                $edit_prog_date = $_POST['edit_prog_date'];
                $_SESSION['edit_date'] = htmlentities($edit_prog_date, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        if(!isset($_POST['edit_prog_summary']) || $_POST['edit_prog_summary'] === ''){
            $_SESSION['emissing'][] = '<span>Summary</span> is missing.';
        }else{
            $edit_prog_summary = $_POST['edit_prog_summary'];
            $_SESSION['edit_summary'] = htmlentities($edit_prog_summary, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        if(!isset($_POST['edit_prog_hrs']) || $_POST['edit_prog_hrs'] === ''){
            $_SESSION['emissing'][] = '<span>Hours</span> is missing.';
            $_SESSION['edit_hours'] = '';
        }else{
            if(!preg_match('/^[0-9]{1,2}$/', $_POST['edit_prog_hrs'])){
                $_SESSION['ematch'][] = '<span>Hours</span> is not properly formatted.';
                $_SESSION['edit_hours'] = $_POST['edit_prog_hrs'];
            }else{

                if($_POST['edit_prog_hrs'] > 24){
                    $_SESSION['ematch'][] = '<span>Hours</span> is too high.';
                }

                $edit_prog_hrs = $_POST['edit_prog_hrs'];
                $_SESSION['edit_hours'] = htmlentities($edit_prog_hrs, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }


        if((!empty($_SESSION['emissing'])) || (!empty($_SESSION['ematch']))){
            $_SESSION['prog_editing'] = 1;
            $_SESSION['ticketId'] = htmlentities($edit_ticket_id, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $_SESSION['progOpen'] = 1;
            goBack($edit_prog_id);
        }else{
            require_once("../connect.php");

            try{
                $sql = "UPDATE Progress SET date=:date, summary=:summary, hours=:hours WHERE pid=:pid";
                $stmt = $dbc->prepare($sql);
                $stmt->execute(['date' =>  $edit_prog_date, 'summary' => $edit_prog_summary, 'hours' => $edit_prog_hrs, 'pid' => $edit_prog_id]);
                $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $_SESSION['prog_editing'] = 0;
                $_SESSION['ticketId'] = $edit_ticket_id;
                $_SESSION['progOpen'] = 1;
                goBack($edit_prog_id);


            }
            catch (PDOException $e){
                printf("Problem " . $e->getMessage());
            }

        }

    }

    if(isset($_POST['cancelEdit'])){
        if(!preg_match('/^[0-9]{0,10}$/', $_POST['ticket_id'])){
            die();
        }else{
            $tid = $_POST['ticket_id'];
        }
        if(!preg_match('/^[t][0-9]{0,12}[p][0-9]{0,12}$/', $_POST['prog_id'])){
            die();
        }else{
            $pid = $_POST['prog_id'];
        }
        unset($_SESSION['prog_editing']);
        unset($_SESSION['prog_edit_id']);
        unset($_SESSION['edit_date']);
        unset($_SESSION['edit_hours']);
        unset($_SESSION['edit_summary']);
        $_SESSION['progOpen'] = 1;
        $_SESSION['ticketId'] = $tid;
        goBack($pid);
    }

    if(isset($_POST['deleteThisPart'])){

        if(!preg_match('/^[0-9]{0,10}$/', $_POST['pid'])){
            die();
        }else{
            $pid = intval($_POST['pid']);
        }
        if(!preg_match('/^[0-9]{0,10}$/', $_POST['tid'])){
            die();
        }else{
            $tid = intval($_POST['tid']);
        }
        require_once("../connect.php");
            try {
                $sql = "DELETE FROM progparts WHERE id =  :id";
                $stmt = $dbc->prepare($sql);
                $stmt->bindParam(':id', $pid, PDO::PARAM_INT);   
                $stmt->execute();
                $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $_SESSION['progAdded'] = 1;
                $_SESSION['ticketId'] = $tid;
                goBack();
            }
            catch (PDOException $e) {
                printf("Problem " . $e->getMessage());
            }
    }


    if(isset($_POST['deleteProgSubmit'])){

        if(!preg_match('/^[t][0-9]{0,12}[p][0-9]{0,12}$/', $_POST['pid'])){
            die();
        }else{
            $pid = $_POST['pid'];
        }
        if(!preg_match('/^[0-9]{0,10}$/', $_POST['tid'])){
            die();
        }else{
            $tid = $_POST['tid'];
        }

        require_once("../connect.php");
            try {
                $sql = "DELETE FROM Progress WHERE pid =  :id ; DELETE FROM progparts WHERE prog_id =  :id ";
                $stmt = $dbc->prepare($sql);
                $stmt->execute([id => $pid]);
                $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $_SESSION['progAdded'] = 1;
                $_SESSION['ticketId'] = $tid;
                goBack();
            }
            catch (PDOException $e) {
                printf("Problem " . $e->getMessage());
            }

    }


if(isset($_POST['searchClosedSubmit'])){

    unset($_SESSION['closed_ids']);
    $_SESSION['no_results'] = 0;

    if(!isset($_POST['company']) || $_POST['company'] == ""){
        unset($_POST['company']);
    }else{
        if(!preg_match('/^[0-9]{0,10}$/', $_POST['company'])){
            die();
        }else{
            $company = $_POST['company'];
        }
    }
    if(!isset($_POST['machine']) || $_POST['machine'] == ""){
        unset($_POST['machine']);
    }else{
        if(!preg_match('/^[a-zA-Z0-9]{0,20}$/', $_POST['machine'])){
            die();
        }else{
            $machine = $_POST['machine'];
        }
    }
    if(!isset($_POST['tech']) || $_POST['tech'] == ""){
        unset($_POST['tech']);
    }else{
        if(!preg_match('/^[0-9]{0,10}$/', $_POST['tech'])){
            die();
        }else{
            $tech = $_POST['tech'];
        }
    }

    try {
            require('../connect.php');
            $sql = "SELECT * FROM Tickets WHERE status = 1";
            // Company Only
            if($company && !$machine && !$tech){
                $sql .= " AND cust_id = :com";
                $stmt = $dbc->prepare($sql);
                $stmt->execute(['com' => $company]);
            }
            // Machine Only
            elseif(!$company && $machine && !$tech){
                $sql .= " AND mach_id = :mac";
                $stmt = $dbc->prepare($sql);
                $stmt->execute(['mac' => $machine]);
            }
            // Tech Only
            elseif(!$company && !$machine && $tech){
                $sql .= " AND tech = :tec";
                $stmt = $dbc->prepare($sql);
                $stmt->execute(['tec' => $tech]);
            }
            // Company and Machine
            elseif($company && $machine && !$tech){
                $sql .= " AND cust_id = :com AND mach_id = :mac";
                $stmt = $dbc->prepare($sql);
                $stmt->execute(['com' => $company, 'mac' => $machine]);
            }
            //Company and Tech
            elseif($company && !$machine && $tech){
                $sql .= " AND cust_id = :com AND tech = :tec";
                $stmt = $dbc->prepare($sql);
                $stmt->execute(['com' => $company, 'tec' => $tech]);
            }
            //Machine and Tech
            elseif(!$company && $machine && $tech){
                $sql .= " AND mach_id = :mac AND tech = :tec";
                $stmt = $dbc->prepare($sql);
                $stmt->execute(['mac' => $machine, 'tec' => $tech]);
            }
            //ALL
            elseif($company && $machine && $tech){
                $sql .= " AND cust_id = :com AND mach_id = :mac AND tech = :tec";
                $stmt = $dbc->prepare($sql);
                $stmt->execute(['com' => $company, 'mac' => $machine, 'tec' => $tech]);
            }
            //NONE
            elseif(!$company && !$machine && !$tech){
                $stmt = $dbc->query("SELECT * FROM Tickets WHERE status = 1");
            }

            $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
                printf("Problem " . $e->getMessage());
        }

        if($stmt->rowCount() > 0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $_SESSION['closed_ids'][] = $row['id'];
            }
        }else{
            $_SESSION['no_results'] = 1;
        }
        header("Location: closed_tickets.php");



}




if(isset($_GET['logOutSubmit'])){
    session_unset();
    goBack();
}
