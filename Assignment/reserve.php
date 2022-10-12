<html>
    <head>
        <meta charset = "utf-8">
        <title> Search</title>
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
        <br>  
<?php

session_start();
include "db.php";

//if user not logged in
if ($_SESSION["Status"] != "Logged in")
{
    //redirect to login page which will ask user to log in
    header( 'Location: login.php?status=notloggedin');
}

//if page number is set and not null, assign to variable
if (isset($_GET['pagenum']) && $_GET['pagenum']!= "") 
{
    $pageNum = $_GET['pagenum'];
} 

//otherwise assign page number to 1
else 
{
    $pageNum = 1;
}

//variable to determine how many rows per page
$displayPage = 5;
//variable which will determine what rows will be displayed on different pages
$newPage = ($pageNum - 1) * $displayPage;
//pagination function to retrieve total number of pages needed to display any given search query
$totalPages = Pagination($conn, $displayPage);

//query which retrieves rows of books that arent reserved
$sql = "SELECT * FROM books WHERE Reserved = 'N' LIMIT $newPage, $displayPage";
$result = $conn->query($sql);

//when query is finished
if (isset($result))
{
    //if rows are found, display table
    if ($result != false && $result-> num_rows > 0) 
    {
        echo "<table class = 'table' border='1'>";
        echo "<tr class = 'table'><th>ISBN</th>
            <th>Book Title</th>
            <th>Author</th>
            <th>Edition</th>
            <th>Year</th>
            <th>Category</th>
            <th>Reserved</th>";

        //display rows inside the table
        while ($row = $result->fetch_assoc())
        {
            echo "<tr><td>";
            echo $row["ISBN"];
            echo "</td><td>";
            echo $row["BookTitle"];
            echo "</td><td>";
            echo $row["Author"];
            echo "</td><td>";
            echo $row["Edition"];
            echo "</td><td>";
            echo $row["Year"];
            echo "</td><td>";
            //query to get the category name corresponding to the category ID
            $CategoryList = "SELECT CategoryName FROM Categories WHERE CategoryID = '$row[Category]'";
            $result1 = $conn->query($CategoryList);
            $row1 = $result1->fetch_assoc();
            echo $row1["CategoryName"];
            echo "</td><td>";
            //hyperlink created for every displayed book which sends a the matching ISBN number inside a string query.
            echo '<a class = "btn btn-success" href = "reserve.php?ISBN=' . $row['ISBN'] . '&Username='.$_SESSION['Name'].'"> Reserve </a> </td>';
            echo "</tr\n>";
        }

        echo "</table>";
        //function to display links to view either the next or previous page
        displayPagination($pageNum, $totalPages);

        //if book is selected to be reserved
        if (isset($_GET['ISBN']) != NULL)
        {
            //assign selected books ISBN to a variable
            $id = $_GET['ISBN'];
            //updates selected books reserved status to yes
            $sql = "UPDATE books SET Reserved = 'Y' WHERE ISBN = '$id'"; 
            $conn->query($sql);
            $Username = $_SESSION["Name"];
            //date of reservation is captured and sent to the table
            $Date = date("y-m-d");
            $conn->query($sql);
            //adds newly reserved book to reserved table
            $sql2 = "INSERT INTO Reserved (ISBN, Username, Date) VALUES ('$id','$Username','$Date')";
            $result = $conn->query($sql2);
            //refreshes page to remove newly reserved book from the available books
            header('Location: reserve.php');
        }
    }

    //if no books are available
    else
    {   
        //display message
        echo "<p class = 'message'> There are currently no books available to reserve </p> <br>";
    }

}

//function to determine how many pages are needed to fit all rows
function Pagination($conn, $displayPage)
{
    //counts the amount of non-reserved books
    $sql = "SELECT COUNT(*) As totalRows FROM Books WHERE Reserved = 'N'";
    $result = $conn->query($sql);
    $totalRows = $result->fetch_assoc();
    $totalRows = $totalRows['totalRows'];
    //rounds up eg. 16/5 = 3.2 pages to 4 pages
    $totalPages = ceil ($totalRows / $displayPage);
    return $totalPages;
}


function displayPagination($pageNum, $totalPages)
{
    //creates next and previous variables which will let the query know what set of rows the new page should display
    $prev = $pageNum - 1;
    $next = $pageNum + 1;   
    unset($_GET["pagenum"]);

    if ($totalPages != 0)
    {
        //displays current page number
        echo "<p id = 'pageNum'>Page $pageNum</p>";

        //if not on final page, display next page option
        if ($pageNum != $totalPages)
        {
            echo "<a class = 'next' href='?". http_build_query($_GET) ."&pagenum=$next'>Next Page</a>";
        } 

        //if not on first page, display previous page option
        if ($pageNum != 1)
        {
            echo "<a class = 'prev'  href='?". http_build_query($_GET) ."&pagenum=$prev'>Previous Page</a>";
        } 
    }
}
?>

<footer>
  <p>Website made by Finn Maguire</p>
</footer>

</body> 

</html>