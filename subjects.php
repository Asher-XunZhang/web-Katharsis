<?php
include 'top.php';

$subjSql="SELECT DISTINCT ` Subj` FROM `tblEnrollments` ORDER BY `tblEnrollments`.` Subj` ASC";
$subjNumSql = 0;
if ($thisDatabaseReader->querySecurityOk($subjSql,0,1,0,0,0)) {
    $thisDatabaseReader->sanitizeQuery($subjSql);
    $subjInfos = $thisDatabaseReader->select($subjSql, '');
    $subjNum = count($subjInfos,0);
}

print "<main>".PHP_EOL."<article>".PHP_EOL;
print "<h2>Select a subject from these-- ".$subjNum." --classes</h2>";
foreach ($subjInfos as $subjInfo){
    print "<a href='classes.php?subj=".$subjInfo[' Subj']."'>".$subjInfo[' Subj']."</a>".PHP_EOL;
}

print "</article>".PHP_EOL."</main>";
include 'footer.php';
print "</body>".PHP_EOL."</html>"
?>