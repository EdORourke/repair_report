<?php
    date_default_timezone_set('America/Chicago');
    if(isset($_POST['submit'])){

        $date = date('Y-m-d');
        $status = 0;
        $missing = array();
        $match = array();

        if((empty($_POST['at_company'])) || ($_POST['at_company'] === '')){
            $missing[] = 'Company is a required field.';
        }elseif(!preg_match('/^[0-9]{1,12}$/', $_POST['at_company'])){
            $match[] = 'Company is not properly formatted';
        }else{
            $company = $_POST['at_company'];
        }

        if((empty($_POST['at_machine'])) || ($_POST['at_machine'] === '')){
            $missing[] = 'Machine is a required field.';
        }elseif(!preg_match('/^[a-zA-Z0-9]{1,50}$/', $_POST['at_machine'])){
            $match[] = 'Machine is not properly formatted';
        }else{
            $machine = $_POST['at_machine'];
        }

        if((empty($_POST['at_tech'])) || ($_POST['at_tech'] === '')){
            $missing[] = 'Tech is a required field.';
        }elseif(!preg_match('/^[0-9]{1,12}$/', $_POST['at_tech'])){
            $match[] = 'Tech is not properly formatted';
        }else{
            $tech = $_POST['at_tech'];
        }

        if((empty(trim($_POST['at_desc']))) || trim(($_POST['at_desc'] === ''))){
            $missing[] = 'Description is a required field.';
        }else{
            $desc = $_POST['at_desc'];
        }

        if((empty($missing)) && (empty($match))){
            require_once("../connect.php");

            try {
                $sql = "INSERT INTO Tickets (tech, date, cust_id, mach_id, issue, status) VALUES (:tech, :date, :cust_id, :mach_id, :issue, :status)";
                $stmt = $dbc->prepare($sql);
                $stmt->execute(['tech' => $tech, 'date' => $date, 'cust_id' => $company, 'mach_id' => $machine, 'issue' => $desc, 'status' => $status]);
                $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                echo "<script>
                alert(\"Ticket added successfully.\");
                window.close();
                </script>";
            }
            catch (PDOException $e) {
                printf("Problem " . $e->getMessage());
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en" style="background-color:#fff;">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link rel="stylesheet" href="css/machine-style.css">
        <title>Create New Ticket</title>
    </head>
    <body>

        <div id="at-form-container">
            <h3>Create New Ticket</h3>
            <?php  
                if(!empty($missing)){
                    echo '<div class="at-error-box">';
                    foreach ($missing as $item) {
                        echo $item . '<br>';
                    }
                    echo '</div>';
                }
                if(!empty($match)){
                    echo '<div class="at-error-box">';
                    foreach ($match as $item) {
                        echo $item . '<br>';
                    }
                    echo '</div>';
                }
            ?>
            <form action="" method="post">
                <select name="at_company" id="at-company">
                    <option value=""<?php if(!$company){echo ' selected';} ?>>Choose a Company</option>
                    <?php  require_once("../connect.php");
                        if($companies = $dbc->query("SELECT id, company FROM Customers")){
                            while($row = $companies->fetch(PDO::FETCH_ASSOC)){
                                echo '<option value="'.$row['id'].'"';
                                if($company == $row['id']){echo ' selected';}
                                echo '>'.$row['company'].'</option>';
                            }
                        }else{ echo 'companies failed';

                        } ?>
                </select>
                <select name="at_machine" id="at-machine">
                    <option value=""<?php if(!$machine){echo ' selected';} ?>>Choose a Machine</option>
                    <?php  require_once("../connect.php");
                        if($machines = $dbc->query("SELECT id, name FROM Machines")){
                            while($row = $machines->fetch(PDO::FETCH_ASSOC)){
                                echo '<option value="'.$row['id'].'"';
                                if($machine == $row['id']){echo ' selected';}   
                                echo '>'.$row['name'].'</option>';
                            }
                        }else{ echo 'machines failed';

                        } ?>                    
                </select>
                <select name="at_tech" id="at-tech">
                    <option value=""<?php if(!$tech){echo ' selected';} ?>>Choose a Tech</option>
                     <?php  require_once("../connect.php");
                        if($techs = $dbc->query("SELECT id, name FROM Technicians")){
                            while($row = $techs->fetch(PDO::FETCH_ASSOC)){
                                echo '<option value="'.$row['id'].'"';
                                if($tech == $row['id']){echo ' selected';}     
                                echo '>'.$row['name'].'</option>';
                            }
                        }else{ echo 'techs failed';

                        } ?>                   
                </select>
                <textarea name="at_desc" id="at-desc" placeholder="Describe the issue...">
                    <?php echo $desc; ?>
                </textarea>
                <input type="submit" name="submit" value="Create Ticket">
            </form>
        </div>
    </body>
</html>