<?php session_start();
function goBack(){header('Location: main.php');} ?>
<?php if(isset($_POST['searchClosed'])){



 //echo $_POST['company'] . '-' . $_POST['machine'] . '-' . $_POST['tech'];



$sql = "SELECT * FROM Tickets";

if((isset($_POST['company'])) && ($_POST['company'] != '')){
    $company = $_POST['company'];

}

if($company){
    echo 'The company is ' . $company;
    $sql .= ' WHERE cust_id = ' . $company;
    echo '<br>So...the query is: ' . $sql;
}else{
    echo 'No company provided';
}

        // require_once("../connect.php");
        // try {
        //     $sql = "";
        //     $stmt = $dbc->prepare($sql);
        //     $stmt->execute([]);
        //     $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // }
        // catch (PDOException $e) {
        //     printf("Problem " . $e->getMessage());
        // }
    }











  
