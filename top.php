<?php
//parse the url into htmlentities to remove any suspicous vales that someone
//may try to pass in. htmlentities helps avoid security issues.
$phpSelf = htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, "UTF-8");

//break the url up into an array, then pull out just the filename as shown
//when I print the body element line 32
$path_parts = pathinfo($phpSelf);
//make sure you have no blank lines before or after this php block
$css_LAR = "../css_folder/LoginAndRegistration.css";
$css_others = "../css_folder/custom.css";

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
        if ($path_parts['filename'] == "index") {
            print "<title>Students' Classes</title>";
        }
        if ($path_parts['filename'] == "subjects") {
            print "<title>Subjects</title>";
        }
        if ($path_parts['filename'] == "classes") {
            print "<title>Classes</title>";
        }

        ?>
        <meta charset="utf-8">
        <meta name="author" content="Xun Zhang">
        <meta name="description" content="read this: http://moz.com/learn/seo/meta-description ">
        <!-- see: http://webdesign.tutsplus.com/tutorials/htmlcss-tutorials/quick-tip-dont-forget-the-viewport-meta-tag/ -->
        <link rel="stylesheet" href=<?php
            if ($path_parts['filename'] == "LoginAndRegistration") {
                print $css_LAR;
            }
            else{
                print $css_others;
            }

        ?> type="text/css" media="screen">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;900&display=swap" rel="stylesheet">
        <script type="text/javascript" src="../javascript/LoginAndRegistration.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php
        $debug = false;
        // This if statement allows us in the classroom to see what our variables are
        // This is NEVER done on a live site
        if (isset($_GET["debug"])) {
            $debug = true;
        }
        $domain = "//";
        $server = htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES, 'UTF-8');
        $domain .= $server;
        if ($debug) {
            print '<p>php Self: ' . $phpSelf;
            print '<pdomain: ' . $domain;
            print '<p>Path Parts<pre>';
            print_r($path_parts);
            print '</pre></p>';
        }
        // %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
        //
        // inlcude all libraries.
        //
        // %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
        print PHP_EOL . '<!-- begin including libraries -->' . PHP_EOL;
        require_once 'lib/security.php';
        include_once 'lib/validation-functions.php';
        include_once 'lib/mail-message.php';

        include_once 'lib/constants.php';
        include_once 'lib/sql.php';
        include_once LIB_PATH . '/Connect-With-Database.php';

        print  PHP_EOL . '<!-- finished including libraries -->' . PHP_EOL;
        ?>
    </head>

    <!-- **********************     Body section      ********************** -->
    <?php
        print '<body id="' . $path_parts['dirname'] . '">'.PHP_EOL;
        print '<!-- ############################ Start of Body ######################### -->';
        include 'header.php';
        print  PHP_EOL;
        include 'nav.php';
        print  PHP_EOL;
        if ($debug) {
            print '<p>DEBUG MODE IS ON</p>';
        }
        print "<!-- End of top.php -->";
    ?>



