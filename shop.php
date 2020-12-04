<?php
include_once "top.php";
?>
<main>
    <article>
        <h1>So sorry that due to time, this page has not yet been developed.</h1>
        <p>The shop page mainly implements online commodity transactions between users. Under this page, you can check the required products and quantities to add them to the shopping cart. In the shopping cart, you can delete the items to be purchased and change the quantity of the purchased items. The final total price will be displayed at the bottom of the shopping cart. There are two buttons at the bottom: "Change" and "Submit". Click "Change" to change the shopping cart product settings; click "Submit" to automatically add the product to the user's "waiting for delivery" page.Then, deduct the user's balance according to the price, increase the corresponding amount for the merchant, and reduce the corresponding The quantity of the product. If the user does not provide an address, a pop-up box will pop up to collect the user's address and add the address to the user's personal information table.</p>
        <p>The setting page mainly implements the user’s personal information change, including username, password, email, name, gender, telephone number, ssn, and address. After the information is changed, the webpage will automatically send a confirmation email to the email (if the email is changed, then send To the new email). The user information is successfully modified after clicking the confirmation link, otherwise the user information will not be changed.</p>
    </article>
</main>
<?php
include_once "footer.php";
print "</body>"."</html>";
?>