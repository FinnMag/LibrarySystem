<?php
    
    $serverName = "localhost";
    $serverUsername = "Finn";
    $serverPassword = "Password123";
    $dbname = "Assignment";

    $conn = new mysqli($serverName, $serverUsername, $serverPassword, $dbname);

    if ($conn ->connect_error)
    {
        die("Connect failed: " . $conn ->connect_error);

    }
?>