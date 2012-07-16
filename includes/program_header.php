  <div id="program_header_content<?php if(!DISPLAY_TOPNAV){echo "_nonav";}?>">
    <div id="searchbox">
      <form name="seek" method="get" action="http://search.nrel.gov/query.html">
        <label for="search"><?php echo $strProgramName;?></label>
        <input type="hidden" name="qp" value="<?php echo SEARCH_STRING; ?>" />
        <input type="hidden" name="style" value="eere" />
        <input type="hidden" name="qs" value="" />
        <input type="hidden" name="qc" value="eren" />

        <input type="hidden" name="ws" value="0" />
        <input type="hidden" name="qm" value="0" />
        <input type="hidden" name="st" value="1" />
        <input type="hidden" name="nh" value="10" />
        <input type="hidden" name="lk" value="1" />
        <input type="hidden" name="rf" value="0" />
        <input type="hidden" name="oq" value="" />
        <input type="hidden" name="col" value="eren" />
        <input type="text" name="qt" maxlength="2047" id="search" />
        <input type="image" src="<?php echo EXT_DIR_PREFIX; ?>/images/v2/search_button_blue.gif" value="Search" alt="Search" />
      </form>
      <p><a href="http://www1.eere.energy.gov/site_administration/searchhelp.html">Search Help</a>&nbsp;<img src="<?php echo EXT_DIR_PREFIX; ?>/images/v2/search_arrow.gif" width="4" height="7" border="0" alt="" /></p>
    </div>
    <div id="sitename"><a href="<?php echo EXT_DIR_PREFIX; ?><?php echo SITE_HOME_URL; ?>"><?php echo $strProgramName;?></a></div>

<!--[if lt IE 8]><div id="ie-lt8"><![endif]-->
<?php if (DISPLAY_TOPNAV) {
    //example top nav below: ?>
    <div id="topnav">
      <ul>
        <li id="tn_home"><a href="<?php echo EXT_DIR_PREFIX; ?><?php echo WEB_ROOT; ?>/index.html">HOME</a></li>
        <li id="tn_about" class="current"><a href="<?php echo EXT_DIR_PREFIX; ?><?php echo WEB_ROOT; ?>/oe_main.html">ABOUT</a></li>
        <li id="tn_energy" ><a href="<?php echo EXT_DIR_PREFIX; ?><?php echo WEB_ROOT; ?>/ee_main.html">ENERGY EFFICIENCY</a></li>
        <li id="tn_renewable" ><a href="<?php echo EXT_DIR_PREFIX; ?><?php echo WEB_ROOT; ?>/re_main.html">RENEWABLE ENERGY</a></li>
        <li id="tn_business" ><a href="<?php echo EXT_DIR_PREFIX; ?><?php echo WEB_ROOT; ?>/bo_main.html">BUSINESS OPERATIONS</a></li>
        <li id="tn_strategic"><a href="<?php echo EXT_DIR_PREFIX; ?><?php echo WEB_ROOT; ?>/sp_main.html">STRATEGIC PROGRAMS</a></li>
        <li id="tn_initiatives"><a href="<?php echo EXT_DIR_PREFIX; ?><?php echo WEB_ROOT; ?>/oe_projects_main.html">INITIATIVES &amp; PROJECTS</a></li>
      </ul>
    </div>
  <?php } //endif ?>
<!--[if lt IE 8]></div><![endif]-->

  </div>