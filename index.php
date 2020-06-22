<?php session_start(); 
if($_SESSION['loggedin']){
    unset($_SESSION['loggedin']);
}
if(isset($_POST['submit'])){

    require("../connect.php");
    $errorMessage = '';

    if($_POST['user']){


        if(preg_match('/^#?[A-Za-z0-9]{8}$/', $_POST['user'])){
            $name = $_POST['user'];
        }else{
            echo "Name failed";
        }


        if(preg_match('/^[improv3315!]{11}$/', $_POST['pw'])){
            $pw = $_POST['pw'];
        }else{
            echo "failure4";
        }




        
    }else{
        $errorMessage .= 'Username Required';
    }


    if($check = $dbc->query("SELECT * FROM users WHERE name = '$name'")){
        if ($check->rowCount() > 0){

            while($row = $check->fetch(PDO::FETCH_ASSOC)){

       
                if(password_verify($pw, $row['pass'])){
                    $_SESSION['loggedin'] = true;
                    header('Location: main.php');
                }else{
                    $errorMessage .= 'Login Failed 2';
                }
            }
        }else{
            $errorMessage .= 'Login Failed 1';
        }
    }else{
        $errorMessage .= 'Login Failed 0';
    }

     echo $errorMessage;   
}
?>
<?php require_once("../connect.php"); ?>
<!DOCTYPE html>
<html lang="en" style="background-color:#fdfdfd;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="css/machine-style.css">
    <title>Machine Repair Console - Log In</title>
</head>
<body>
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
        <a class="navbar-brand" href="#">Machine Repair Console</a>
    </nav>
    <div class="container-fluid" id="login-container">
        
        <div id="login-form" class="group">
            <h4>Log In:</h4>
            <form action="" method="post">
            <input type="text" name="user" placeholder="username"><br>
            <input type="password" name="pw" placeholder="password"><br>
            <input type="submit" name="submit" value="Log In"> 
            </form>     
        </div>
    </div>
</body>
</html>







