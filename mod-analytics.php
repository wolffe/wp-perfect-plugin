<?php
if(isset($_GET['wpsa_action'])) {
	$iriAction = mysql_real_escape_string($_GET['wpsa_action']);
}

function iri_add_pages() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'wpsa';
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
		iri_wpsa_CreateTable();
}
function permalinksEnabled() {
	global $wpdb;
	$result = $wpdb->get_row('SELECT `option_value` FROM `' . $wpdb->prefix . 'options` WHERE `option_name` = "permalink_structure"');
	if($result->option_value != '')
		return true;
	else
		return false;
}
function my_substr($str, $x, $y = 0) {
	if($y == 0)
		$y = strlen($str) - $x;
	if(function_exists('mb_substr'))
		return mb_substr($str, $x, $y);
	else
		return substr($str, $x, $y);
}
function iriwpsa() {
	iriwpsaMain();
	iriwpsaDetails();
	iriwpsaOptions();
}
function iriwpsaOptions() {
	if($_POST['saveit'] == 'yes') {
		update_option('wpsa_collectloggeduser', $_POST['wpsa_collectloggeduser']);
		update_option('wpsa_autodelete', $_POST['wpsa_autodelete']);
		update_option('wpsa_daysinoverviewgraph', $_POST['wpsa_daysinoverviewgraph']);
		update_option('wpsa_donotcollectspider', $_POST['wpsa_donotcollectspider']);
		update_option('wpsa_autodelete_spider', $_POST['wpsa_autodelete_spider']);

		iri_wpsa_CreateTable();
		echo '<div class="updated"><p>' . __('Settings saved!', 'wpsa') . '</p></div>';
	}
	?>
	<div class='wrap'>
		<div id="icon-options-general" class="icon32"></div>
		<h2><?php _e('W3P Analytics Options', 'wpsa'); ?></h2>
		<form method="post">
			<p>
			<?php echo '
				<input type="checkbox" name="wpsa_collectloggeduser" id="wpsa_collectloggeduser" value="checked" ' . get_option('wpsa_collectloggeduser') . '> <label for="wpsa_collectloggeduser">' . __('Collect data about logged users, but ', 'wpsa') . '</label> 
				<input type="checkbox" name="wpsa_donotcollectspider" id="wpsa_donotcollectspider" value="checked" ' . get_option('wpsa_donotcollectspider') . '> <label for="wpsa_donotcollectspider">' . __('do not collect spiders visits', 'wpsa') . '</label>';
			?>
			</p>
			<p>
				<?php _e('Automatically delete visits older than', 'wpsa'); ?> <select name="wpsa_autodelete">
					<option value="" <?php if(get_option('wpsa_autodelete') == '') echo 'selected="selected"'; ?>><?php _e('Never delete!', 'wpsa'); ?></option>
					<option value="1 month" <?php if(get_option('wpsa_autodelete') == '1 month') echo 'selected="selected"'; ?>>1 <?php _e('month', 'wpsa'); ?></option>
					<option value="3 months" <?php if(get_option('wpsa_autodelete') == '3 months') echo 'selected="selected"'; ?>>3 <?php _e('months', 'wpsa'); ?></option>
					<option value="6 months" <?php if(get_option('wpsa_autodelete') == "6 months") echo 'selected="selected"'; ?>>6 <?php _e('months', 'wpsa'); ?></option>
					<option value="1 year" <?php if(get_option('wpsa_autodelete') == '1 year') echo 'selected="selected"'; ?>>1 <?php _e('year', 'wpsa'); ?></option>
				</select> <?php _e('and automatically delete spider visits older than','wpsa'); ?>
				<select name="wpsa_autodelete_spider">
					<option value="" <?php if(get_option('wpsa_autodelete_spider') =='' ) echo 'selected="selected"'; ?>><?php _e('Never delete!','wpsa'); ?></option>
					<option value="1 day" <?php if(get_option('wpsa_autodelete_spider') == "1 day") echo 'selected="selected"'; ?>>1 <?php _e('day','wpsa'); ?></option>
					<option value="1 week" <?php if(get_option('wpsa_autodelete_spider') == "1 week") echo 'selected="selected"'; ?>>1 <?php _e('week','wpsa'); ?></option>
					<option value="1 month" <?php if(get_option('wpsa_autodelete_spider') == "1 month") echo 'selected="selected"'; ?>>1 <?php _e('month','wpsa'); ?></option>
					<option value="1 year" <?php if(get_option('wpsa_autodelete_spider') == "1 year") echo 'selected="selected"'; ?>>1 <?php _e('year','wpsa'); ?></option>
				</select>
			</p>
			<p>
				<?php _e('Show', 'wpsa'); ?> <select name="wpsa_daysinoverviewgraph">
					<option value="7" <?php if(get_option('wpsa_daysinoverviewgraph') == 7) echo 'selected="selected"'; ?>>7</option>
					<option value="10" <?php if(get_option('wpsa_daysinoverviewgraph') == 10) echo 'selected="selected"'; ?>>10</option>
					<option value="20" <?php if(get_option('wpsa_daysinoverviewgraph') == 20) echo 'selected="selected"'; ?>>20</option>
					<option value="30" <?php if(get_option('wpsa_daysinoverviewgraph') == 30) echo 'selected="selected"'; ?>>30</option>
					<option value="50" <?php if(get_option('wpsa_daysinoverviewgraph') == 50) echo 'selected="selected"'; ?>>50</option>
				</select> <?php _e('days in overview graph', 'wpsa'); ?>
			</p>
			<p><input type="submit" class="button-primary" value="<?php _e('Save options', 'wpsa'); ?>"></p>
			<input type="hidden" name="saveit" value="yes">
			<input type="hidden" name="page" value="wpsa">
			<input type="hidden" name="wpsa_action" value="options">
		</form>
	</div>
	<?php
}
function iriwpsaMain() {
	global $wpdb;
	$table_name = $wpdb->prefix . "wpsa";

	$unique_color = "#114477";
	$web_color = "#3377B6";
	$rss_color = "#f38f36";
	$spider_color = "#83b4d8";
	$lastmonth = iri_wpsa_lastmonth();
	$thismonth = gmdate('Ym', current_time('timestamp'));
	$yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
	$today = gmdate('Ymd', current_time('timestamp'));
	$tlm[0] = my_substr($lastmonth, 0, 4);
	$tlm[1] = my_substr($lastmonth, 4, 2);
	echo '
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2>' . __('W3P Analytics', 'w3p') . '</h2>
		<p>Welcome to <strong>W3P Analytics</strong>! The dashboard shows you a general overview, last hits, search terms, referrers, agents and spiders.</p>';

	echo "
	<table class='widefat'>
		<thead>
			<tr>
				<th scope='col'></th>
				<th scope='col'>" . __('Total', 'wpsa') . "</th>
				<th scope='col'>" . __('Last month', 'wpsa') . "<br /><font size=1>" . gmdate('M, Y', gmmktime(0, 0, 0, $tlm[1], 1, $tlm[0])) . "</font></th>
				<th scope='col'>" . __('This month', 'wpsa') . "<br /><font size=1>" . gmdate('M, Y', current_time('timestamp')) . "</font></th>
				<th scope='col'>" . __('Target', 'wpsa') . " " . __('This month', 'wpsa') . "<br /><font size=1>" . gmdate('M, Y', current_time('timestamp')) . "</font></th>
				<th scope='col'>" . __('Yesterday', 'wpsa') . "<br /><font size=1>" . gmdate('d M, Y', current_time('timestamp') - 86400) . "</font></th>
				<th scope='col'>" . __('Today', 'wpsa') . "<br /><font size=1>" . gmdate('d M, Y', current_time('timestamp')) . "</font></th>
			</tr>
		</thead>
		<tbody id='the-list'>";
          
          //###############################################################################################
          // VISITORS ROW
          print "<tr><td><div style='background:$unique_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Visitors', 'wpsa') . "</td>";
          
          //TOTAL
          $qry_total = $wpdb->get_row("
    SELECT count(DISTINCT ip) AS visitors
    FROM $table_name
    WHERE feed=''
    AND spider=''
  ");
          print "<td>" . $qry_total->visitors . "</td>\n";
          
          //LAST MONTH
          $qry_lmonth = $wpdb->get_row("
    SELECT count(DISTINCT ip) AS visitors
    FROM $table_name
    WHERE feed=''
    AND spider=''
    AND date LIKE '" . mysql_real_escape_string($lastmonth) . "%'
  ");
          print "<td>" . $qry_lmonth->visitors . "</td>\n";
          
          //THIS MONTH
          $qry_tmonth = $wpdb->get_row("
    SELECT count(DISTINCT ip) AS visitors
    FROM $table_name
    WHERE feed=''
    AND spider=''
    AND date LIKE '" . mysql_real_escape_string($thismonth) . "%'
  ");
          if ($qry_lmonth->visitors <> 0)
          {
              $pc = round(100 * ($qry_tmonth->visitors / $qry_lmonth->visitors) - 100, 1);
              if ($pc >= 0)
                  $pc = "+" . $pc;
              $qry_tmonth->change = "<code> (" . $pc . "%)</code>";
          }
          print "<td>" . $qry_tmonth->visitors . $qry_tmonth->change . "</td>\n";
          
          //TARGET
          
          $qry_tmonth->target = round($qry_tmonth->visitors / (time() - mktime(0,0,0,date('m'),date('1'),date('Y'))) * (86400 * date('t')));
          if ($qry_lmonth->visitors <> 0)
          {
              $pt = round(100 * ($qry_tmonth->target / $qry_lmonth->visitors) - 100, 1);
              if ($pt >= 0)
                  $pt = "+" . $pt;
              $qry_tmonth->added = "<code> (" . $pt . "%)</code>";
          }
          print "<td>" . $qry_tmonth->target . $qry_tmonth->added . "</td>\n";
          
          //YESTERDAY
          $qry_y = $wpdb->get_row("
    SELECT count(DISTINCT ip) AS visitors
    FROM $table_name
    WHERE feed=''
    AND spider=''
    AND date = '" . mysql_real_escape_string($yesterday) . "'
  ");
          print "<td>" . $qry_y->visitors . "</td>\n";
          
          //TODAY
          $qry_t = $wpdb->get_row("
    SELECT count(DISTINCT ip) AS visitors
    FROM $table_name
    WHERE feed=''
    AND spider=''
    AND date = '" . mysql_real_escape_string($today) . "'
  ");
          print "<td>" . $qry_t->visitors . "</td>\n";
          print "</tr>";
          
          //###############################################################################################
          // PAGEVIEWS ROW
          print "<tr><td><div style='background:$web_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Pageviews', 'wpsa') . "</td>";
          
          //TOTAL
          $qry_total = $wpdb->get_row("
    SELECT count(date) as pageview
    FROM $table_name
    WHERE feed=''
    AND spider=''
  ");
          print "<td>" . $qry_total->pageview . "</td>\n";
          
          //LAST MONTH
          $prec = 0;
          $qry_lmonth = $wpdb->get_row("
    SELECT count(date) as pageview
    FROM $table_name
    WHERE feed=''
    AND spider=''
    AND date LIKE '" . mysql_real_escape_string($lastmonth) . "%'
  ");
          print "<td>" . $qry_lmonth->pageview . "</td>\n";
          
          //THIS MONTH
          $qry_tmonth = $wpdb->get_row("
    SELECT count(date) as pageview
    FROM $table_name
    WHERE feed=''
    AND spider=''
    AND date LIKE '" . mysql_real_escape_string($thismonth) . "%'
  ");
          if ($qry_lmonth->pageview <> 0)
          {
              $pc = round(100 * ($qry_tmonth->pageview / $qry_lmonth->pageview) - 100, 1);
              if ($pc >= 0)
                  $pc = "+" . $pc;
              $qry_tmonth->change = "<code> (" . $pc . "%)</code>";
          }
          print "<td>" . $qry_tmonth->pageview . $qry_tmonth->change . "</td>\n";
          
          //TARGET
          $qry_tmonth->target = round($qry_tmonth->pageview / (time() - mktime(0,0,0,date('m'),date('1'),date('Y'))) * (86400 * date('t')));
          if ($qry_lmonth->pageview <> 0)
          {
              $pt = round(100 * ($qry_tmonth->target / $qry_lmonth->pageview) - 100, 1);
              if ($pt >= 0)
                  $pt = "+" . $pt;
              $qry_tmonth->added = "<code> (" . $pt . "%)</code>";
          }
          print "<td>" . $qry_tmonth->target . $qry_tmonth->added . "</td>\n";
          
          //YESTERDAY
          $qry_y = $wpdb->get_row("
    SELECT count(date) as pageview
    FROM $table_name
    WHERE feed=''
    AND spider=''
    AND date = '" . mysql_real_escape_string($yesterday) . "'
  ");
          print "<td>" . $qry_y->pageview . "</td>\n";
          
          //TODAY
          $qry_t = $wpdb->get_row("
    SELECT count(date) as pageview
    FROM $table_name
    WHERE feed=''
    AND spider=''
    AND date = '" . mysql_real_escape_string($today) . "'
  ");
          print "<td>" . $qry_t->pageview . "</td>\n";
          print "</tr>";
          //###############################################################################################
          // SPIDERS ROW
          print "<tr><td><div style='background:$spider_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Spiders', 'wpsa') . "</td>";
          //TOTAL
          $qry_total = $wpdb->get_row("
    SELECT count(date) as spiders
    FROM $table_name
    WHERE feed=''
    AND spider<>''
  ");
          print "<td>" . $qry_total->spiders . "</td>\n";
          //LAST MONTH
          $prec = 0;
          $qry_lmonth = $wpdb->get_row("
    SELECT count(date) as spiders
    FROM $table_name
    WHERE feed=''
    AND spider<>''
    AND date LIKE '" . mysql_real_escape_string($lastmonth) . "%'
  ");
          print "<td>" . $qry_lmonth->spiders . "</td>\n";
          
          //THIS MONTH
          $prec = $qry_lmonth->spiders;
          $qry_tmonth = $wpdb->get_row("
    SELECT count(date) as spiders
    FROM $table_name
    WHERE feed=''
    AND spider<>''
    AND date LIKE '" . mysql_real_escape_string($thismonth) . "%'
  ");
          if ($qry_lmonth->spiders <> 0)
          {
              $pc = round(100 * ($qry_tmonth->spiders / $qry_lmonth->spiders) - 100, 1);
              if ($pc >= 0)
                  $pc = "+" . $pc;
              $qry_tmonth->change = "<code> (" . $pc . "%)</code>";
          }
          print "<td>" . $qry_tmonth->spiders . $qry_tmonth->change . "</td>\n";
          
          //TARGET
          $qry_tmonth->target = round($qry_tmonth->spiders / (time() - mktime(0,0,0,date('m'),date('1'),date('Y'))) * (86400 * date('t')));
          if ($qry_lmonth->spiders <> 0)
          {
              $pt = round(100 * ($qry_tmonth->target / $qry_lmonth->spiders) - 100, 1);
              if ($pt >= 0)
                  $pt = "+" . $pt;
              $qry_tmonth->added = "<code> (" . $pt . "%)</code>";
          }
          print "<td>" . $qry_tmonth->target . $qry_tmonth->added . "</td>\n";
          
          //YESTERDAY
          $qry_y = $wpdb->get_row("
    SELECT count(date) as spiders
    FROM $table_name
    WHERE feed=''
    AND spider<>''
    AND date = '" . mysql_real_escape_string($yesterday) . "'
  ");
          print "<td>" . $qry_y->spiders . "</td>\n";
          
          //TODAY
          $qry_t = $wpdb->get_row("
    SELECT count(date) as spiders
    FROM $table_name
    WHERE feed=''
    AND spider<>''
    AND date = '" . mysql_real_escape_string($today) . "'
  ");
          print "<td>" . $qry_t->spiders . "</td>\n";
          print "</tr>";
          //###############################################################################################
          // FEEDS ROW
          print "<tr><td><div style='background:$rss_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Feeds', 'wpsa') . "</td>";
          //TOTAL
          $qry_total = $wpdb->get_row("
    SELECT count(date) as feeds
    FROM $table_name
    WHERE feed<>''
    AND spider=''
  ");
          print "<td>" . $qry_total->feeds . "</td>\n";
          
          //LAST MONTH
          $qry_lmonth = $wpdb->get_row("
    SELECT count(date) as feeds
    FROM $table_name
    WHERE feed<>''
    AND spider=''
    AND date LIKE '" . mysql_real_escape_string($lastmonth) . "%'
  ");
          print "<td>" . $qry_lmonth->feeds . "</td>\n";
          
          //THIS MONTH
          $qry_tmonth = $wpdb->get_row("
    SELECT count(date) as feeds
    FROM $table_name
    WHERE feed<>''
    AND spider=''
    AND date LIKE '" . mysql_real_escape_string($thismonth) . "%'
  ");
          if ($qry_lmonth->feeds <> 0)
          {
              $pc = round(100 * ($qry_tmonth->feeds / $qry_lmonth->feeds) - 100, 1);
              if ($pc >= 0)
                  $pc = "+" . $pc;
              $qry_tmonth->change = "<code> (" . $pc . "%)</code>";
          }
          print "<td>" . $qry_tmonth->feeds . $qry_tmonth->change . "</td>\n";
          
          //TARGET
          $qry_tmonth->target = round($qry_tmonth->feeds / (time() - mktime(0,0,0,date('m'),date('1'),date('Y'))) * (86400 * date('t')));
          if ($qry_lmonth->feeds <> 0)
          {
              $pt = round(100 * ($qry_tmonth->target / $qry_lmonth->feeds) - 100, 1);
              if ($pt >= 0)
                  $pt = "+" . $pt;
              $qry_tmonth->added = "<code> (" . $pt . "%)</code>";
          }
          print "<td>" . $qry_tmonth->target . $qry_tmonth->added . "</td>\n";
          
          $qry_y = $wpdb->get_row("
    SELECT count(date) as feeds
    FROM $table_name
    WHERE feed<>''
    AND spider=''
    AND date = '" . mysql_real_escape_string($yesterday) . "'
  ");
          print "<td>" . $qry_y->feeds . "</td>\n";
          
          $qry_t = $wpdb->get_row("
    SELECT count(date) as feeds
    FROM $table_name
    WHERE feed<>''
    AND spider=''
    AND date = '" . mysql_real_escape_string($today) . "'
  ");
          print "<td>" . $qry_t->feeds . "</td>\n";
          
          print "</tr></table><br />\n\n";
          
          //###############################################################################################
          //###############################################################################################
          // THE GRAPHS
          
          // last "N" days graph  NEW
          $gdays = get_option('wpsa_daysinoverviewgraph');
          if ($gdays == 0)
          {
              $gdays = 20;
          }
          //  $start_of_week = get_option('start_of_week');
          $start_of_week = get_option('start_of_week');
          print '<table width="100%" border="0"><tr>';
          $qry = $wpdb->get_row("
    SELECT count(date) as pageview, date
    FROM $table_name
    GROUP BY date HAVING date >= '" . gmdate('Ymd', current_time('timestamp') - 86400 * $gdays) . "'
    ORDER BY pageview DESC
    LIMIT 1
  ");
          $maxxday = $qry->pageview;
          if ($maxxday == 0)
          {
              $maxxday = 1;
          }
          // Y
          $gd = (90 / $gdays) . '%';
          for ($gg = $gdays - 1; $gg >= 0; $gg--)
          {
              //TOTAL VISITORS
              $qry_visitors = $wpdb->get_row("
      SELECT count(DISTINCT ip) AS total
      FROM $table_name
      WHERE feed=''
      AND spider=''
      AND date = '" . gmdate('Ymd', current_time('timestamp') - 86400 * $gg) . "'
    ");
              $px_visitors = round($qry_visitors->total * 100 / $maxxday);
              
              //TOTAL PAGEVIEWS (we do not delete the uniques, this is falsing the info.. uniques are not different visitors!)
              $qry_pageviews = $wpdb->get_row("
      SELECT count(date) as total
      FROM $table_name
      WHERE feed=''
      AND spider=''
      AND date = '" . gmdate('Ymd', current_time('timestamp') - 86400 * $gg) . "'
    ");
              $px_pageviews = round($qry_pageviews->total * 100 / $maxxday);
              
              //TOTAL SPIDERS
              $qry_spiders = $wpdb->get_row("
      SELECT count(ip) AS total
      FROM $table_name
      WHERE feed=''
      AND spider<>''
      AND date = '" . gmdate('Ymd', current_time('timestamp') - 86400 * $gg) . "'
    ");
              $px_spiders = round($qry_spiders->total * 100 / $maxxday);
              
              //TOTAL FEEDS
              $qry_feeds = $wpdb->get_row("
      SELECT count(ip) AS total
      FROM $table_name
      WHERE feed<>''
      AND spider=''
      AND date = '" . gmdate('Ymd', current_time('timestamp') - 86400 * $gg) . "'
    ");
              $px_feeds = round($qry_feeds->total * 100 / $maxxday);
              
              $px_white = 100 - $px_feeds - $px_spiders - $px_pageviews - $px_visitors;
              
              print '<td width="' . $gd . '" valign="bottom"';
              if ($start_of_week == gmdate('w', current_time('timestamp') - 86400 * $gg))
              {
                  print ' style="border-left:2px dotted gray;"';
              }
              // week-cut
              print "><div style='float:left;height: 100%;width:100%;font-family:Helvetica;font-size:7pt;text-align:center;border-right:1px solid white;color:black;'>
    <div style='background:#ffffff;width:100%;height:" . $px_white . "px;'></div>
    <div style='background:$unique_color;width:100%;height:" . $px_visitors . "px;' title='" . $qry_visitors->total . " " . __('visitors', 'wpsa')."'></div>
    <div style='background:$web_color;width:100%;height:" . $px_pageviews . "px;' title='" . $qry_pageviews->total . " " . __('pageviews', 'wpsa')."'></div>
    <div style='background:$spider_color;width:100%;height:" . $px_spiders . "px;' title='" . $qry_spiders->total . " " . __('spiders', 'wpsa')."'></div>
    <div style='background:$rss_color;width:100%;height:" . $px_feeds . "px;' title='" . $qry_feeds->total . " " . __('feeds', 'wpsa')."'></div>
    <div style='background:gray;width:100%;height:1px;'></div>
    <br />" . gmdate('d', current_time('timestamp') - 86400 * $gg) . ' ' . gmdate('M', current_time('timestamp') - 86400 * $gg) . "</div></td>\n";
          }
          print '</tr></table>';
          
          print '</div>';
          // END OF OVERVIEW
          //###################################################################################################
          
          
          
          
          $querylimit = "LIMIT 20";
          
          // Tabella Last hits
          print "<div class='wrap'><h2>" . __('Last hits', 'wpsa') . "</h2><table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'wpsa') . "</th><th scope='col'>" . __('Time', 'wpsa') . "</th><th scope='col'>" . __('IP', 'wpsa') . "</th><th scope='col'>" . __('Threat', 'wpsa') . "</th><th scope='col'>" . __('Domain', 'wpsa') . "</th><th scope='col'>" . __('Page', 'wpsa') . "</th><th scope='col'>" . __('OS', 'wpsa') . "</th><th scope='col'>" . __('Browser', 'wpsa') . "</th><th scope='col'>" . __('Feed', 'wpsa') . "</th></tr></thead>";
          print "<tbody id='the-list'>";
          
          $fivesdrafts = $wpdb->get_results("SELECT * FROM $table_name WHERE (os<>'' OR feed<>'') order by id DESC $querylimit");
          foreach ($fivesdrafts as $fivesdraft)
          {
              print "<tr>";
              print "<td>" . irihdate($fivesdraft->date) . "</td>";
              print "<td>" . $fivesdraft->time . "</td>";
              print "<td>" . $fivesdraft->ip . "</td>";
              print "<td>" . $fivesdraft->threat_score;
              if ($fivesdraft->threat_score > 0)
              {
                  print "/";
                  if ($fivesdraft->threat_type == 0)
                      print "Sp"; // Spider
                  else
                  {
                      if (($fivesdraft->threat_type & 1) == 1)
                          print "S"; // Suspicious
                      if (($fivesdraft->threat_type & 2) == 2)
                          print "H"; // Harvester
                      if (($fivesdraft->threat_type & 4) == 4)
                          print "C"; // Comment spammer
                  }
              }
              print "<td>" . $fivesdraft->nation . "</td>";
              print "<td>" . iri_wpsa_Abbrevia(iri_wpsa_Decode($fivesdraft->urlrequested), 30) . "</td>";
              print "<td>" . $fivesdraft->os . "</td>";
              print "<td>" . $fivesdraft->browser . "</td>";
              print "<td>" . $fivesdraft->feed . "</td>";
              print "</tr>";
          }
          print "</table></div>";
          
          
          // Last Search terms
          print "<div class='wrap'><h2>" . __('Last search terms', 'wpsa') . "</h2><table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'wpsa') . "</th><th scope='col'>" . __('Time', 'wpsa') . "</th><th scope='col'>" . __('Terms', 'wpsa') . "</th><th scope='col'>" . __('Engine', 'wpsa') . "</th><th scope='col'>" . __('Result', 'wpsa') . "</th></tr></thead>";
          print "<tbody id='the-list'>";
          $qry = $wpdb->get_results("SELECT date,time,referrer,urlrequested,search,searchengine FROM $table_name WHERE search<>'' ORDER BY id DESC $querylimit");
          foreach ($qry as $rk)
          {
              print "<tr><td>" . irihdate($rk->date) . "</td><td>" . $rk->time . "</td><td><a href='" . $rk->referrer . "'>" . urldecode($rk->search) . "</a></td><td>" . $rk->searchengine . "</td><td><a href='" . irigetblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "'>" . __('page viewed', 'wpsa') . "</a></td></tr>\n";
          }
          print "</table></div>";
          
          // Referrer
          print "<div class='wrap'><h2>" . __('Last referrers', 'wpsa') . "</h2><table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'wpsa') . "</th><th scope='col'>" . __('Time', 'wpsa') . "</th><th scope='col'>" . __('URL', 'wpsa') . "</th><th scope='col'>" . __('Result', 'wpsa') . "</th></tr></thead>";
          print "<tbody id='the-list'>";
          $qry = $wpdb->get_results("SELECT date,time,referrer,urlrequested FROM $table_name WHERE ((referrer NOT LIKE '" . get_option('home') . "%') AND (referrer <>'') AND (searchengine='')) ORDER BY id DESC $querylimit");
          foreach ($qry as $rk)
          {
              print "<tr><td>" . irihdate($rk->date) . "</td><td>" . $rk->time . "</td><td><a href='" . $rk->referrer . "'>" . iri_wpsa_Abbrevia($rk->referrer, 80) . "</a></td><td><a href='" . irigetblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "'>" . __('page viewed', 'wpsa') . "</a></td></tr>\n";
          }
          print "</table></div>";
          
          // Last Agents
          print "<div class='wrap'><h2>" . __('Last agents', 'wpsa') . "</h2><table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'wpsa') . "</th><th scope='col'>" . __('Time', 'wpsa') . "</th><th scope='col'>" . __('Agent', 'wpsa') . "</th><th scope='col'>" . __('What', 'wpsa') . "</th></tr></thead>";
          print "<tbody id='the-list'>";
          $qry = $wpdb->get_results("SELECT date,time,agent,os,browser,spider FROM $table_name WHERE (agent <>'') ORDER BY id DESC $querylimit");
          foreach ($qry as $rk)
          {
              print "<tr><td>" . irihdate($rk->date) . "</td><td>" . $rk->time . "</td><td>" . $rk->agent . "</td><td> " . $rk->os . " " . $rk->browser . " " . $rk->spider . "</td></tr>\n";
          }
          print "</table></div>";
          
          // Last pages
          print "<div class='wrap'><h2>" . __('Last pages', 'wpsa') . "</h2><table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'wpsa') . "</th><th scope='col'>" . __('Time', 'wpsa') . "</th><th scope='col'>" . __('Page', 'wpsa') . "</th><th scope='col'>" . __('What', 'wpsa') . "</th></tr></thead>";
          print "<tbody id='the-list'>";
          $qry = $wpdb->get_results("SELECT date,time,urlrequested,os,browser,spider FROM $table_name WHERE (spider='' AND feed='') ORDER BY id DESC $querylimit");
          foreach ($qry as $rk)
          {
              print "<tr><td>" . irihdate($rk->date) . "</td><td>" . $rk->time . "</td><td>" . iri_wpsa_Abbrevia(iri_wpsa_Decode($rk->urlrequested), 60) . "</td><td> " . $rk->os . " " . $rk->browser . " " . $rk->spider . "</td></tr>\n";
          }
          print "</table></div>";
          
          // Last Spiders
          print "<div class='wrap'><h2>" . __('Last spiders', 'wpsa') . "</h2>";
          print "<table class='widefat'><thead><tr>";
          print "<th scope='col'>" . __('Date', 'wpsa') . "</th>";
          print "<th scope='col'>" . __('Time', 'wpsa') . "</th>";
          print "<th scope='col'>" . __('Spider', 'wpsa') . "</th>";
          print "<th scope='col'>" . __('Page', 'wpsa') . "</th>";
          print "<th scope='col'>" . __('Agent', 'wpsa') . "</th>";
          print "</tr></thead><tbody id='the-list'>";
          $qry = $wpdb->get_results("SELECT date,time,agent,spider,urlrequested,agent FROM $table_name WHERE (spider<>'') ORDER BY id DESC $querylimit");
          foreach ($qry as $rk)
          {
              print "<tr><td>" . irihdate($rk->date) . "</td>";
              print "<td>" . $rk->time . "</td>";
              print "<td>" . $rk->spider . "</td>";
              print "<td>" . iri_wpsa_Abbrevia(iri_wpsa_Decode($rk->urlrequested), 30) . "</td>";
              print "<td> " . $rk->agent . "</td></tr>\n";
          }
          print "</table></div>";
          
}

function iriwpsaDetails() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'wpsa';
	$querylimit = "LIMIT 10";
	echo '<h2>Detailed Overview</h2>';
	echo '<table class="widefat">';
		iriValueTable("date", __('Top days', 'wpsa'), 5);
		iriValueTable("os", __('O.S.', 'wpsa'), 0, "", "", "AND feed='' AND spider='' AND os<>''");
		iriValueTable("browser", __('Browser', 'wpsa'), 0, "", "", "AND feed='' AND spider='' AND browser<>''");
		iriValueTable("feed", __('Feeds', 'wpsa'), 5, "", "", "AND feed<>''");
		iriValueTable("searchengine", __('Search engines', 'wpsa'), 10, "", "", "AND searchengine<>''");
		iriValueTable("search", __('Top search terms', 'wpsa'), 20, "", "", "AND search<>''");
		iriValueTable("referrer", __('Top referrer', 'wpsa'), 10, "", "", "AND referrer<>'' AND referrer NOT LIKE '%" . get_bloginfo('url') . "%'");
		iriValueTable("nation", __('Countries (domains)', 'wpsa'), 10, "", "", "AND nation<>'' AND spider=''");
		iriValueTable("spider", __('Spiders', 'wpsa'), 10, "", "", "AND spider<>''");
		iriValueTable("urlrequested", __('Top pages', 'wpsa'), 5, "", "urlrequested", "AND feed='' and spider=''");
		iriValueTable("date", __('Top Days - Unique visitors', 'wpsa'), 5, "distinct", "ip", "AND feed='' and spider=''");
		iriValueTable("date", __('Top Days - Pageviews', 'wpsa'), 5, "", "urlrequested", "AND feed='' and spider=''");
		iriValueTable("ip", __('Top IPs - Pageviews', 'wpsa'), 5, "", "urlrequested", "AND feed='' and spider=''");
	echo '</table>';
}
      
      

      
      

      
      function iri_wpsa_Abbrevia($s, $c)
      {
          $res = "";
          if (strlen($s) > $c)
          {
              $res = "...";
          }
          return my_substr($s, 0, $c) . $res;
      }
      
      function iri_wpsa_Where($ip)
      {
          $url = "http://api.hostip.info/get_html.php?ip=$ip";
          $res = file_get_contents($url);
          if ($res === false)
          {
              return(array('', ''));
          }
          $res = str_replace("Country: ", "", $res);
          $res = str_replace("\nCity: ", ", ", $res);
          $nation = preg_split('/\(|\)/', $res);
          print "( $ip $res )";
          return(array($res, $nation[1]));
      }
      
      
      function iri_wpsa_Decode($out_url)
      {
      	if(!permalinksEnabled())
      	{
	          if ($out_url == '')
	          {
	              $out_url = __('Page', 'wpsa') . ": Home";
	          }
	          if (my_substr($out_url, 0, 4) == "cat=")
	          {
	              $out_url = __('Category', 'wpsa') . ": " . get_cat_name(my_substr($out_url, 4));
	          }
	          if (my_substr($out_url, 0, 2) == "m=")
	          {
	              $out_url = __('Calendar', 'wpsa') . ": " . my_substr($out_url, 6, 2) . "/" . my_substr($out_url, 2, 4);
	          }
	          if (my_substr($out_url, 0, 2) == "s=")
	          {
	              $out_url = __('Search', 'wpsa') . ": " . my_substr($out_url, 2);
	          }
	          if (my_substr($out_url, 0, 2) == "p=")
	          {
	              $post_id_7 = get_post(my_substr($out_url, 2), ARRAY_A);
	              $out_url = $post_id_7['post_title'];
	          }
	          if (my_substr($out_url, 0, 8) == "page_id=")
	          {
	              $post_id_7 = get_page(my_substr($out_url, 8), ARRAY_A);
	              $out_url = __('Page', 'wpsa') . ": " . $post_id_7['post_title'];
	          }
	        }
	        else
	        {
	        	if ($out_url == '')
	          {
	              $out_url = __('Page', 'wpsa') . ": Home";
	          }
	          else if (my_substr($out_url, 0, 9) == "category/")
	          {
	              $out_url = __('Category', 'wpsa') . ": " . get_cat_name(my_substr($out_url, 9));
	          }
	          else if (my_substr($out_url, 0, 8) == "//") // not working yet
	          {
	              //$out_url = __('Calendar', 'wpsa') . ": " . my_substr($out_url, 4, 0) . "/" . my_substr($out_url, 6, 7);
	          }
	          else if (my_substr($out_url, 0, 2) == "s=")
	          {
	              $out_url = __('Search', 'wpsa') . ": " . my_substr($out_url, 2);
	          }
	          else if (my_substr($out_url, 0, 2) == "p=") // not working yet 
	          {
	              $post_id_7 = get_post(my_substr($out_url, 2), ARRAY_A);
	              $out_url = $post_id_7['post_title'];
	          }
	          else if (my_substr($out_url, 0, 8) == "page_id=") // not working yet
	          {
	              $post_id_7 = get_page(my_substr($out_url, 8), ARRAY_A);
	              $out_url = __('Page', 'wpsa') . ": " . $post_id_7['post_title'];
	          }
	        }
          return $out_url;
      }
      
      
      function iri_wpsa_URL()
      {
          $urlRequested = (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
          if ($urlRequested == "")
          {
              // SEO problem!
              $urlRequested = (isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '');
          }
          if (my_substr($urlRequested, 0, 2) == '/?')
          {
              $urlRequested = my_substr($urlRequested, 2);
          }
          if ($urlRequested == '/')
          {
              $urlRequested = '';
          }
          return $urlRequested;
      }
      
      function irigetblogurl()
      {
      	$prsurl = parse_url(get_bloginfo('url'));
      	return $prsurl['scheme'] . '://' . $prsurl['host'] . ((!permalinksEnabled()) ? $prsurl['path'] . '/?' : '');
      }
      
      // Converte da data us to default format di Wordpress
      function irihdate($dt = "00000000")
      {
          return mysql2date(get_option('date_format'), my_substr($dt, 0, 4) . "-" . my_substr($dt, 4, 2) . "-" . my_substr($dt, 6, 2));
      }
      
      
      function iritablesize($table)
      {
          global $wpdb;
          $res = $wpdb->get_results("SHOW TABLE STATUS LIKE '$table'");
          foreach ($res as $fstatus)
          {
              $data_lenght = $fstatus->Data_length;
              $data_rows = $fstatus->Rows;
          }
          return number_format(($data_lenght / 1024 / 1024), 2, ",", " ") . " MB ($data_rows records)";
      }
      
      
      function irirgbhex($red, $green, $blue)
      {
          $red = 0x10000 * max(0, min(255, $red + 0));
          $green = 0x100 * max(0, min(255, $green + 0));
          $blue = max(0, min(255, $blue + 0));
          // convert the combined value to hex and zero-fill to 6 digits
          return "#" . str_pad(strtoupper(dechex($red + $green + $blue)), 6, "0", STR_PAD_LEFT);
      }
      
      
function iriValueTable($fld, $fldtitle, $limit = 0, $param = "", $queryfld = "", $exclude = "") {
	global $wpdb;
	$table_name = $wpdb->prefix . 'wpsa';
	if($queryfld == '')
		$queryfld = $fld;

	echo '
	<thead>
		<tr>
			<th>' . $fldtitle . '</th>
			<th>' . __('Visits', 'wpsa') . '</th>
			<th>' . __('Chart', 'wpsa') . '</th>
		</tr>
	</thead>
	<tbody id="the-list">';

	$rks = $wpdb->get_var("SELECT count($param $queryfld) as rks FROM $table_name WHERE 1=1 $exclude;");
	if($rks > 0) {
		$sql = "SELECT count($param $queryfld) as pageview, $fld FROM $table_name WHERE 1=1 $exclude GROUP BY $fld ORDER BY pageview DESC";
		if($limit > 0)
			$sql = $sql . " LIMIT $limit";
		$qry = $wpdb->get_results($sql);
		$tdwidth = 450;
		$red = 131;
		$green = 180;
		$blue = 216;
		$deltacolor = round(250 / count($qry), 0);

		foreach($qry as $rk) {
			$pc = round(($rk->pageview * 100 / $rks), 1);
			if($fld == 'date')
				$rk->$fld = irihdate($rk->$fld);
			if($fld == 'urlrequested')
				$rk->$fld = iri_wpsa_Decode($rk->$fld);
			if($fld == 'search')
				$rk->$fld = urldecode($rk->$fld);

			echo '
			<tr>
				<td style="width: 400px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">' . my_substr($rk->$fld, 0, 50);
					if(strlen("$rk->fld") >= 50) echo '...';
				echo '</td>
				<td>' . $rk->pageview . '</td>
				<td>
					<div style="text-align: right; padding: 2px; height: 16px; width: ' . number_format(($tdwidth * $pc / 100), 1, '.', '') . 'px;background:' . irirgbhex($red, $green, $blue) . ';border-top:1px solid ' . irirgbhex($red + 20, $green + 20, $blue) . ';border-bottom:1px solid ' . irirgbhex($red - 20, $green - 20, $blue) . ';"><small>'.$pc.'%</small></div>
				</td>
			</tr>';
			$red = $red + $deltacolor;
			$blue = $blue - ($deltacolor / 2);
		}

		echo '<tr><td colspan="3"></td></tr>';
	}
}
      
      
      
      function iriDomain($ip)
      {
          $host = gethostbyaddr($ip);
          if (ereg('^([0-9]{1,3}\.){3}[0-9]{1,3}$', $host))
          {
              return "";
          }
          else
          {
              return my_substr(strrchr($host, "."), 1);
          }
      }
      
      function iriGetQueryPairs($url)
      {
          $parsed_url = parse_url($url);
          $tab = parse_url($url);
          $host = $tab['host'];
          if (key_exists("query", $tab))
          {
              $query = $tab["query"];
              $query = str_replace("&amp;", "&", $query);
              $query = urldecode($query);
              $query = str_replace("?", "&", $query);
              return explode("&", $query);
          }
          else
          {
              return null;
          }
      }
      
      
      function iriGetOS($arg)
      {
          $arg = str_replace(" ", "", $arg);
          $lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/definitions/os.dat');
          foreach ($lines as $line_num => $os)
          {
              list($nome_os, $id_os) = explode("|", $os);
              if (strpos($arg, $id_os) === false)
                  continue;
              // riconosciuto
              return $nome_os;
          }
          return '';
      }
      
      
      function iriGetBrowser($arg)
      {
          $arg = str_replace(" ", "", $arg);
          $lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/definitions/browser.dat');
          foreach ($lines as $line_num => $browser)
          {
              list($nome, $id) = explode("|", $browser);
              if (strpos($arg, $id) === false)
                  continue;
              // riconosciuto
              return $nome;
          }
          return '';
      }
      
      
      function iriGetSE($referrer = null)
      {
          $key = null;
          $lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/definitions/searchengines.dat');
          foreach ($lines as $line_num => $se)
          {
              list($nome, $url, $key) = explode("|", $se);
              if (strpos($referrer, $url) === false)
                  continue;
              // trovato se
              $variables = iriGetQueryPairs($referrer);
              $i = count($variables);
              while ($i--)
              {
                  $tab = explode("=", $variables[$i]);
                  if ($tab[0] == $key)
                  {
                      return($nome . "|" . urlencode($tab[1]));
                  }
              }
          }
          return null;
      }
      
      function iriGetSpider($agent = null)
      {
          $agent = str_replace(" ", "", $agent);
          $key = null;
          $lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/definitions/spider.dat');
          if (file_exists(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '-custom/spider.dat'))
              $lines = array_merge($lines, file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '-custom/spider.dat'));
          foreach ($lines as $line_num => $spider)
          {
              list($nome, $key) = explode("|", $spider);
              if (strpos($agent, $key) === false)
                  continue;
              // trovato
              return $nome;
          }
          return null;
      }
      
      
      function iri_wpsa_lastmonth()
      {
          $ta = getdate(current_time('timestamp'));
          
          $year = $ta['year'];
          $month = $ta['mon'];
          
          // go back 1 month;
          $month = $month - 1;
          
          if ($month === 0)
          {
          	// if this month is Jan
            // go back a year
            $year  = $year - 1;
          	$month = 12;
          }
          
          // return in format 'YYYYMM'
          return sprintf($year . '%02d', $month);
      }
      
      
      function iri_wpsa_CreateTable()
      {
          global $wpdb;
          global $wp_db_version;
          $table_name = $wpdb->prefix . "wpsa";
          $sql_createtable = "CREATE TABLE " . $table_name . " (
  id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  date TINYTEXT,
  time TINYTEXT,
  ip TINYTEXT,
  urlrequested TEXT,
  agent TEXT,
  referrer TEXT,
  search TEXT,
  nation TINYTEXT,
  os TINYTEXT,
  browser TINYTEXT,
  searchengine TINYTEXT,
  spider TINYTEXT,
  feed TINYTEXT,
  user TINYTEXT,
  timestamp TINYTEXT,
  threat_score SMALLINT,
  threat_type SMALLINT,
  UNIQUE KEY id (id)
  );";
          if ($wp_db_version >= 5540)
              $page = 'wp-admin/includes/upgrade.php';
          else
              $page = 'wp-admin/upgrade-functions.php';
          require_once(ABSPATH . $page);
          dbDelta($sql_createtable);
      }
      
function iri_wpsa_is_feed($url) {
   if (stristr($url,get_bloginfo('comments_atom_url')) != FALSE) { return 'COMMENT ATOM'; }
   elseif (stristr($url,get_bloginfo('comments_rss2_url')) != FALSE) { return 'COMMENT RSS'; }
   elseif (stristr($url,get_bloginfo('rdf_url')) != FALSE) { return 'RDF'; }
   elseif (stristr($url,get_bloginfo('atom_url')) != FALSE) { return 'ATOM'; }
   elseif (stristr($url,get_bloginfo('rss_url')) != FALSE) { return 'RSS'; }
   elseif (stristr($url,get_bloginfo('rss2_url')) != FALSE) { return 'RSS2'; }
   elseif (stristr($url,'wp-feed.php') != FALSE) { return 'RSS2'; }
   elseif (stristr($url,'/feed') != FALSE) { return 'RSS2'; }
   return '';
}



function iri_wpsa_extractfeedreq($url)
{
		if(!strpos($url, '?') === FALSE)
		{
        list($null, $q) = explode("?", $url);
    		list($res, $null) = explode("&", $q);
    }
    else
    {
    	$prsurl = parse_url($url);
    	$res = $prsurl['path'] . $$prsurl['query'];
    }
    
    return $res;
}

function iriStatAppend() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'wpsa';
	global $userdata;
	get_currentuserinfo();
	$feed = '';

	// Time
	$timestamp = current_time('timestamp');
	$vdate = gmdate("Ymd", $timestamp);
	$vtime = gmdate("H:i:s", $timestamp);

	// IP
	$ipAddress = $_SERVER['REMOTE_ADDR'];

	// Determine Threats if http:bl installed
	$threat_score = 0;
	$threat_type = 0;
	$httpbl_key = get_option("httpbl_key");
	if($httpbl_key !== false) {
		$result = explode( ".", gethostbyname( $httpbl_key . "." . implode ( ".", array_reverse( explode( ".", $ipAddress ) ) ) . ".dnsbl.httpbl.org" ) );
		// If the response is positive
		if($result[0] == 127) {
			$threat_score = $result[2];
			$threat_type = $result[3];
		}
	}

	// URL (requested)
	$urlRequested = iri_wpsa_URL();
	if(eregi(".ico$", $urlRequested))
		return '';
	if(eregi("favicon.ico", $urlRequested))
		return '';
	if(eregi(".css$", $urlRequested))
		return '';
	if(eregi(".js$", $urlRequested))
		return '';
	if(stristr($urlRequested, "/wp-content/plugins") != false)
		return '';
	if(stristr($urlRequested, "/wp-content/themes") != false)
		return '';

	$referrer = (isset($_SERVER['HTTP_REFERER']) ? htmlentities($_SERVER['HTTP_REFERER']) : '');
	$userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? htmlentities($_SERVER['HTTP_USER_AGENT']) : '');
	$spider = iriGetSpider($userAgent);

	if(($spider != '') and (get_option('wpsa_donotcollectspider') == 'checked'))
		return '';

	if($spider != '') {
		$os = '';
		$browser = '';
	}
	else {
		// Trap feeds
		$prsurl = parse_url(get_bloginfo('url'));
		$feed = iri_wpsa_is_feed($prsurl['scheme'] . '://' . $prsurl['host'] . $_SERVER['REQUEST_URI']);
		// Get OS and browser
		$os = iriGetOS($userAgent);
		$browser = iriGetBrowser($userAgent);
		list($searchengine, $search_phrase) = explode("|", iriGetSE($referrer));
	}
	// Auto-delete visits if...
	if(get_option('wpsa_autodelete_spider') != '') {
		$t = gmdate("Ymd", strtotime('-' . get_option('wpsa_autodelete_spider')));
		$results = $wpdb->query("DELETE FROM " . $table_name . " WHERE date < '" . $t . "' AND spider <> ''");
	}
	if(get_option('wpsa_autodelete') != '') {
		$t = gmdate("Ymd", strtotime('-' . get_option('wpsa_autodelete')));
		$results = $wpdb->query("DELETE FROM " . $table_name . " WHERE date < '" . $t . "'");
	}
	if((!is_user_logged_in()) or (get_option('wpsa_collectloggeduser') == 'checked')) {
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
			iri_wpsa_CreateTable();

		$insert = "INSERT INTO " . $table_name . " (date, time, ip, urlrequested, agent, referrer, search,nation,os,browser,searchengine,spider,feed,user,threat_score,threat_type,timestamp) " . "VALUES ('$vdate','$vtime','$ipAddress','" . mysql_real_escape_string($urlRequested) . "','" . mysql_real_escape_string(strip_tags($userAgent)) . "','" . mysql_real_escape_string($referrer) . "','" . mysql_real_escape_string(strip_tags($search_phrase)) . "','" . iriDomain($ipAddress) . "','" . mysql_real_escape_string($os) . "','" . mysql_real_escape_string($browser) . "','$searchengine','$spider','$feed','$userdata->user_login',$threat_score,$threat_type,'$timestamp')";
		$results = $wpdb->query($insert);
	}
}

function iriwpsaUpdate() {
	echo '<p>This function will synchronize the .dat files (OSs, browsers, spiders and IPs) with the database. It is requested on plugin updates.</p>'; // CHIP

	global $wpdb;
	$table_name = $wpdb->prefix . "wpsa";
	$wpdb->show_errors();

	// update table
	print ''.__('Updating table structure', 'wpsa')." $table_name... ";
	iri_wpsa_CreateTable();
	print ''.__('done', 'wpsa').'<br />';

	// Update Feed
	print ''.__('Updating Feeds', 'wpsa').'... ';
	$wpdb->query("UPDATE $table_name SET feed='';");

	// standard blog info urls
	$s = iri_wpsa_extractfeedreq(get_bloginfo('comments_atom_url'));
	if($s != '') {
		$wpdb->query("UPDATE $table_name SET feed='COMMENT ATOM' WHERE INSTR(urlrequested,'$s')>0 AND feed='';");
	}
	$s = iri_wpsa_extractfeedreq(get_bloginfo('comments_rss2_url'));
	if($s != '') {
		$wpdb->query("UPDATE $table_name SET feed='COMMENT RSS' WHERE INSTR(urlrequested,'$s')>0 AND feed='';");
	}
	$s = iri_wpsa_extractfeedreq(get_bloginfo('atom_url'));
	if($s != '') {
		$wpdb->query("UPDATE $table_name SET feed='ATOM' WHERE INSTR(urlrequested,'$s')>0 AND feed='';");
	}
	$s = iri_wpsa_extractfeedreq(get_bloginfo('rdf_url'));
	if($s != '') {
		$wpdb->query("UPDATE $table_name SET feed='RDF' WHERE INSTR(urlrequested,'$s')>0 AND feed='';");
	}
	$s = iri_wpsa_extractfeedreq(get_bloginfo('rss_url'));
	if($s != '') {
		$wpdb->query("UPDATE $table_name SET feed='RSS'  WHERE INSTR(urlrequested,'$s')>0 AND feed='';");
	}
	$s = iri_wpsa_extractfeedreq(get_bloginfo('rss2_url'));
	if($s != '') {
		$wpdb->query("UPDATE $table_name SET feed='RSS2' WHERE INSTR(urlrequested,'$s')>0 AND feed='';");
	}
          
          // not standard
          $wpdb->query("UPDATE $table_name SET feed='RSS2' WHERE urlrequested LIKE '%/feed%' AND feed='';");
          $wpdb->query("UPDATE $table_name SET feed='RSS2' WHERE urlrequested LIKE '%wp-feed.php%' AND feed='';");
         
          
          print "" . __('done', 'wpsa') . "<br>";
          
          // Update OS
          print "" . __('Updating OS', 'wpsa') . "... ";
          $wpdb->query("UPDATE $table_name SET os = '';");
          $lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/definitions/os.dat');
          foreach ($lines as $line_num => $os)
          {
              list($nome_os, $id_os) = explode("|", $os);
              $qry = "UPDATE $table_name SET os = '$nome_os' WHERE os='' AND replace(agent,' ','') LIKE '%" . $id_os . "%';";
              $wpdb->query($qry);
          }
          print "" . __('done', 'wpsa') . "<br>";
          
          // Update Browser
          print "". __('Updating Browsers', 'wpsa') ."... ";
          $wpdb->query("UPDATE $table_name SET browser = '';");
          $lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/definitions/browser.dat');
          foreach ($lines as $line_num => $browser)
          {
              list($nome, $id) = explode("|", $browser);
              $qry = "UPDATE $table_name SET browser = '$nome' WHERE browser='' AND replace(agent,' ','') LIKE '%" . $id . "%';";
              $wpdb->query($qry);
          }
          print "" . __('done', 'wpsa') . "<br>";
          
          print "" . __('Updating Spiders', 'wpsa') . "... ";
          $wpdb->query("UPDATE $table_name SET spider = '';");
          $lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/definitions/spider.dat');
          if (file_exists(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '-custom/spider.dat'))
              $lines = array_merge($lines, file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '-custom/spider.dat'));
          foreach ($lines as $line_num => $spider)
          {
              list($nome, $id) = explode("|", $spider);
              $qry = "UPDATE $table_name SET spider = '$nome',os='',browser='' WHERE spider='' AND replace(agent,' ','') LIKE '%" . $id . "%';";
              $wpdb->query($qry);
          }
          print "" . __('done', 'wpsa') . "<br>";
          
          // Update feed to ''
          print "" . __('Updating Feeds', 'wpsa') . "... ";
          $wpdb->query("UPDATE $table_name SET feed = '' WHERE isnull(feed);");
          print "" . __('done', 'wpsa') . "<br>";
          
          // Update Search engine
          print "" . __('Updating Search engines', 'wpsa') . "... ";
          print "<br>";
          $wpdb->query("UPDATE $table_name SET searchengine = '', search='';");
          print "..." . __('null-ed', 'wpsa') . "!<br>";
          $qry = $wpdb->get_results("SELECT id, referrer FROM $table_name WHERE referrer !=''");
          print "..." . __('select-ed', 'wpsa') . "!<br>";
          foreach ($qry as $rk)
          {
              list($searchengine, $search_phrase) = explode("|", iriGetSE($rk->referrer));
              if ($searchengine <> '')
              {
                  $q = "UPDATE $table_name SET searchengine = '$searchengine', search='" . addslashes($search_phrase) . "' WHERE id=" . $rk->id;
                  $wpdb->query($q);
              }
          }
          print "" . __('done', 'wpsa') . "<br>";
          
          $wpdb->hide_errors();
          
          print "<br>&nbsp;<h1>" . __('Updated', 'wpsa') . "!</h1>";
      }
      
      function wpsa_Widget($w = '')
      {
      }
      
      function wpsa_Print($body = '')
      {
          print iri_wpsa_Vars($body);
      }
      
      
      function iri_wpsa_Vars($body)
      {
          global $wpdb;
          $table_name = $wpdb->prefix . "wpsa";
          
          if (strpos(strtolower($body), "%visits%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as pageview FROM $table_name WHERE date = '" . gmdate("Ymd", current_time('timestamp')) . "' and spider='' and feed='';");
              $body = str_replace("%visits%", $qry[0]->pageview, $body);
          }
          if (strpos(strtolower($body), "%totalvisits%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as pageview FROM $table_name WHERE spider='' and feed='';");
              $body = str_replace("%totalvisits%", $qry[0]->pageview, $body);
          }
          if (strpos(strtolower($body), "%thistotalvisits%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as pageview FROM $table_name WHERE spider='' and feed='' AND urlrequested='" . mysql_real_escape_string(iri_wpsa_URL()) . "';");
              $body = str_replace("%thistotalvisits%", $qry[0]->pageview, $body);
          }
          if (strpos(strtolower($body), "%since%") !== false)
          {
              $qry = $wpdb->get_results("SELECT date FROM $table_name ORDER BY date LIMIT 1;");
              $body = str_replace("%since%", irihdate($qry[0]->date), $body);
          }
          if (strpos(strtolower($body), "%os%") !== false)
          {
              $userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
              $os = iriGetOS($userAgent);
              $body = str_replace("%os%", $os, $body);
          }
          if (strpos(strtolower($body), "%browser%") !== false)
          {
              $userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
              $browser = iriGetBrowser($userAgent);
              $body = str_replace("%browser%", $browser, $body);
          }
          if (strpos(strtolower($body), "%ip%") !== false)
          {
              $ipAddress = $_SERVER['REMOTE_ADDR'];
              $body = str_replace("%ip%", $ipAddress, $body);
          }
          if (strpos(strtolower($body), "%visitorsonline%") !== false)
          {
              $to_time = current_time('timestamp');
              $from_time = strtotime('-4 minutes', $to_time);
              $qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as visitors FROM $table_name WHERE spider='' and feed='' AND timestamp BETWEEN $from_time AND $to_time;");
              $body = str_replace("%visitorsonline%", $qry[0]->visitors, $body);
          }
          if (strpos(strtolower($body), "%usersonline%") !== false)
          {
              $to_time = current_time('timestamp');
              $from_time = strtotime('-4 minutes', $to_time);
              $qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as users FROM $table_name WHERE spider='' and feed='' AND user<>'' AND timestamp BETWEEN $from_time AND $to_time;");
              $body = str_replace("%usersonline%", $qry[0]->users, $body);
          }
          if (strpos(strtolower($body), "%toppost%") !== false)
          {
              $qry = $wpdb->get_results("SELECT urlrequested,count(*) as totale FROM $table_name WHERE spider='' AND feed='' AND urlrequested LIKE '%p=%' GROUP BY urlrequested ORDER BY totale DESC LIMIT 1;");
              $body = str_replace("%toppost%", iri_wpsa_Decode($qry[0]->urlrequested), $body);
          }
          if (strpos(strtolower($body), "%topbrowser%") !== false)
          {
              $qry = $wpdb->get_results("SELECT browser,count(*) as totale FROM $table_name WHERE spider='' AND feed='' GROUP BY browser ORDER BY totale DESC LIMIT 1;");
              $body = str_replace("%topbrowser%", iri_wpsa_Decode($qry[0]->browser), $body);
          }
          if (strpos(strtolower($body), "%topos%") !== false)
          {
              $qry = $wpdb->get_results("SELECT os,count(*) as totale FROM $table_name WHERE spider='' AND feed='' GROUP BY os ORDER BY totale DESC LIMIT 1;");
              $body = str_replace("%topos%", iri_wpsa_Decode($qry[0]->os), $body);
          }
          if(strpos(strtolower($body),"%pagestoday%") !== false)
          {
      				$qry = $wpdb->get_results("SELECT count(ip) as pageview FROM $table_name WHERE date = '".gmdate("Ymd",current_time('timestamp'))."' and spider='' and feed='';");
      				$body = str_replace("%pagestoday%", $qry[0]->pageview, $body);
   				}
   				
   				if(strpos(strtolower($body),"%thistotalpages%") !== FALSE)
   				{
      				$qry = $wpdb->get_results("SELECT count(ip) as pageview FROM $table_name WHERE spider='' and feed='';");
      				$body = str_replace("%thistotalpages%", $qry[0]->pageview, $body);
      		}
      		
      		if (strpos(strtolower($body), "%latesthits%") !== false)
			{
				$qry = $wpdb->get_results("SELECT search FROM $table_name WHERE search <> '' ORDER BY id DESC LIMIT 10");
				$body = str_replace("%latesthits%", urldecode($qry[0]->search), $body);
				for ($counter = 0; $counter < 10; $counter += 1)
				{
					$body .= "<br>". urldecode($qry[$counter]->search);
				}
			}
			
			if (strpos(strtolower($body), "%pagesyesterday%") !== false)
			{
				$yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
				$qry = $wpdb->get_row("SELECT count(DISTINCT ip) AS visitsyesterday FROM $table_name WHERE feed='' AND spider='' AND date = '" . $yesterday . "'");
				$body = str_replace("%pagesyesterday%", (is_array($qry) ? $qry[0]->visitsyesterday : 0), $body);
			}
          
			
          return $body;
      }
      
      
      function iri_wpsa_TopPosts($limit = 5, $showcounts = 'checked')
      {
          global $wpdb;
          $res = "\n<ul>\n";
          $table_name = $wpdb->prefix . "wpsa";
          $qry = $wpdb->get_results("SELECT urlrequested,count(*) as totale FROM $table_name WHERE spider='' AND feed='' GROUP BY urlrequested ORDER BY totale DESC LIMIT $limit;");
          foreach ($qry as $rk)
          {
              $res .= "<li><a href='" . irigetblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "'>" . iri_wpsa_Decode($rk->urlrequested) . "</a></li>\n";
              if (strtolower($showcounts) == 'checked')
              {
                  $res .= " (" . $rk->totale . ")";
              }
          }
          return "$res</ul>\n";
      }
      
function widget_wpsa_init($args) {
	function widget_wpsa_control() {
		$options = get_option('widget_wpsa');
		if(!is_array($options))
			$options = array('title' => 'wpsa', 'body' => 'Visits today: %visits%');
		if($_POST['wpsa-submit']) {
			$options['title'] = strip_tags(stripslashes($_POST['wpsa-title']));
			$options['body'] = stripslashes($_POST['wpsa-body']);
			update_option('widget_wpsa', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$body = htmlspecialchars($options['body'], ENT_QUOTES);

		echo '<p><label for="wpsa-title">' . __('Title:', 'w3p') . '</label><br><input id="wpsa-title" name="wpsa-title" type="text" value="' . $title . '" class="large-text"></p>';
		echo '<p><label for="wpsa-body">' . __('Body:', 'w3p') . '</label><br><textarea id="wpsa-body" name="wpsa-body" type="textarea" class="large-text">' . $body . '</textarea></p>';
		echo '<p><input type="hidden" id="wpsa-submit" name="wpsa-submit" value="1" /><small>%totalvisits% %visits% %thistotalvisits% %os% %browser% %ip% %since% %visitorsonline% %usersonline% %toppost% %topbrowser% %topos%</small></p>';
	}
	function widget_wpsa($args) {
		extract($args);
		$options = get_option('widget_wpsa');
		$title = $options['title'];
		$body = $options['body'];
		echo $before_widget;
		print($before_title . $title . $after_title);
		print iri_wpsa_Vars($body);
		echo $after_widget;
	}

	wp_register_sidebar_widget(
		'wpsa',
		'W3P Analytics',
		'widget_wpsa',
		array(
			'description' => 'Real time statistics of your site.'
		)
	);
	wp_register_widget_control('wpsa', array('wpsa', 'widgets'), 'widget_wpsa_control');

	// Top posts
	function widget_wpsatopposts_control() {
		$options = get_option('widget_wpsatopposts');
		if(!is_array($options))
			$options = array('title' => 'wpsa TopPosts', 'howmany' => '5', 'showcounts' => 'checked');

		if($_POST['wpsatopposts-submit']) {
			$options['title'] = strip_tags(stripslashes($_POST['wpsatopposts-title']));
			$options['howmany'] = stripslashes($_POST['wpsatopposts-howmany']);
			$options['showcounts'] = stripslashes($_POST['wpsatopposts-showcounts']);
			if($options['showcounts'] == "1")
				$options['showcounts'] = 'checked';

			update_option('widget_wpsatopposts', $options);
		}

		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$howmany = htmlspecialchars($options['howmany'], ENT_QUOTES);
		$showcounts = htmlspecialchars($options['showcounts'], ENT_QUOTES);

		echo '<p><label for="wpsatopposts-title">' . __('Title', 'w3p') . '</label><br><input id="wpsa-title" name="wpsatopposts-title" type="text" value="' . $title . '"></p>';
		echo '<p><label for="wpsatopposts-howmany">' . __('Limit Results To', 'w3p') . ' <input id="wpsatopposts-howmany" name="wpsatopposts-howmany" type="number" min="1" max="50" value="' . $howmany . '"></label></p>';
		echo '<p><label for="wpsatopposts-showcounts">' . __('Visits', 'w3p') . ' <input id="wpsatopposts-showcounts" name="wpsatopposts-showcounts" type="checkbox" value="checked" ' . $showcounts . '></label></p>';
		echo '<input type="hidden" id="wpsa-submitTopPosts" name="wpsatopposts-submit" value="1">';
	}
	function widget_wpsatopposts($args) {
		extract($args);
		$options = get_option('widget_wpsatopposts');
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$howmany = htmlspecialchars($options['howmany'], ENT_QUOTES);
		$showcounts = htmlspecialchars($options['showcounts'], ENT_QUOTES);
		echo $before_widget;
		print($before_title . $title . $after_title);
		print iri_wpsa_TopPosts($howmany, $showcounts);
		echo $after_widget;
	}

	wp_register_sidebar_widget('wpsatp', 'W3P Top Posts', 'widget_wpsatopposts');
	wp_register_widget_control('wpsatp', array('wpsa TopPosts', 'widgets'), 'widget_wpsatopposts_control');
}

add_action('admin_menu', 'iri_add_pages');
add_action('plugins_loaded', 'widget_wpsa_init');
//add_action('wp_head', 'iriStatAppend');
add_action('send_headers', 'iriStatAppend');

register_activation_hook(__FILE__, 'iri_wpsa_CreateTable');
?>
