<?php
session_start();
include_once "top.php";
$products = array();
$categories = array();
$certainCategory = "All";
$setCategory = false;
$delproducts = array();
if (isset($_SESSION["UserId"])&&isset($_SESSION["Username"])){
    try{
        $UserId = $_SESSION["UserId"];
        if(DEBUG){
            print "<p>sql " . $displayAllQuery;
            print"<p><pre>";
            print_r($UserId);
            print"</pre></p>";
        }

        if ($thisDatabaseReader->querySecurityOk($displayCateQuery,1)){
            $displayCateQuery=$thisDatabaseReader->sanitizeQuery($displayCateQuery);
            $categories = $thisDatabaseReader->select($displayCateQuery, array($UserId));
        }
        if(($_SERVER["REQUEST_METHOD"] == 'POST')&&(getPData("Execute")=="Execute")){
            $certainCategory = getPData("Category");
            $delproducts = getPData("delPros");
            if(isset($delproducts)&&(!empty($delproducts))){
                if ($thisDatabaseWriter->querySecurityOk($deleteProQuery,1,0,0,0,0)) {
                    $deleteProQuery = $thisDatabaseWriter->sanitizeQuery($deleteProQuery);
                    foreach ($delproducts as $delproduct){
                        $delresults = $thisDatabaseWriter->delete($deleteProQuery, array($delproduct));
                    }
                }
            }
            if(isset($certainCategory)){
                if(($certainCategory!="All")){
                    if ($thisDatabaseReader->querySecurityOk($displayProOfCatQuery,1,1,0,0,0)) {
                        $displayProOfCatQuery = $thisDatabaseReader->sanitizeQuery($displayProOfCatQuery);
                        $products = $thisDatabaseReader->select($displayProOfCatQuery, array($UserId,$certainCategory));
                    }
                }
            }
        }
        if((!isset($products)) || empty($products)){
            if ($thisDatabaseReader->querySecurityOk($displayAllQuery,1)) {
                $displayAllQuery=$thisDatabaseReader->sanitizeQuery($displayAllQuery);
                $products = $thisDatabaseReader->select($displayAllQuery, array($UserId));
            }
        }
    } catch (PDOExecption $e) {
        if (DEBUG) print "Error!: " . $e->getMessage() . "</br>";
        $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
    }
}
?>
<main>
    <article>
        <form action="homepage.php" method="POST">
            <fieldset class="listbox">
                <legend>Select the category You want to check</legend>
                <p><input class = "execute" type = "submit" name="Execute" value="Execute"></p>
                <p>
                    <select class="category"
                            name="Category"
                    >
            <?php
            print "<option value='All' selected hidden>select-a-category(optional)</option>".PHP_EOL;
            foreach ($categories as $category){
                print "<option value='".$category["Category"]."'>";
                print $category["Category"]."</option>".PHP_EOL;
            }
            print"</select>".PHP_EOL."</p>".PHP_EOL."</fieldset>".PHP_EOL;
            print "<h1>";
            print $certainCategory." Products";
            print "</h1>";
            print "<table>".PHP_EOL;
                print "<tr>";
                    print "<th>Delete</th>";
                    print "<th>Name</th>";
                    print "<th>Category</th>";
                    print "<th>Update time</th>";
                    print "<th>Price</th>";
                    print "<th>Quantity</th>";
                    print "<th>Description</th>";
                    print "</tr>";
                foreach ($products as $product) {
                    print "<tr>";
                    print "<td><input type=checkbox name='delPros[]' value=".$product["Id"].">"."</td>";
                    print "<td>".$product["Name"]."</td>";
                    print "<td>".$product["Category"]."</td>";
                    print "<td>".$product["Time"]."</td>";
                    print "<td>".$product["Price"]."</td>";
                    print "<td>".$product["Quantity"]."</td>";
                    print "<td>".$product["Description"]."</td>";
                    print "</tr>";
                }
            print "</table>".PHP_EOL;
        ?>
        </form>
    </article>
</main>
<?php
    include "footer.php";
    print "</body>".PHP_EOL."</html>";
?>