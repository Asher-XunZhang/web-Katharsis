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

$buyProducts = array();
$deleteInfo = false;
$deleteNum = 0;

if (isset($_SESSION["UserId"])&&isset($_SESSION["Username"])){
    $UserId = $_SESSION["UserId"];
    if(DEBUG){
        print "<p>sql " . $displayAllOtrQuery;
        print"<p><pre>";
        print_r($UserId);
        print"</pre></p>";
    }
    if ($thisDatabaseReader->querySecurityOk($displayOtrCateQuery,1, 1)){
        $displayOtrCateQuery=$thisDatabaseReader->sanitizeQuery($displayOtrCateQuery);
        $categories = $thisDatabaseReader->select($displayOtrCateQuery, array($UserId));
    }
    if(($_SERVER["REQUEST_METHOD"] == 'POST')){
        //TODO: implement the shopping function including select products to shopping cart and
        //      modify the information in the "tblOrderRecord" table;
//        if(isset($_POST["btn-buy"])){
//            if(isset($_POST["buyPros"])){
//                $buyProducts = getPData("buyPros");
//                try{
//                    if(isset($buyProducts)&&(!empty($buyProducts))){
//                        $thisDatabaseWriter->db->beginTransaction();
//                        if ($thisDatabaseWriter->querySecurityOk($buyProQuery,1,0,0,0,0)) {
//                            $buyProQuery = $thisDatabaseWriter->sanitizeQuery($buyProQuery);
//                            foreach ($buyProducts as $buyProduct){
//                                $buyResults = $thisDatabaseWriter->delete($buyProQuery, array($buyProduct));
//                                $buyNum++;
//                            }
//                        }
//                        $dataBought = $thisDatabaseWriter->db->commit();
//                    }
//                    if ($dataBought) $buyInfo=true;
//                } catch (PDOExecption $e) {
//                    $thisDatabaseWriter->db->rollback();
//                    if (DEBUG) print "Error!: " . $e->getMessage() . "</br>";
//                    $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
//                }
//            }
//        }
        //TODO: change the css style of selected products and show the

        if(isset($_POST["btn-category"])){
            $Category = getPData("Category");
            if(deep_in_array($Category,$categories)){
                if ($thisDatabaseReader->querySecurityOk($displayOtrProOfCatQuery,1,2)) {
                    $displayOtrProOfCatQuery = $thisDatabaseReader->sanitizeQuery($displayOtrProOfCatQuery);
                    $products = $thisDatabaseReader->select($displayOtrProOfCatQuery, array($UserId,$Category));
                }
            }
        }
    }
    if(empty($products)) {
        if ($thisDatabaseReader->querySecurityOk($displayAllOtrQuery, 1, 1)) {
            $displayAllOtrQuery = $thisDatabaseReader->sanitizeQuery($displayAllOtrQuery);
            $products = $thisDatabaseReader->select($displayAllOtrQuery, array($UserId));
        }
    }
}
?>
<main>
    <article>
        <form class="category-content" action="shop.php" method="Post">
<!--            --><?php //if($deleteInfo){
//                echo "<h2>Delete $deleteNum Products Successfully!</h2>";
//            }?>
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

        <form class="product-content" action="shop.php" method="POST">
            <input class = "btn-buy" type = "submit" name="btn-buy" value="Buy">
            <input class = "btn-cart" type = "submit" name="btn-cart" value="Cart">
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
include_once "footer.php";
print "</body>"."</html>";
?>