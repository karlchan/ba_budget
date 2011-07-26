<?php
/**
 * User: karl
 * Date: 6/30/11
 * Time: 9:26 AM
 *
 */
include("includes/phpfunctions.php");
include("includes/templateGlobals.php");

//Page specific variables
$strLastUpdated = "6/30/2011";
$strPageID = "70001";
$strProgramName = PROGRAM_NAME;
$bln3Colmode = false;
$current_FY = "2010";
$pageHeadline = "EERE Budget Formulation"
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
    page_title : '<?php echo $pageHeadline ?>'
};
</script>

<?php include("includes/head_code.php");?>

<title>Planning, Budget and Analysis: <?php echo $pageHeadline ?></title>

<meta name="cms_id" content="<?php echo $strPageID;?>" />
<meta name="upd_date" content="<?php echo $strLastUpdated;?>" />
<meta name="lnav_name" content="Budget Archives" />



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
      <!--<li id="print"><a id="printversion" href="/ba/printable_versions/about.html">Printable Version</a></li>-->
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
    <h1><?php echo $pageHeadline ?></h1>
      <p>Make a selection below to see the EERE Programs.</p>

<?php
  //setup page variables
  //todo: sanitize all request vars
  //todo: better navigation between searches
    if (isset($_REQUEST['sel_years'])){
      $years = $_REQUEST['sel_years'];
    }
    else {
      //page resubmit, use previous years from last search
      if (isset($_REQUEST['hdn_Years'])){
        $years = explode(",", $_REQUEST['hdn_Years']);
      }
      //first time on page
      else $years = getFiscalYears(7);
    }
    //Not so elegant way of forcing the FY period terms
    if (in_array("14", $years)) $years = getFiscalYears(14);
    if (in_array("9", $years)) $years = getFiscalYears(9);
    if (in_array("7", $years)) $years = getFiscalYears(7);


  //get top level programs
  //Use the select control to populate programs
  if (isset($_REQUEST['sel_programs'])){
    $tmpParentIDList = $_REQUEST['sel_programs'];
  }
  else {
    //page resubmit and not using select control, use saved parentIDs
    if (isset($_REQUEST['hdn_ParentIDs'])){
      $tmpParentIDList = explode(",", $_REQUEST['hdn_ParentIDs']);
    }
    //initial visit to the page
    else $tmpParentIDList = array(0);
  }
?>
  <form id="frm_budget" name="frm_budget" action="" method="post">
  <?php
    printf ("<input type=\"hidden\" value=\"%s\" name=\"hdn_ParentIDs\"/>", htmlspecialchars(implode(",",$tmpParentIDList)));
    printf ("<input type=\"hidden\" value=\"%s\" name=\"hdn_Years\"/>", htmlspecialchars(implode(",",$years)));
  //only present the form if only one program has been selected
  if (count($tmpParentIDList) == 1){
  ?>
    <div id="program_select">
<?php

    //create program select control
    $result = returnProgramListing($tmpParentIDList[0]);
    printf("<div>%s</div>",createSelect("programID",
                                        "program_name",
                                        "sel_programs[]",
                                        "Programs",
                                        $result,
                                        "multiple",
                                        5 ,
                                        "All EERE Programs",0)
    );
?>
    </div>
    <?php } ?>
    <div id="year_select">
      <label for="sel_years[]">Fiscal Year(s)</label><br/>
      <select name="sel_years[]" id="sel_years[]" size="5" multiple>
        <option value="14">10 Year Period</option>
        <option value="9">5 Year Period</option>
        <option value="7">3 Year Period</option>
      <?php
      foreach(getFiscalYears(0) as $possibleYears){
        echo "<option ";
        if (in_array($possibleYears, $years)) echo "selected ";
        echo "value=\"" . $possibleYears . "\">" . $possibleYears . "</option>\n";
      }
      ?>
      </select>
    </div>
    <input type="submit" value="Submit"/>
  </form>
<?php
  //Loop through each program that has been selected
  foreach ($tmpParentIDList as $tmpParentID) {

  //get parent name for subprograms
  $tmpParentName = getProgramName($tmpParentID);
  $result = returnProgramListing($tmpParentID);
?>

<?php

//Display table
    //if (mysql_num_rows($result) > 0) {
      //reset result set pointer to beginning
      //mysql_data_seek($result, 0);
    //}
  echo "<br/><p style=\"clear:both;\">* dollars in thousands</p> \n
      <table class=\"data\" width='100%'>";
  if ($tmpParentName <> ""){
    printf("<caption>%s</caption>", $tmpParentName);
  }
  echo "<tr>
    <th>EERE Program Area</th>";
  foreach ($years as $year) {
    printf("<th>FY %s</th>", $year);
  }
  echo "</tr>";

  while($row = mysql_fetch_array($result)) {
      echo "<tr>" . "\n";
      echo "<td>" . $row['program_name'] . "</td>" . "\n";
      foreach($years as $year){
        if ($tmpParentID == 0){
          //get rollup for parent level
          echo "<td style=\"text-align:right; vertical-align:bottom;\">" . number_format(getBudgetRollup_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($year))) . "</td>" . "\n";
        }
        else {
          //get budget number for subprogram
          echo "<td style=\"text-align:right; vertical-align:bottom;\">" . number_format(getBudget_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($year))) . "</td>" . "\n";
        }
      }
      echo "</tr>" . "\n";
  }
  //if there are multiple results, show the totals row
  if (mysql_num_rows($result) > 1) {
    echo "<tr><td style=\"text-align:right; vertical-align:bottom;\"><strong>EERE Totals:</strong></td>\n";
      foreach($years as $year){
        if ($tmpParentID == 0){
          //Totals for ALL EERE
          echo "<td style=\"text-align:right; vertical-align:bottom;\">" . number_format(getBudgetRollup_FY_Program(0, mysql_real_escape_string($year),true)) . "</td>" . "\n";
        }
        else {
          //Totals for Subprogram
          echo "<td style=\"text-align:right; vertical-align:bottom;\">" . number_format(getBudgetRollup_FY_Program(mysql_real_escape_string($tmpParentID), mysql_real_escape_string($year))) . "</td>" . "\n";
        }
      }
    echo "</tr>\n";
  }
echo "</table>\n";
} //end foreach loop
?>
  </div>
<!--stopindex-->
</div>

<div id="footer">
  <?php include("includes/footer.php");?>
</div>

<script language="JavaScript" src="/includes/nrel_eere.js" type="text/javascript"></script>
<script language="JavaScript" src="/includes/countpdfs.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">set_printable_version();</script>
<?php
  mysql_close($con);
  ?>
<!--startindex-->
</body>
</html>