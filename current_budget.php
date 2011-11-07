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
$strLastUpdated = "11/07/2011";
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

    <ul id="utilities_508" class="nositemap">
      <!--<li id="print"><a id="printversion" href="#">Printable Version</a></li>-->
      <li id="share">
         <ul>
            <li class="addthis_toolbar"><span class="addthis_button_508">Share</span> <span class="invisible">this resource</span>
              <ul class="addthis_list">
                <li>
                  <a class="email" href="http://api.addthis.com/oexchange/0.8/forward/email/offer?username=addthiseere&amp;url=www4.eere.energy.gov/ba/pba/current_budget.php&amp;title=Planning, Budget and Analysis: <?php echo $pageHeadline ?>" target="_blank"><span class="invisible">Send a link to Planning, Budget and Analysis: <?php echo $pageHeadline ?> to someone by </span>E-mail</a>
                </li>
                <li>
                  <a class="facebook" href="http://api.addthis.com/oexchange/0.8/forward/facebook/offer?username=addthiseere&amp;url=www4.eere.energy.gov/ba/pba/current_budget.php&amp;title=Planning, Budget and Analysis: <?php echo $pageHeadline ?>" target="_blank"><span class="invisible">Share Planning, Budget and Analysis: <?php echo $pageHeadline ?> on </span>Facebook</a>
                </li>
                <li>
                  <a class="twitter" href="http://api.addthis.com/oexchange/0.8/forward/twitter/offer?username=addthiseere&amp;url=www4.eere.energy.gov/ba/pba/current_budget.php&amp;title=Planning, Budget and Analysis: <?php echo $pageHeadline ?>" target="_blank"><span class="invisible">Tweet about Planning, Budget and Analysis: <?php echo $pageHeadline ?> on </span>Twitter</a>
                </li>
                <li>
                  <a class="google" href="http://api.addthis.com/oexchange/0.8/forward/google/offer?username=addthiseere&amp;url=www4.eere.energy.gov/ba/pba/current_budget.php&amp;title=Planning, Budget and Analysis: <?php echo $pageHeadline ?>" target="_blank"><span class="invisible">Bookmark Planning, Budget and Analysis: <?php echo $pageHeadline ?> on </span>Google</a>
                </li>
                <li>
                  <a class="delicious" href="http://api.addthis.com/oexchange/0.8/forward/delicious/offer?username=addthiseere&amp;url=www4.eere.energy.gov/ba/pba/current_budget.php&amp;title=Planning, Budget and Analysis: <?php echo $pageHeadline ?>" target="_blank"><span class="invisible">Bookmark Planning, Budget and Analysis: <?php echo $pageHeadline ?> on </span>Delicious</a>
                </li>
                <li>
                  <a class="digg" href="http://api.addthis.com/oexchange/0.8/forward/digg/offer?username=addthiseere&amp;url=www4.eere.energy.gov/ba/pba/current_budget.php&amp;title=Planning, Budget and Analysis: <?php echo $pageHeadline ?>" target="_blank"><span class="invisible">Rank Planning, Budget and Analysis: <?php echo $pageHeadline ?> on </span>Digg</a>
                </li>
                <li>
                  <a class="more" href="http://api.addthis.com/oexchange/0.8/offer?username=addthiseere&amp;url=www4.eere.energy.gov/ba/pba/current_budget.php&amp;title=Planning, Budget and Analysis: <?php echo $pageHeadline ?>" target="_blank"><span class="invisible">Find </span>More<span class="invisible"> places to share Planning, Budget and Analysis: <?php echo $pageHeadline ?> on AddThis.com&#8230;</span></a>
                </li>
              </ul>
            </li>
         </ul>
      </li>
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
      //KC: removing subprogram navigation: echo "<td><a href=\"program_budget_formulation.php?sel_programs[]=". $row['programID'] . "\">" . htmlspecialchars($row['program_name']) . " &raquo;</a></td>" . "\n";
      echo "<td>" . htmlspecialchars($row['program_name']) . "</td>" . "\n";
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
                  width: 220,
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

<script language="JavaScript" src="<?php echo EXT_DIR_PREFIX; ?>/includes/nrel_eere.js" type="text/javascript"></script>
<?php
  mysql_close($con);
  ?>
<!--startindex-->
</body>
</html>