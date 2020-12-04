<!-- ######################     Main Navigation   ########################## -->
<nav id="nav">
        <?php
            print "<input type='checkbox' />";
            print "<p></p>";
            print "<p></p>";
            print '<ul class="menu';
            if ((PATH_PARTS['filename'] == 'homepage') ||(PATH_PARTS['filename'] == 'addProducts')
                ||(PATH_PARTS['filename'] == 'shop') ||(PATH_PARTS['filename'] == 'setting')) {
                print '">' . PHP_EOL . '<li ';
                if (PATH_PARTS['filename'] == 'homepage') {
                    print ' class="activePage" ';
                }
                print "><a href='homepage.php'>Home</a></li>";

                print '<li ';
                if (PATH_PARTS['filename'] == 'addProducts') {
                    print ' class="activePage" ';
                }
                print "><a href='addProducts.php'>Add Products</a></li>";

                print '<li ';
                if (PATH_PARTS['filename'] == 'shop') {
                    print ' class="activePage" ';
                }
                print "><a href='shop.php'>Shopping</a></li>";

                print '<li ';
                if (PATH_PARTS['filename'] == 'setting') {
                    print ' class="activePage" ';
                }
                print "><a href='setting.php'>Setting</a></li>";

                print '<li ';
                if (PATH_PARTS['filename'] == 'logout') {
                    print ' class="activePage" ';
                }
                print "><a href='logout.php'>Log Out</a></li>";
            }else{
                print ' index">' . PHP_EOL . '<li ';
                if (PATH_PARTS['filename'] == 'form') {
                    print ' class="activePage" ';
                }
                print "><a href='form.php'>Home</a></li>";

                print '<li ';
                if (PATH_PARTS['filename'] == 'about') {
                    print ' class="activePage" ';
                }
                print "><a href='about.php'>About</a></li>";
            }
            print PHP_EOL.'</ul>';
        ?>
</nav>
<!-- #################### Ends Main Navigation    ########################## -->

