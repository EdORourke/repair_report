<?php if(isset($_POST['submit'])){

    $missing = array();
    $match = array();

    if(!isset($_POST['company']) || $_POST['company'] === ''){
        $missing[] = '<span>Company</span> is a required field.';
    }else{
        if(!preg_match('/^[a-zA-Z0-9 -!,.&*\']{1,30}$/', $_POST['company'])){
            $match[] = 'Company contains invalid characters';
        }else{
            $company = $_POST['company'];
        }
    }
    if(!isset($_POST['address']) || $_POST['address'] === ''){
        $missing[] = '<span>Address</span> is a required field.';
    }else{
        if(!preg_match('/^[a-zA-Z0-9 -!,.&*]{1,30}$/', $_POST['address'])){
            $match[] = 'Address contains invalid characters';
        }else{
            $address = $_POST['address'];
        }
    }   
    if(!isset($_POST['city']) || $_POST['city'] === ''){
        $missing[] = '<span>City</span> is a required field.';
    }else{
        if(!preg_match('/^[a-zA-Z0-9 -]{1,30}$/', $_POST['city'])){
            $match[] = 'City contains invalid characters';
        }else{
            $city = $_POST['city'];
        }
    }
    if(!isset($_POST['state']) || $_POST['state'] === ''){
        $missing[] = '<span>State</span> is a required field.';
    }else{
        if(!preg_match('/^[A-Z]{2}$/', $_POST['state'])){
            $match[] = 'State invalid';
        }else{
            $state = $_POST['state'];
        }
    }
    if(!isset($_POST['zip']) || $_POST['zip'] === ''){
        $missing[] = '<span>Zip Code</span> is a required field.';
    }else{
        if(!preg_match('/^\d{5}$|^\d{5}-\d{4}$/', $_POST['zip'])){
            $match[] = 'Zip contains invalid characters';
        }else{
            $zip = $_POST['zip'];
        }
    }
    if(!isset($_POST['phone']) || $_POST['phone'] === ''){
        $missing[] = '<span>Phone Number</span> is a required field.';
    }else{
        if(!preg_match('/^[2-9]\d{2}-\d{3}-\d{4}$/', $_POST['phone'])){
            $match[] = 'Phone contains invalid characters';
        }else{
            $phone = $_POST['phone'];
        }
    }
    if(!isset($_POST['contact']) || $_POST['contact'] === ''){
        $missing[] = '<span>Contact</span> is a required field.';
    }else{
        if(!preg_match('/^[a-zA-Z0-9 -!,.&*\']{1,30}$/', $_POST['contact'])){
            $match[] = 'Contact contains invalid characters';
        }else{
            $contact = $_POST['contact'];
        }
    }   

    if(empty($missing) && empty($match)){

        require_once("../connect.php");

        try {

            $sql = "INSERT INTO Customers (company, contact, address, city, state, zip, phone) VALUES (:company, :contact, :address, :city, :state, :zip, :phone) ";
            $stmt = $dbc->prepare($sql);
            $stmt->execute(['company' => $company, 'contact' => $contact, 'address' => $address, 'city' => $city, 'state' => $state, 'zip' => $zip, 'phone' => $phone]);
            $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<script>
            alert(\"Company added successfully.\");
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
        <title>Add a Company</title>
    </head>
    <body>
        <div id="ac-form-container" class="group">
            <h3>Add a Company</h3>
            <?php 
                if(!empty($missing)){
                    echo '<ul class="empty-errors">';
                    foreach($missing as $item){
                        echo '<li>' . $item . '</li>';
                    }
                    echo '</ul>';
                }
                if(!empty($match)){
                    echo '<ul class="empty-errors">';
                    foreach($match as $item){
                        echo '<li>' . $item . ' contains special characters or is incorrectly formatted. </li>';
                    }
                    echo '</ul>';
                }
            ?>
            <p><span>*</span> All fields required</p>
            <form action="" method="post">
                <label for="company">Company Name:</label><br>
                <input type="text" name="company" value="<?php echo htmlentities($company, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" maxlength="30"><br>
                <label for="address">Address:</label><br>
                <input type="text" name="address" value="<?php echo htmlentities($address, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><br>
                <label for="city" id="label-city">City</label><label for="state" id="label-state">State</label><label for="zip" id="label-zip">Zip</label><br>
                <input type="text" name="city" id="ac-city" value="<?php echo htmlentities($city, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
                <select name="state" id="ac-state">
                    <option value=""></option>
                    <option value="AL"<?php if($state == 'AL'){echo ' selected';} ?>>AL</option>
                    <option value="AK"<?php if($state == 'AK'){echo ' selected';} ?>>AK</option>
                    <option value="AR"<?php if($state == 'AR'){echo ' selected';} ?>>AR</option>	
                    <option value="AZ"<?php if($state == 'AZ'){echo ' selected';} ?>>AZ</option>
                    <option value="CA"<?php if($state == 'CA'){echo ' selected';} ?>>CA</option>
                    <option value="CO"<?php if($state == 'CO'){echo ' selected';} ?>>CO</option>
                    <option value="CT"<?php if($state == 'CT'){echo ' selected';} ?>>CT</option>
                    <option value="DC"<?php if($state == 'DC'){echo ' selected';} ?>>DC</option>
                    <option value="DE"<?php if($state == 'DE'){echo ' selected';} ?>>DE</option>
                    <option value="FL"<?php if($state == 'FL'){echo ' selected';} ?>>FL</option>
                    <option value="GA"<?php if($state == 'GA'){echo ' selected';} ?>>GA</option>
                    <option value="HI"<?php if($state == 'HI'){echo ' selected';} ?>>HI</option>
                    <option value="IA"<?php if($state == 'IA'){echo ' selected';} ?>>IA</option>	
                    <option value="ID"<?php if($state == 'ID'){echo ' selected';} ?>>ID</option>
                    <option value="IL"<?php if($state == 'IL'){echo ' selected';} ?>>IL</option>
                    <option value="IN"<?php if($state == 'IN'){echo ' selected';} ?>>IN</option>
                    <option value="KS"<?php if($state == 'KS'){echo ' selected';} ?>>KS</option>
                    <option value="KY"<?php if($state == 'KY'){echo ' selected';} ?>>KY</option>
                    <option value="LA"<?php if($state == 'LA'){echo ' selected';} ?>>LA</option>
                    <option value="MA"<?php if($state == 'MA'){echo ' selected';} ?>>MA</option>
                    <option value="MD"<?php if($state == 'MD'){echo ' selected';} ?>>MD</option>
                    <option value="ME"<?php if($state == 'ME'){echo ' selected';} ?>>ME</option>
                    <option value="MI"<?php if($state == 'MI'){echo ' selected';} ?>>MI</option>
                    <option value="MN"<?php if($state == 'MN'){echo ' selected';} ?>>MN</option>
                    <option value="MO"<?php if($state == 'MO'){echo ' selected';} ?>>MO</option>	
                    <option value="MS"<?php if($state == 'MS'){echo ' selected';} ?>>MS</option>
                    <option value="MT"<?php if($state == 'MT'){echo ' selected';} ?>>MT</option>
                    <option value="NC"<?php if($state == 'NC'){echo ' selected';} ?>>NC</option>
                    <option value="ND"<?php if($state == 'ND'){echo ' selected';} ?>>ND</option>	
                    <option value="NE"<?php if($state == 'NE'){echo ' selected';} ?>>NE</option>
                    <option value="NH"<?php if($state == 'NH'){echo ' selected';} ?>>NH</option>
                    <option value="NJ"<?php if($state == 'NJ'){echo ' selected';} ?>>NJ</option>
                    <option value="NM"<?php if($state == 'NM'){echo ' selected';} ?>>NM</option>			
                    <option value="NV"<?php if($state == 'NV'){echo ' selected';} ?>>NV</option>
                    <option value="NY"<?php if($state == 'NY'){echo ' selected';} ?>>NY</option>
                    <option value="OH"<?php if($state == 'OH'){echo ' selected';} ?>>OH</option>
                    <option value="OK"<?php if($state == 'OK'){echo ' selected';} ?>>OK</option>
                    <option value="OR"<?php if($state == 'OR'){echo ' selected';} ?>>OR</option>
                    <option value="PA"<?php if($state == 'PA'){echo ' selected';} ?>>PA</option>
                    <option value="RI"<?php if($state == 'RI'){echo ' selected';} ?>>RI</option>
                    <option value="SC"<?php if($state == 'SC'){echo ' selected';} ?>>SC</option>
                    <option value="SD"<?php if($state == 'SD'){echo ' selected';} ?>>SD</option>
                    <option value="TN"<?php if($state == 'TN'){echo ' selected';} ?>>TN</option>
                    <option value="TX"<?php if($state == 'TX'){echo ' selected';} ?>>TX</option>
                    <option value="UT"<?php if($state == 'UT'){echo ' selected';} ?>>UT</option>
                    <option value="VT"<?php if($state == 'VT'){echo ' selected';} ?>>VT</option>
                    <option value="VA"<?php if($state == 'VA'){echo ' selected';} ?>>VA</option>
                    <option value="WA"<?php if($state == 'WA'){echo ' selected';} ?>>WA</option>
                    <option value="WI"<?php if($state == 'WI'){echo ' selected';} ?>>WI</option>	
                    <option value="WV"<?php if($state == 'WV'){echo ' selected';} ?>>WV</option>
                    <option value="WY"<?php if($state == 'WY'){echo ' selected';} ?>>WY</option>
                </select>
                <input type="text" name="zip" id="ac-zip" value="<?php echo htmlentities($zip, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><br>
                <label for="phone">Phone:</label><br>
                <input type="text" name="phone" value="<?php echo htmlentities($phone, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><br>
                <label for="contact">Contact Name:</label><br>
                <input type="text" name="contact" value="<?php echo htmlentities($contact, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"><br>
                <input type="submit" name="submit" value="ADD COMPANY">
            </form>
        </div>
    </body>
</html>