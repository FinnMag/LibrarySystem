<html>
    <head>
        <meta charset = "utf-8">
        <title> Registration</title>
        <link rel = "stylesheet" type = "text/css" href = "site.css">
    </head>

    <body>
        <nav>
            <ul>
                <li> <a href = "login.php" > Login </a> </li>
                <li> <a href = "registration.php" > Registration </a> </li>
                <li> <a href = "search.php?pagenum=1" > Search </a> </li>
                <li> <a href = "reserve.php" > Reserve </a> </li>
                <li> <a href = "viewreserved.php" > View Reserved </a> </li>
            </ul>
        </nav> 

    <div class = "registration">
        <form method="post">
            <p>Username: <input type = "text" class = "generalInput" name = "Username"></p>
            <p>Password: <input type = "password" class = "generalInput" name = "Password"></p>
            <p>Confirm Password: <input type = "password" class = "generalInput" name = "ConfirmPassword"></p>
            <p>First Name: <input type="text" class = "generalInput" name="FirstName"></p>
            <p>Surname: <input type= "text" class = "generalInput" name="Surname"></p>
            <p>AddressLine1: <input type ="text" class = "generalInput" name = "AddressLine1"></p>
            <p>AddressLine2: <input type = "text" class = "generalInput" name = "AddressLine2"></p>
            <p>City: <input type = "text" class = "generalInput" name = "City"></p>
            <p>Telephone: <input type = "text" class = "generalInput" name = "Telephone"></p>
            <p>Mobile: <input type = "text" class = "generalInput" name = "Mobile"></p>
            <p><input type= "submit" value = "Submit" name = "Submit"/></p>
        </form>
    </div>
<?php

include "db.php";

//if register is successful
if (isset($_GET['successful']))
{
    if ($_GET['successful'] == 'yes')
    {
        //Success message
        echo "<p class = 'message'>Successfully registered </p>";
    }
}

//if all information is posted
if(isset($_POST['Submit']))
{
    //sends all inputed text through function to remove unwanted special characters and assigns them to variables
    $un = validation($_POST['Username']);
    $p = validation($_POST['Password']);
    $cp = validation($_POST['ConfirmPassword']);
    $fn = validation($_POST['FirstName']);
    $sn = validation($_POST['Surname']);
    $ad1 = validation($_POST['AddressLine1']);
    $ad2 = validation($_POST['AddressLine2']);
    $c = validation($_POST['City']);
    $t = validation($_POST['Telephone']);
    $m = validation($_POST['Mobile']);

    
    //checks if any input was left blank
    if(($un && $p && $fn && $sn && $ad1 && $ad1 && $c && $t && $m) != "")
    {
        //checks if phone number is numeric and 10 numbers in length
        if (is_numeric($m) && strlen($m) == 10) 
        {
            //query to insert user input into table
            $sql = "INSERT INTO login (Username, Password, FirstName, Surname, AddressLine1, AddressLine2, City, Telephone, Mobile) VALUES ('$un', '$p', '$fn', '$sn','$ad1','$ad2','$c','$t','$m')";

            //if passwords match
            if($p == $cp)
            {
                //if username is not taken
                if ($conn->query($sql) === TRUE) 
                {
                    //sends string query to display success message
                    header('Location: registration.php?successful=yes');
                } 
                
                //if username is taken
                else 
                {   
                    //error message
                    echo "<p class = 'message'>This username has already been taken, please try another </p>";
                }
            }

            //if passwords dont match
            else
            {
                 //error message
                echo "<p class = 'message'>Passwords do not match </p>";
            }
        }

        //if phone number is not numeric or 10 numbers in length
        else
        {
             //error message
            echo "<p class = 'message'>Please ensure mobile phone number is numeric and 10 numbers long </p>";
        }
    }

    //if blank input detected
    else
    {
         //error message
        echo "<p class = 'message'>Please make sure all data is inputed </p>";
    }
}


//function to validate input
function validation($input) 
{
    //pre-defined functions to remove special characters
    $input = trim($input);
    $input = htmlspecialchars($input);
    $input = htmlentities($input);
    $input = stripslashes($input);
    return $input;
}

?>

<footer>
  <p>Website made by Finn Maguire</p>
</footer>

</body> 

</html>
