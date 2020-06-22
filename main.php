<?php session_start();
if($_SESSION['loggedin'] == false){
    header('Location: index.php');
}else{

    $id;
    $progOpen = 0;

    if($_SESSION['partProblem'] == 1){
        $id = $_SESSION['ticketId'];
        unset($_SESSION['ticketId']);
        $_SESSION['progOpen'] = 0;
        $progOpen = 1;
        unset($_SESSION['partProblem']);
    }

    if($_SESSION['progAdded'] == 1){
        $id = $_SESSION['ticketId'];
        unset($_SESSION['ticketId']);
        unset($_SESSION['progAdded']);
    }

    if($_SESSION['progOpen'] == 1){
       $id = $_SESSION['ticketId'];
       unset($_SESSION['ticketId']);
       $_SESSION['progOpen'] = 0;
       $progOpen = 1; 
    }

    if($_SESSION['scroll']){
        $scroll = $_SESSION['scroll'];
        unset($_SESSION['scroll']);
    }else{
        $scroll = '';
    }
   

    if(isset($_POST['submit'])){

        $id = $_POST['li-id'];

        if($_SESSION['loggedin'] == 1){
            session_unset();
            $_SESSION['loggedin'] = 1;
        }
    } 

function noHTML($input){
    return htmlentities($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';
// echo 'POST: '; 
// echo '<pre>';
// print_r($_POST);
// echo '</pre>';
// echo 'Ticket ID: ' . $id;
// echo '<br>scroll ' . $scroll;
?>
<?php require_once("../connect.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
    <link rel="stylesheet" href="css/machine-style.css">
    <title>Machine Repair Console</title>
</head>
<body><div id="show-size"></div>
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark" id="mrc-header">
        <a class="navbar-brand" href="#">Machine Repair Console</a>
        <ul class="navbar-nav mr-auto" id="mrc-nav">
        <a href="add_ticket.php" target="popup" onclick="window.open('add_ticket.php','popup','width=400,height=550'); return false;"><li class="nav-item">ADD A TICKET</li></a>
        <a href="add_company.php" target="popup" onclick="window.open('add_company.php','popup','width=400,height=550'); return false;"><li class="nav-item">ADD A COMPANY</li></a>
        <a href="closed_tickets.php"><li class="nav-item">VIEW CLOSED TICKETS</li></a>
            <a href="actions.php?logOutSubmit=1"><li class="nav-item">LOG OUT</li></a>
        </ul>   
    </nav>
    <div class="container-fluid group" id="container">
        
        <div class="row">
            <div class="col-md-6 col-sm-4" id="list-section">
                <?php 
                 require("../connect.php"); 
                 if($result = $dbc->query("
                    SELECT t.id AS tid, t.mach_id AS tmach, t.cust_id AS tcus, t.date AS tdate, t.status AS status,
                    m.id AS mid, m.name AS mname, 
                    c.id AS CID, c.company AS cname
                    FROM Tickets t
                    LEFT OUTER JOIN Machines m
                    ON t.mach_id = m.id
                    LEFT OUTER JOIN Customers c
                    ON t.cust_id = c.id
                    WHERE t.status = 0
                ")){
                        while($row = $result->fetch(PDO::FETCH_ASSOC)){
                            echo '<div class="list-item group">';
                            echo '<span class="li-id">'.noHTML($row['tid']).'</span>';
                            echo '<span class="li-co-label">COMPANY</span>';
                            echo '<span class="li-co">'.noHTML($row['cname']).'</span>';
                            echo '<span class="li-mach-label">EQUIPMENT</span>';
                            echo '<span class="li-mach">'.noHTML($row['mname']).'</span>';
                            echo '<span class="li-date">'.noHTML($row['tdate']).'</span>';
                            echo '<form action="" method="post">';
                            echo '<input type="hidden" name="li-id" value="'.noHTML($row['tid']).'">';
                            echo '<input type="submit" value="" name="submit" class="li-trigger">';
                            echo '</form>';
                            echo '</div><!--list-item-group-->';
                        }
                }
                ?>
            </div>
            <div class="col-md-6 col-sm-8" id="detail-section">
                <div class="detail-display group">
                    <?php 
                        include("summary.php");
                        $b = new Summary($id);
                        $b->createReport($progOpen);
                        ?>
                </div><!--detail display group-->
            </div>
        </div><!--row-->

    </div>
    <footer>
        <span>&copy; 2020 Ed O'Rourke</span>
    </footer>
    <script>
        function showSize(){var w = window.innerWidth;var d = document.getElementById('show-size');d.innerText = w;}window.onresize = showSize;showSize();

         $(function() {
            $( "#datepicker-8" ).datepicker({
               dateFormat:"yy-mm-dd", 
               prevText:"click for previous months",
               nextText:"click for next months",
               showOtherMonths:true,
               selectOtherMonths: false
            });
         });

         $(document).ready(function(){

            $("#add-progress").click(function(){
                $("#add-progress-form").toggle();
            });
                
            $(".add-parts-button").click(function(){
                $( this ).parent().children(".add-parts-form").toggle();
            });
            $("#add-parts-reveal").click(function(event){
                event.preventDefault();
                $("#add-parts-to-progress").toggle();
            });
         });

         <?php 
            if($scroll){
                echo '
                (function(){
                    var loc = document.getElementById("'.$scroll.'");
                    loc.scrollIntoView();
                    console.log("made it");
                })();
                ';
            }
          ?>
      </script>
</body>
</html>

<?php } ?>

