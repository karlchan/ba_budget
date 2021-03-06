<?php
/**
 * Global template constants
 */

//valid values: green, blue, orange
define("TEMPLATE_COLOR",    "green");

//program template or subsite template: true or false
define("IS_PROGRAM",         true);

//Program Name
define("PROGRAM_NAME",       "Office of EERE");

//External Directory Prefix, used for getting external resources that are not in the root directory on localhost
define("EXT_DIR_PREFIX", "http://www1.eere.energy.gov");

//Root directory structure
define("WEB_ROOT", "/office_eere");

//Search url string
define("SEARCH_STRING", "url:eere.energy.gov/office_eere/");

//Boolean, display top nav?
define("DISPLAY_TOPNAV", true);

//Program Homepage URL
define("SITE_HOME_URL", WEB_ROOT . "/index.html");

/*
 * FY offset, This constant makes sure that the data does not display current FY data that has not yet been determined
 * This number signifies the most current FY stage that HAS data.
  0 = current FY Request
  1 = current FY House
  2 = current FY Senate
  3 = current FY Current Approp.
 */

define("FY_OFFSET", 0);

define("CURRENT_FY", '2015 Request');

define("THREE_YEAR", 4);
define("FIVE_YEAR", 6);
define("TEN_YEAR", 11);

?>