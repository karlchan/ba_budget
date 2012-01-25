<?php
/**
 * Global template constants
 */

//valid values: green, blue, orange
define("TEMPLATE_COLOR",    "green");

//program template or subsite template: true or false
define("IS_PROGRAM",         true);

//Program Name
define("PROGRAM_NAME",       "About the Office of EERE");

//External Directory Prefix, used for getting external resources that are not in the root directory on localhost
define("EXT_DIR_PREFIX", "http://www2.eere.energy.gov");

//Search url string
define("SEARCH_STRING", "url:eere.energy.gov/office_eere/ url:eere.energy.gov/ba/");

//Boolean, display top nav?
define("DISPLAY_TOPNAV", false);

//Program Homepage URL
define("SITE_HOME_URL", "/office_eere/index.html");

/*
 * FY offset, This constant makes sure that the data does not display current FY data that has not yet been determined
 * This number signifies the most current FY stage that HAS data.
  4 = current FY Request
  3 = current FY House
  2 = current FY Senate
  1 = current FY Current Approp.
 */

define("FY_OFFSET", 1);

?>