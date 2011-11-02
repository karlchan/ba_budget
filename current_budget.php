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

//Page specific variables, see templateGlobals.php for more variables and constant definitions
$strLastUpdated = "6/30/2011";
$strPageID = "70000";
$strProgramName = PROGRAM_NAME;
$bln3Colmode = false;
$current_FY = "2012 Senate";
$pageHeadline = "Fiscal Year " . $current_FY . " Budget";
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
  <div class="<?php if($bln3Colmode){ echo "grid_4"; } else {echo "grid_7";} ?>" id="maincontent">
    <h1><?php echo $pageHeadline ?></h1>
      <p>Below you'll find information on EERE's fiscal year <?php echo $current_FY;?> budget.</p>
      <p>* dollars in thousands</p>
<?php

$sSQL = sprintf("SELECT * FROM program WHERE parentID is NULL");

$result = mysql_query($sSQL);


echo "<table class=\"data\" width='65%' summary='This table displays the EERE fiscal year $current_FY budget for all EERE programs'>
<caption>EERE FY $current_FY Budget</caption>
<tr>
<th scope='col'>EERE Program Area</th>
<th scope='col' style=\"text-align:right;\">$current_FY Budget</th>
</tr>";

while($row = mysql_fetch_array($result))
  {

    $tmpBudget = getBudgetRollup_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($current_FY));

    if ((int)$tmpBudget != 0){
      $programNameArray[] = $row['program_name'];
      $currentFYBudgetArray[] = $tmpBudget;
      echo "<tr>" . "\n";
      echo "<td><a href=\"program_budget_formulation.php?sel_programs[]=". $row['programID'] . "\">" . htmlspecialchars($row['program_name']) . " &raquo;</a></td>" . "\n";
      echo "<td style=\"text-align:right;\">" . number_format($tmpBudget) . "</td>" . "\n";
      echo "</tr>" . "\n";
    }
  }
  printf ("<tr><td style=\"text-align:right;\"><strong><a href=\"program_budget_formulation.php\">EERE Total</a>: </strong></td><td style=\"text-align:right;\">%s</td></tr>", number_format(getBudgetRollup_FY_Program(0, mysql_real_escape_string($current_FY),true)));
  echo "</table>";
  echo "<p>View EERE budget <a href=\"program_budget_formulation.php\">historical funding</a>.</p>";
?>
  <script type="text/javascript">


  //---------------EERE Budget chart-----------------------
  var fy_total = <?php echo getBudgetRollup_FY_Program(0, mysql_real_escape_string($current_FY),true);?>;
  var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'pie_eere_budget',
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false
					},
					title: {
						text: 'Current EERE Budget for FY <?php echo $current_FY;?>'
					},
          credits: {
            enabled:false
          },
          subtitle: {
						text: '* Dollars in Thousands',
						x: -20
					},
					tooltip: {
						formatter: function() {
							return '<strong>'+ this.point.name +'</strong>: ' + formatNumber((this.y/fy_total)*100, 2) + '% ('+ formatNumber(this.y,0) + ')';
						}
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								color: '#000000',
                style: {
                  width: '220px',
                  fontWeight: 'bold'
                },
								connectorColor: '#000000',
								formatter: function() {
									return '<strong>'+ this.point.name +'</strong>';
								}
							}
						}
					},
				    series: [{
						type: 'pie',
						name: 'EERE Budget',
						data: [
            <?php
              for ($i = 0; $i < count($programNameArray); $i++) {
                if ($currentFYBudgetArray[$i] > 0){
                  printf("['%s', %d],\n", $programNameArray[$i], $currentFYBudgetArray[$i]);
                }
              }
            ?>
						]
					}]
				});
			});

</script>

  <div id="pie_eere_budget" style="width: 100%; height: 400px"></div>

  
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