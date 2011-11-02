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
<script type="text/javascript">
$(document).ready(function() {
  $('tr:nth-child(odd)').addClass('odd');
});
</script>

  <?php
  //setup page variables
  //todo: better navigation between searches
    if (isset($_REQUEST['sel_years'])){
      $years = filter_var_array($_REQUEST['sel_years'],FILTER_SANITIZE_STRING);
    }
    else {
      //page resubmit, use previous years from last search
      if (isset($_REQUEST['hdn_Years'])){
        $years = explode(",", filter_var($_REQUEST['hdn_Years'],FILTER_SANITIZE_STRING));
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
    $tmpParentIDList = filter_var_array($_REQUEST['sel_programs'], FILTER_SANITIZE_SPECIAL_CHARS);
  }
  else {
    //page resubmit and not using select control, use saved parentIDs
    if (isset($_REQUEST['hdn_ParentIDs'])){
      $tmpParentIDList = explode(",", filter_var($_REQUEST['hdn_ParentIDs'], FILTER_SANITIZE_SPECIAL_CHARS));
    }
    //initial visit to the page
    else $tmpParentIDList = array(0);
  }

  //get program_type
  if (isset($_REQUEST['sel_programType'])){
    $tmpProgramType = filter_var($_REQUEST['sel_programType'],FILTER_SANITIZE_NUMBER_INT);
  }
  else {
    //page resubmit and not using select control, use saved programType
    if (isset($_REQUEST['hdn_programType'])){
      $tmpProgramType = filter_var($_REQUEST['hdn_programType'], FILTER_SANITIZE_NUMBER_INT);
    }
    //initial visit to the page
    else $tmpProgramType = 0;
  }

//get chart_type
  if (isset($_REQUEST['sel_chart'])){
    $tmpChartTypeID = filter_var($_REQUEST['sel_chart'], FILTER_SANITIZE_NUMBER_INT);
  }
  else {
    //page resubmit and not using select control, use saved programType
    if (isset($_REQUEST['hdn_chartType'])){
      $tmpChartTypeID = filter_var($_REQUEST['hdn_chartType'], FILTER_SANITIZE_NUMBER_INT);
    }
    //initial visit to the page
    else $tmpChartTypeID = 0;
  }

  switch ($tmpChartTypeID){
    case 0:
      //table
      $tmpFileInclude = "includes/tabular.php";
      break;
    case 1:
      //pie
      $tmpFileInclude = "includes/pie.php";
      break;
    case 2:
      //line
      $tmpFileInclude = "includes/line_column.php";
      $tmpChartType = "line";
      break;
    case 3:
      //column
      $tmpFileInclude = "includes/line_column.php";
      $tmpChartType = "column";
      break;
  }
?>


</head>
<body >

<!--stopindex-->


<div id="outer" >
  <div id="eere_header">
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
  <div class="<?php if($bln3Colmode){ echo "grid_4"; } else {echo "grid_7";} ?>" id="maincontent">
    <h1><?php echo $pageHeadline ?></h1>
      <p>Make a selection below to see the EERE Program budgeting information and archives.</p>
<div id="form_container">
<form id="frm_budget" name="frm_budget" action="program_budget_formulation.php" method="post">
  <?php
    printf ("<input type=\"hidden\" value=\"%s\" name=\"hdn_ParentIDs\"/>", htmlspecialchars(implode(",",$tmpParentIDList)));
    printf ("<input type=\"hidden\" value=\"%s\" name=\"hdn_Years\"/>", htmlspecialchars(implode(",",$years)));
    printf ("<input type=\"hidden\" value=\"%s\" name=\"hdn_programType\"/>", htmlspecialchars($tmpProgramType));
    printf ("<input type=\"hidden\" value=\"%s\" name=\"hdn_chartType\"/>", htmlspecialchars($tmpChartTypeID));
    printf ("<input type=\"hidden\" name=\"submit\"/>");
  //only present the form if only one program has been selected
  if (count($tmpParentIDList) == 1){
  ?>
    <div id="program_select">
<?php

    //create program select control
    $result = returnProgramListing($tmpParentIDList[0], $tmpProgramType);
    printf("<div>%s</div>",createProgramSelect("programID",
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
      <label for="sel_years">Fiscal Year(s)</label>
      <select name="sel_years[]" id="sel_years" <?php if ($tmpChartTypeID <> 1) echo "size=\"5\" multiple=\"multiple\"";?>>
        <?php if ($tmpChartTypeID <> 1) { ?>
        <option value="14">10 Year Period</option>
        <option value="9">5 Year Period</option>
        <option value="7">3 Year Period</option>
  <?php
      }
      foreach(getFiscalYears(0) as $possibleYears){
        echo "<option ";
        if (in_array($possibleYears, $years)) echo "selected=\"selected\" ";
        echo "value=\"" . $possibleYears . "\">" . $possibleYears . "</option>\n";
      }
      ?>
      </select>
    </div>
  <?php
    //only show program type at top level
    if (count($tmpParentIDList) == 1 and $tmpParentIDList[0] == 0){
    ?>
    <div id="program_type_select">
      <label for="sel_programType">EERE Program Type</label>
      <select name="sel_programType" id="sel_programType">
        <option <?php if ($tmpProgramType == 0) echo " selected=\"selected\" ";?>value="0">All EERE Program Types</option>
        <option <?php if ($tmpProgramType == 1) echo " selected=\"selected\" ";?>value="1">Renewable</option>
        <option <?php if ($tmpProgramType == 2) echo " selected=\"selected\" ";?>value="2">Efficiency</option>
        <option <?php if ($tmpProgramType == 3) echo " selected=\"selected\" ";?>value="3">Corporate</option>
        <option <?php if ($tmpProgramType == 4) echo " selected=\"selected\" ";?>value="4">Compare All</option>
      </select>
    </div>
    <?php } ?>
    <div id="chart_select">
      <label for="sel_chart">Display Type</label>
      <select name="sel_chart" id="sel_chart">
        <option <?php if ($tmpChartTypeID == 0) echo " selected=\"selected\" ";?>value="0">Tabular</option>
        <option <?php if ($tmpChartTypeID == 1) echo " selected=\"selected\" ";?>value="1">Pie</option>
        <option <?php if ($tmpChartTypeID == 2) echo " selected=\"selected\" ";?>value="2">Line</option>
        <option <?php if ($tmpChartTypeID == 3) echo " selected=\"selected\" ";?>value="3">Column</option>
      </select>
    </div>


  <input type="button" value="Reset" onclick="javascript:document.location.href='program_budget_formulation.php';" class="controlbutton"/>
  <input type="submit" value="Submit" name="submit" class="controlbutton"/>
  <?php if (isset($_POST['submit'])) { ?>
  <input type="button" value="&laquo; Back" onclick="javascript:window.history.back();" class="controlbutton"/>
  <?php } ?>
    </form>
  </div>

<?php include($tmpFileInclude); ?>

  </div>
<!--stopindex-->
</div>
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