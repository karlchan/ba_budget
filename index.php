<?php
/**
 * Created by JetBrains PhpStorm.
 * User: karl
 * Date: 6/30/11
 * Time: 9:26 AM
 * To change this template use File | Settings | File Templates.
 */
include("includes/phpfunctions.php");
include("includes/templateGlobals.php");

//Page specific variables
$strLastUpdated = "6/30/2011";
$strPageID = "70000";
$strProgramName = PROGRAM_NAME;
$bln3Colmode = false;
$current_FY = "2010";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />


<script type="text/javascript">
var pageMetaData = {
    pageid : '<?php echo $strPageID?>',
    programname : '<?php echo $strProgramName;?> - Budget',
    last_updated : '<?php echo $strLastUpdated;?>',
    page_title : 'Budget'
};
</script>

<?php include("includes/head_code.php");?>

<title>Planning, Budget and Analysis: Budget</title>

<meta name="cms_id" content="<?php echo $strPageID;?>" />
<meta name="upd_date" content="<?php echo $strLastUpdated;?>" />
<meta name="lnav_name" content="Budget" />



</head>
<body >

<!--stopindex-->


<div id="outer" >
 <?php include("includes/header.php");?>
</div>

<div id="program_header">
  <?php include("includes/program_header.php"); ?>
</div>

<div id="content">
  <div id="utility_line" class="clearfix">
    <div id="breadcrumb"><a href="http://www.eere.energy.gov/">EERE</a> &raquo; <a href="/ba/index.html"><?php echo $strProgramName;?></a> &raquo; Budget</div>

    <ul id="utilities" class="nositemap">
      <li id="print"><a id="printversion" href="/ba/printable_versions/about.html">Printable Version</a></li>
      <li id="share"><!-- AddThis Button BEGIN --><script type="text/javascript">var addthis_config = {"data_track_clickback":true, ui_click:true};</script><a class="addthis_button" href="http://addthis.com/bookmark.php?v=250&amp;username=addthiseere"><img src="http://s7.addthis.com/static/btn/sm-share-en.gif" width="83" height="16" alt="Bookmark and Share" style="border:0"/></a><script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=addthiseere"></script><!-- AddThis Button END --></li>
    </ul>
  </div>

  <div class="grid_1" >
    <ul id="leftnav">
      <?php include("includes/leftnav.php"); ?>
    </ul>
  </div>
<!--startindex-->
  <?php     if($bln3Colmode){ ?>
  <div class="grid_4" id="maincontent">
  <?php     } else { ?>
  <div class="grid_7" id="maincontent">
  <?php     } ?>
    <h1>Fiscal Year Budgeting</h1>
      <p>Below you'll find information on EERE's proposed Fiscal Year 2010 budget.</p>
      <p>* dollars in thousands</p>
<?php

$sSQL = sprintf("SELECT * FROM program WHERE parentID is NULL");

$result = mysql_query($sSQL);

echo "<table class=\"data\" border='1'>
<caption>EERE FY $current_FY Budget</caption>
<tr>
<th>program id</th>
<th>EERE Program Area</th>
<th>$current_FY Budget</th>
</tr>";

while($row = mysql_fetch_array($result))
  {
  echo "<tr>" . "\n";
  echo "<td>" . $row['programID'] . "</td>" . "\n";
  echo "<td>" . $row['program_name'] . "</td>" . "\n";
  echo "<td style=\"text-align:right;\">" . number_format(getBudgetRollup_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($current_FY))) . "</td>" . "\n";
  echo "</tr>" . "\n";
  }
echo "</table>";


?>
  </div>
<!--stopindex-->
</div>

<div id="footer">
  <?php include("includes/footer.php");?>
</div>

<script language="JavaScript" src="<?php echo EXT_DIR_PREFIX; ?>/includes/nrel_eere.js" type="text/javascript"></script>
<script language="JavaScript" src="<?php echo EXT_DIR_PREFIX; ?>/includes/countpdfs.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">set_printable_version();</script>
<?php
  mysql_close($con);
  ?>
<!--startindex-->
</body>
</html>