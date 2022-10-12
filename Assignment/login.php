<html> 
    <head>
        <meta charset = "utf-8">
        <title> Login</title>
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
<?php

session_start();
include "db.php";

//Checks status
if (isset($_GET['status'])) 
{   
    //if logged out
    if($_GET['status'] == 'loggedout')
    {
        //logged out successful
        echo "<p class = 'message'> Successfully logged out </p>";
    }

    //if logged in
    else if($_GET['status'] == 'loggedin')
    {
        //logged in successful
        echo "<p class = 'message'> Successfully logged in </p>";
    }

    //if redirected from other pages
    elseif($_GET['status'] == 'notloggedin')
    {
        //indicates to user that they must be signed in to access other pages
        echo "<p class = 'message'> You must be signed in to view or reserve books </p>";
    }
}

//if no status has been set or not logged in
if (isset($_SESSION['Status']) == NULL || $_SESSION['Status'] != "Logged in")
{
    //display login form
    echo "<div class = 'login'>
            <form method = 'post'>
                <p> Username: 
                    <input type='text' class = 'generalInput' name='Username'/>
                </p>

                <p>Password: 
                    <input type='password' class = 'generalInput' name='Password'/>
                </p>

                <p>
                <input type='submit' class = 'logBtn' value='Login' name = 'Login'>
                </p>
            </form>
        </div>";
}

//if logged in
else
{
    //display logout option
    echo " <div class = 'login'>
                <form method = 'post'> 
                    <p> <input type='submit' class = 'logBtn' value='Logout' name = 'Logout'> </p> 
                </form>
            </div>";
}

//if button is pressed
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{   
    //if login is pressed
    if (isset($_POST['Login'])) 
    {
        //checks if both username and password is entered
        if (isset($_POST["Username"]) && isset($_POST["Password"]))
        {       
            //assigns inputed username and password to variables
            $Username = $_POST["Username"];
            $Password = $_POST["Password"];

            //query to match login details
            $sql =" SELECT * FROM login WHERE Username = '$Username' AND Password = '$Password' ";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();

            //if username and password matches
            if ($row != 0)
            {	
                //sets session as logged in
                $_SESSION["Status"] = "Logged in";
                //sets logged in username
                $_SESSION["Name"] = $Username;
                //string query to display success message
                header('Location: login.php?status=loggedin');
            }
            
            //if username or password dont match
            else
            {
                //error message
                echo "<p class = 'message'>Incorrect username or password </p>";
            } 
        }
    }

    //if logout is pressed
    else
    {
        session_destroy();
        //string query to display success messaGE
        header('Location: login.php?status=loggedout');
    }
}
?>

<footer>
  <p>Website made by Finn Maguire</p>
</footer>

</body> 
</html>