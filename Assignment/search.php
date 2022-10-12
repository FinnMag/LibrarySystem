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

    <form method = "GET">

        <input type = "text" name = "Search" placeholder = "Search by Title or Author" id = "SearchBox" />

        <?php   
            //function to display category options
            include "db.php";
            getCategory($conn); 
        ?>

        <input type = "submit" value = "Search" id = 'SearchSubmit'/>
       
    </form>

<?php
session_start();

//if not logged in
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

//if search or category set, assign it to user input, if not assign it as blank
$Search = isset($_GET['Search']) ? $_GET['Search'] : "";
$Category = isset($_GET['Categories']) ? $_GET['Categories'] : "";

//Validates search input
$Search = Validation($Search);

//if user enters search, or if category has not been selected
if (strlen($Search) > 1 || $Category == "" || $Category == "No Value" )
{
    //if search query has not been entered assign it as null
    if (isset($_GET['Search']) == NULL)
    {
        $Search = "";
    }

    //if search query entered, assign it to a variable
    else
    {
        $Search = ($_GET['Search']);
    }

    //condition to let the pagination function know which query to select
    $condition = "Search";
    $selCategory = "";

    //pagination function to retrieve total number of pages needed to display any given search query
    $totalPages = Pagination($conn, $displayPage, $condition, $selCategory, $Search);
    //query to retrieve book titles that match user input by either the book title itself or its author
    $sql = "SELECT * FROM books WHERE BookTitle LIKE '%$Search%' OR Author LIKE '%$Search%' LIMIT $newPage, $displayPage";
    $result = $conn->query($sql);
}

//if category is selected
elseif (isset($_GET['Categories']))
{ 
    $selCategory = $_GET['Categories'];
    //condition to let the pagination function know which query to select
    $condition = "Category";
    //query to retrieve book titles that match user input by either the book title itself or its author
    $totalPages = Pagination($conn, $displayPage, $condition, $selCategory, $Search);
    //query to retrieve book titles that match the slected category
    $sql = "SELECT * FROM books WHERE Category = '$selCategory' LIMIT $newPage, $displayPage";
    $result = $conn->query($sql);
}

//when query is finished
if (isset($result))
{   
    //if rows are found, display table
    if ($result != false && $result-> num_rows > 0) 
    {
        echo "<table class = 'table' border='1'>";
        echo "<tr><th>ISBN</th>
            <th>Book Title</th>
            <th>Author</th>
            <th>Edition</th>
            <th>Year</th>
            <th>Category</th>
            <th>Reserved</th>";

        //display rows inside the table
        while ($row = $result->fetch_assoc())
        {
            echo "<tr class = 'table'><td>";
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
            echo $row["Reserved"];
            echo "</tr>";
        }

        echo "</table>";
        //function to display links to view either the next or previous page
        displayPagination($pageNum, $totalPages);
    }

    //if no results are found
    else
    {   
        //indicate that no results were found
        echo "<p class = 'message'>0 Results found </p>";
    }
}

//function to display category options
function getCategory($conn)
{
    //retrieves categories from the database
    $sql = "SELECT CategoryName FROM Categories";
    $result = $conn->query($sql);
    $i = 0;

    //assigns retrieved categories to an array
    while ($row = $result->fetch_assoc())
    {
        $categories[$i] = $row["CategoryName"];
        //index
        $i += 1; 
    }

    //declare drop down menu
    echo " <select name = 'Categories' id = 'CategoryLabel'>";

    //default no value category
    echo "<option value='No Value'> Select Category </option>";

    //puts categories inside the drop down menu
    for ($i = 0; $i < count($categories); $i++)
    {
        $j = $i + 1;
        //echos out categories inside the menu
        echo '<option value="'.'00'.$j.'"> '.$categories[$i].' </option>';
    }
  
}

//function to determine how many pages are needed to fit all rows
function Pagination($conn, $displayPage, $condition, $selCategory, $Search)
{
    //if category is selected
    if($condition == "Category")
    {
        //counts how many rows where selected category matches
        $sql = "SELECT COUNT(*) As totalRows FROM books WHERE Category = '$selCategory'";
    }

    //if search is selected
    elseif ($condition == "Search")
    {
        //counts how many rows where search matches either the title or author
        $sql = "SELECT COUNT(*) As totalRows FROM books WHERE BookTitle LIKE '%$Search%' OR Author LIKE '%$Search%' ";
    }

    //queries either statement
    $result = $conn->query($sql);
    $totalRows = $result->fetch_assoc();
    $totalRows = $totalRows['totalRows'];
    //rounds up eg. 16/5 = 3.2 pages to 4 pages
    $totalPages = ceil ($totalRows / $displayPage);
    return $totalPages;
}

//function to display links to view either the next or previous page
function displayPagination($pageNum, $totalPages)
{
    //creates next and previous variables which will let the query know what set of rows the new page should display
    $prev = $pageNum - 1;
    $next = $pageNum + 1;   
    unset($_GET["pagenum"]);
    
    //displays current page number
    echo "<p id = 'pageNum'>Page $pageNum</p>";

    //if not on final page, display next page option
    if ($pageNum != $totalPages)
    {
        echo "<a class = 'next' href = '?". http_build_query($_GET) ."&pagenum=$next'>Next Page</a>";
    } 
   
    //if not on first page, display previous page option
    if ($pageNum != 1)
    {
        echo "<a class = 'prev' href= '?". http_build_query($_GET) ."&pagenum=$prev'>Previous Page</a>";
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

