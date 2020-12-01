<!-- ######################     Main Navigation   ########################## -->
<nav class="nav">
        <?php
        if (PATH_PARTS['filename'] != 'LoginAndRegistration') {
            print '<ol>' . PHP_EOL . '<li ';
            if (PATH_PARTS['filename'] == 'index') {
                print ' class="activePage" ';
            }
            print "><a href='index.php'>Students' classes</a></li>";

            print '<li ';
            if (PATH_PARTS['filename'] == 'subjects') {
                print ' class="activePage" ';
            }
            print "><a href='subjects.php'>Subjects</a></li>";
            print PHP_EOL.'</ol>';
        }
        ?>
</nav>
<!-- #################### Ends Main Navigation    ########################## -->

