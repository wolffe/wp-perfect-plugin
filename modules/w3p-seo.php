<?php
function w3p_seo_options() {
	$hidden_field_name = 'wpfc_submit_hidden';
	$w3p_email_field_name = 'w3p_email';

	// read in existing option value from database
    $option_value_w3p_email = get_option('w3p_email');

    // See if the user has posted us some information // if they did, this hidden field will be set to 'Y'
	if(isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {
		$option_value_w3p_email = $_POST[$w3p_email_field_name];

		update_option('w3p_email', $option_value_w3p_email);
		?>
		<div class="updated"><p><strong>Settings saved.</strong></p></div>
		<?php
	}

	$w3p_email = get_option('w3p_email');
	$w3p_tracked_site = get_option('siteurl');
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2>SEO Settings</h2>

		<h3>SEO Tracker</h3>
		<p><a href="http://www.alexa.com/siteinfo/<?php echo $w3p_tracked_site;?>"><script type="text/javascript" src="http://xslt.alexa.com/site_stats/js/s/c?url=<?php echo $w3p_tracked_site;?>"></script></a></p>
		<p><small>Alexa Traffic Rank is a measure of a site's popularity. The rank is calculated using a combination of average daily visitors and pageviews over the past 3 months. The site with the highest combination of visitors and pageviews is ranked #1.</small></p>

		<?php wp_seo_rank_widget_admin_function();?>

		<h3>Quick Links</h3>

		<table class="widefat">
			<thead>
				<tr>
					<th>Google</th>
					<th>Yahoo</th>
					<th>Bing</th>
					<th>Alexa</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<ul>
							<li><a href="http://www.google.com/webmasters/" rel="external">Google Webmaster Tools</a></li>
							<li><a href="http://www.google.com/analytics/" rel="external">Google Analytics</a></li>
						</ul>
					</td>
					<td>
						<ul>
							<li><a href="https://siteexplorer.search.yahoo.com/" rel="external">Yahoo SiteExplorer</a></li>
						</ul>
					</td>
					<td>
						<ul>
							<li><a href="http://www.bing.com/toolbox/webmasters/" rel="external">Bing Webmaster Tools</a></li>
						</ul>
					</td>
					<td>
						<ul>
							<li><a href="http://www.alexa.com/siteowners" rel="external">Alexa Site Tools</a></li>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>

		<form name="form1" method="post" action="">
			<input type="hidden" name="<?php echo $hidden_field_name;?>" value="Y" />
			<p>
				<input type="text" name="<?php echo $w3p_email_field_name;?>" id="<?php echo $w3p_email_field_name;?>" value="<?php echo $option_value_w3p_email;?>" size="40" /> <label for="<?php echo $w3p_email_field_name;?>">Contact Form Email</label>
				<br />
				<span class="description"><small>Contact emails will be sent to this address (currently set to <strong><?php echo $w3p_email;?></strong>).</small></span>
			</p>
			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="Save Changes" />
			</p>
		</form>
	</div>
<?php }?>