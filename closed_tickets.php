<?php session_start(); ?>
<?php include 'closed.php'; ?>
<?php
if(isset($_POST['submit'])){
    $id = $_POST['li-id'];
}  ?>
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
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
        <a class="navbar-brand" href="#">Machine Repair Console</a>
        <ul class="navbar-nav mr-auto" id="mrc-nav">
        <a href="add_ticket.php" target="popup" onclick="window.open('add_ticket.php','popup','width=400,height=550'); return false;"><li class="nav-item">ADD A TICKET</li></a>
        <a href="add_company.php" target="popup" onclick="window.open('add_company.php','popup','width=400,height=550'); return false;"><li class="nav-item">ADD A COMPANY</li></a>
        <a href="main.php"><li class="nav-item">VIEW MAIN CONSOLE</li></a>
            <a href="actions.php?logOutSubmit=1"><li class="nav-item">LOG OUT</li></a>
        </ul>   
    </nav>
    <div class="container-fluid group" id="container">
        <div class="row">
            <div class="col-md-6 col-sm-4" id="list-section">
            <p>Select your search parameters, or search with no selections to view all closed tickets</p>
                <?php
                $closed = new Closed();
                $closed->searchForm();
                if($_SESSION['no_results'] == 1){
                    echo "No Results.";
                }else{

                    if($_SESSION['closed_ids']){
                        foreach ($_SESSION['closed_ids'] as $value) {
                            $closed->generateTab($value);
                        }
                    }
                                
                }
                ?>

            </div><!--list section-->
            <div class="col-md-6 col-sm-8" id="detail-section">


                <div class="detail-display group">
                    <?php include("progress_block.php");




                        include("summary.php");
                        $b = new Summary($id);
                        $b->closedReport();



                        ?>




                </div><!--detail display group-->



            </div>
        </div><!--row-->



    </div><!--container-->

</body>
</html>