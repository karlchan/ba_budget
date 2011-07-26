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

  //get program_type
  if (isset($_REQUEST['sel_programType'])){
    $tmpProgramType = $_REQUEST['sel_programType'];
  }
  else {
    //page resubmit and not using select control, use saved programType
    if (isset($_REQUEST['hdn_programType'])){
      $tmpProgramType = $_REQUEST['hdn_programType'];
    }
    //initial visit to the page
    else $tmpProgramType = 0;
  }
?>
  <form id="frm_budget" name="frm_budget" action="" method="post">
  <?php
    printf ("<input type=\"hidden\" value=\"%s\" name=\"hdn_ParentIDs\"/>", htmlspecialchars(implode(",",$tmpParentIDList)));
    printf ("<input type=\"hidden\" value=\"%s\" name=\"hdn_Years\"/>", htmlspecialchars(implode(",",$years)));
    printf ("<input type=\"hidden\" value=\"%s\" name=\"hdn_programType\"/>", htmlspecialchars($tmpProgramType));
  //only present the form if only one program has been selected
  if (count($tmpParentIDList) == 1){
  ?>
    <div id="program_select">
<?php

    //create program select control
    $result = returnProgramListing($tmpParentIDList[0], $tmpProgramType);
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
    <?php
    //only show program type at top level
    if (count($tmpParentIDList) == 1 and $tmpParentIDList[0] == 0){
    ?>
    <div id="program_type_select">
      <label for="sel_programType">EERE Program Type</label><br/>
      <select name="sel_programType" id="sel_programType">
        <option <?php if ($tmpProgramType == 0) echo " selected ";?>value="0">All EERE Program Types</option>
        <option <?php if ($tmpProgramType == 1) echo " selected ";?>value="1">Renewable</option>
        <option <?php if ($tmpProgramType == 2) echo " selected ";?>value="2">Efficiency</option>
        <option <?php if ($tmpProgramType == 3) echo " selected ";?>value="3">Corporate</option>
      </select>
    </div>
    <?php } ?>
    <div id="chart_select">
      <label for="sel_chart">Display Type</label><br/>
      <select name="sel_chart" id="sel_chart">
        <option value="0">Tabular</option>
        <option value="1">Pie</option>
        <option value="2">Line</option>
        <option value="3">Column</option>
      </select>
    </div>
    <input type="submit" value="Submit"/>
  </form>
<?php



?>

<script type="text/javascript" src="includes/highcharts/modules/exporting.js"></script>


		<!-- 2. Add the JavaScript to initialize the chart on document ready -->
		<script type="text/javascript">

			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container',
						defaultSeriesType: 'line',
						marginRight:  25
					},
					title: {
						text: '<?php
                      if (count($tmpParentIDList) == 1) {
                        if ($tmpParentIDList[0] == 0) $tmpParentName = "EERE Budget";
                        else $tmpParentName = getProgramName($tmpParentIDList[0]);
                      }
                      //get the parent program, just send the first programID

                    else {
                      $tmpParentName = getParentProgramName($tmpParentIDList[0]);
                      if ($tmpParentName == "") $tmpParentName = "EERE Budget";
                    }
                    echo $tmpParentName;?>',
						x: -20 //center
					},
          subtitle: {
						text: 'Dollars in Thousands',
						x: -20
					},
					xAxis: {
						categories: ['<?php echo implode("', '", $years);?>']
					},
					yAxis: {
						title: {
							text: 'Dollars in Thousands'
						},
						plotLines: [{
							value: 0,
							width: 1,
							color: '#808080'
						}]
					},
					tooltip: {
						formatter: function() {
				                return '<b>'+ this.series.name +'</b><br/>'+
								this.x +': '+ formatNumber(this.y,0) +' ';
						}
					},
					legend: {
						layout: 'vertical',
						align: 'center',
						verticalAlign: 'bottom',
						borderWidth: 0
					},
					series: [

            <?php
              //for multiple selections
              if (count($tmpParentIDList) > 1) {
                foreach ($tmpParentIDList as $tmpParentID) {
                $result = returnProgramListing($tmpParentID,0);
                echo "{" . "\n";
                    echo "name:'" . getProgramName($tmpParentID) . "'," . "\ndata:[";
                    foreach($years as $year){
                      if (mysql_num_rows($result) > 1) //still on a program that can 'drill down'
                        echo getBudgetRollup_FY_Program(mysql_real_escape_string($tmpParentID), mysql_real_escape_string($year)) . ", ";
                      else //drilled down as far as possible.
                        echo getBudget_FY_Program(mysql_real_escape_string($tmpParentID), mysql_real_escape_string($year)) . ", ";

                    }
                  echo "]\n} ," . "\n";
                }
              }
              else {
                //drill down, only one program selected

                  //get subprograms for the selected program.
                  //use programType only if at top level EERE
                if ($tmpParentIDList[0] == 0){
                  $result = returnProgramListing($tmpParentIDList[0], $tmpProgramType);
                }
                else $result = returnProgramListing($tmpParentIDList[0],0);
                   while($row = mysql_fetch_array($result)) {
                    echo "{" . "\n";
                    echo "name:'" . $row['program_name'] . "'," . "\ndata:[";
                    foreach($years as $year){
                      if ($tmpParentIDList[0] == 0){
                        //get rollup for parent level
                        echo getBudgetRollup_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($year)) . ", ";
                      }
                      else {
                        //get budget number for subprogram
                        echo getBudget_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($year)) . ", ";
                      }
                    }
                    echo "]\n} ," . "\n";
                }

              }
          ?>]


				});


			});

		</script>


  <div id="container" style="width: 100%; height: 700px; clear:both;"></div>

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