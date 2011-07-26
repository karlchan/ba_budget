<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo EXT_DIR_PREFIX; ?>/includes/scripts/jsFunctions.js"></script>
<script type="text/javascript" src="<?php echo EXT_DIR_PREFIX; ?>/includes/scripts/iconreplacement.js"></script>
<link href="<?php echo EXT_DIR_PREFIX; ?>/includes/reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo EXT_DIR_PREFIX; ?>/includes/main_blue.css" rel="stylesheet" type="text/css" />

<script src="includes/highcharts/highcharts.js" type="text/javascript"></script>

<link href="includes/budget.css" rel="stylesheet" type="text/css" />

<meta name="WT.sp" content="/ba/">
<meta name="DCSext.sp1" content="/ba/pba/">

<style type="text/css">
#topnav li{margin-right:17px;}

#tn_home {min-width:67px; width:67px;}
#topnav li#tn_home a{width:67px;}


#tn_about {min-width:98px; width:98px;}
#topnav li#tn_about a{width:98px;}


#tn_planning {min-width:100px; width:100px;}
#topnav li#tn_planning a{width:100px;}


#tn_budgeting {min-width:100px; width:100px;}
#topnav li#tn_budgeting a{width:100px;}


#tn_performance {min-width:200px; width:200px;}
#topnav li#tn_performance a{width:200px;}

#tn_data {min-width:160px; width:160px;}
#topnav li#tn_data a{width:160px;}

</style>
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