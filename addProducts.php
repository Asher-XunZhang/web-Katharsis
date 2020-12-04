<?php
session_start();
include_once "top.php";
$ProName = "";
$Category = "";
$Price = "";
$Quantity = "";
$Descrition = "";
$upload = false;
$PriceError = false;
$QuantityError = false;
$errorMsg = array();
$PriceInfos = "<li> No letters accepted;</li>".PHP_EOL;
$PriceInfos .= "<li> No negative numbers accepted;</li>".PHP_EOL;
$PriceInfos .= "<li> Two decimal at most;</li>".PHP_EOL;
$PriceInfos .= "<li> Do not accept numbers greater than 1 but starting with 0;</li>".PHP_EOL;

$QuantityInfos = "<li> No letters accepted;</li>".PHP_EOL;
$QuantityInfos .= "<li> Only allow positive integers not starting with 0;</li>".PHP_EOL;

if (isset($_SESSION["UserId"])&&isset($_SESSION["Username"])) {
    $UserId = $_SESSION["UserId"];
    if (DEBUG) {
        print "<p>Some Problems happened...</p>";
        print "<p>Please contact with Email: <i>xzhang@uvm.edu</i></p>";
    }
    if($_SERVER["REQUEST_METHOD"] == 'POST'){
        if(isset($_POST["btn-add"])){
            $upload = true;
            $ProName = getPData("Name");
            $Category = getPData("Category");
            $Price = (float)(getPData("Price"));
            $Quantity = (int)(getPData("Quantity"));
            $Descrition = getPData("Description");

            $ProName = ucwords(strtolower($ProName));
            $Category = ucwords(strtolower($Category));
            $Descrition = ucfirst(strtolower($Descrition));

            if (!checkPrice($Price)){
                $errorMsg[] = "Invalid Price Input. Please follow the tips";
                $PriceError = true;
            }
            if (!checkQuantity($Quantity)){
                $errorMsg[] = "Invalid Quantity Input. Please follow the tips";
                $QuantityError = true;
            }
            if(!$PriceError&&!$QuantityError){
                try{
                    $thisDatabaseWriter->db->beginTransaction();
                    if ($thisDatabaseWriter->querySecurityOk($addProQuery,0)){
                        $addProQuery=$thisDatabaseWriter->sanitizeQuery($addProQuery);
                        $results1 = $thisDatabaseWriter->insert($addProQuery, array($UserId));
                        $productId = $thisDatabaseWriter->lastInsert();
                    }
                    if ($results1&&$productId){
                        if ($thisDatabaseWriter->querySecurityOk($addProInfosQuery,0)){
                            $addProInfosQuery=$thisDatabaseWriter->sanitizeQuery($addProInfosQuery);
                            $results2 = $thisDatabaseWriter->insert($addProInfosQuery,
                                array($productId,$ProName,$Category,$Price,$Quantity,$Descrition));
                        }
                    }
                    $productInsert = $thisDatabaseWriter->db->commit();
                }catch (PDOExecption $e) {
                    $thisDatabaseWriter->db->rollback();
                    if (DEBUG) print "Error!: " . $e->getMessage() . "</br>";
                    $errorMsg[] = "There was a problem with accpeting your data.";
                    $errorMsg[] = "Please contact us directly.";
                }
            }
        }
    }
}
?>
<main>
    <article>
        <form action="addProducts.php" method="POST">
            <?php if($upload){
                if(isset($errorMsg)&&(!empty($errorMsg))){
                    echo "<h1 class='error'>Some errors occurred...</h1>";
                }else{
                    echo "<h1 class='success'>Add Product Successfully!</h1>";
                }
            }else{
                echo '<h1>Add Products</h1>';
            }?>

            <input type="text"
                   value="<?php
                   if($ProName) echo $ProName;
                   ?>"
                   name="Name"
                   placeholder="Product Name"
                   required/>



            <input type="text"
                   value="<?php
                   if($Category) echo $Category;
                   ?>"
                   name="Category"
                   placeholder="Category"
                   required/>


            <section>
                <input type="number" step="0.01" min="0"
                        <?php if($PriceError) print "class='input-mistake' onchange='revise(this)'";?>
                       value="<?php
                       if(!$PriceError) echo $Price;
                       ?>"
                       name="Price"
                       placeholder="Price($)"
                       required/>
                <section class="tooltip tip1">
                    <ul class="tooltiptext tiptext1">Tips<?php echo $PriceInfos; ?></ul>
                </section>
            </section>

            <section>
                <input type="number" step="1" min="1" max="9999"
                        <?php if($QuantityError) print "class='input-mistake' onchange='revise(this)'";?>
                       value="<?php
                       if(!$QuantityError) echo $Quantity;
                       ?>"
                       name="Quantity"
                       placeholder="Quantity(1-9999)"
                       required/>
                <section class="tooltip tip2">
                    <ul class="tooltiptext tiptext2">Tips<?php echo $QuantityInfos; ?></ul>
                </section>
            </section>

            <textarea name="Description"
                      rows="2"
                      cols="30"
                      placeholder="Products Descrition"><?php
                if($Descrition) echo $Descrition ?></textarea>

            <input type="submit" name="btn-add" value="ADD" />
        </form>
    </article>
</main>
<?php
include "footer.php";
print "</body>".PHP_EOL."</html>";
?>
