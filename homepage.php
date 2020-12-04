<?php
session_start();
include_once "top.php";

function deep_in_array($value, $array) {
    foreach($array as $item) {
        if(!is_array($item)) {
            if ($item == $value) {
                return true;
            } else {
                continue;
            }
        }
        if(in_array($value, $item)) {
            return true;
        } else if(deep_in_array($value, $item)) {
            return true;
        }
    }
    return false;
}
$products = array();
$categories = array();
$Category = "";

$delproducts = array();
$deleteInfo = false;
$deleteNum = 0;

if (isset($_SESSION["UserId"])&&isset($_SESSION["Username"])){
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
    if(($_SERVER["REQUEST_METHOD"] == 'POST')){
        if(isset($_POST["btn-delete"])){
            if(isset($_POST["delPros"])){
                $delproducts = getPData("delPros");
                try{
                    if(isset($delproducts)&&(!empty($delproducts))){
                        $thisDatabaseWriter->db->beginTransaction();
                        if ($thisDatabaseWriter->querySecurityOk($deleteProQuery,1,0,0,0,0)) {
                            $deleteProQuery = $thisDatabaseWriter->sanitizeQuery($deleteProQuery);
                            foreach ($delproducts as $delproduct){
                                $delresults = $thisDatabaseWriter->delete($deleteProQuery, array($delproduct));
                                $deleteNum++;
                            }
                        }
                        $dataDeleted = $thisDatabaseWriter->db->commit();
                    }
                    if ($dataDeleted) $deleteInfo=true;
                } catch (PDOExecption $e) {
                    $thisDatabaseWriter->db->rollback();
                    if (DEBUG) print "Error!: " . $e->getMessage() . "</br>";
                    $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
                }
            }
        }
        if(isset($_POST["btn-category"])){
            $Category = getPData("Category");
            if(deep_in_array($Category,$categories)){
                if ($thisDatabaseReader->querySecurityOk($displayProOfCatQuery,1,1)) {
                    $displayProOfCatQuery = $thisDatabaseReader->sanitizeQuery($displayProOfCatQuery);
                    $products = $thisDatabaseReader->select($displayProOfCatQuery, array($UserId,$Category));
                }
            }
        }
    }
    if(empty($products)) {
        if ($thisDatabaseReader->querySecurityOk($displayAllQuery, 1)) {
            $displayAllQuery = $thisDatabaseReader->sanitizeQuery($displayAllQuery);
            $products = $thisDatabaseReader->select($displayAllQuery, array($UserId));
        }
    }
}
?>
<main>
    <article>
        <form class="category-content" action="homepage.php" method="Post">
            <?php if($deleteInfo){
                echo "<h2>Delete $deleteNum Products Successfully!</h2>";
            }?>
            <select class="category"
                    name="Category"
            >
                <?php
                    print "<option disabled selected hidden>select-a-category(optional)</option>".PHP_EOL;
                    foreach ($categories as $category){
                        print "<option value='".$category["Category"]."'>";
                        print $category["Category"]."</option>".PHP_EOL;
                    }
                ?>
            </select>
            <input class = "btn-category" type="submit" name="btn-category" value="Go To">
        </form>

        <form class="product-content" action="homepage.php" method="POST">
            <input class = "btn-delete" type = "submit" name="btn-delete" value="Dele">
            <?php
                print "<h1>";
                print $Category." Products";
                print "</h1>";
                print "<table>".PHP_EOL;
                print "<tr>";
                print "<th></th>";
                print "<th>Name</th>";
                print "<th>Category</th>";
                print "<th>Update time</th>";
                print "<th>Price</th>";
                print "<th>Quantity</th>";
                print "<th>Description</th>";
                print "</tr>";
                foreach ($products as $product) {
                    print "<tr>";
                    print "<td><input type=checkbox name='delPros[]' value=".$product["Id"]."><p></p><p></p></td>";
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