<?php
function IsPeacedevImageSitemapWritable($filename) {
	if(!is_writable($filename)) {
		if(!@chmod($filename, 0666)) {
			$pathtofilename = dirname($filename);
			if(!is_writable($pathtofilename)) {
				if(!@chmod($pathtoffilename, 0666))
					return false;
			}
		}
	}
	return true;
}

function multi_sitemap_generate() {
	if($_POST['schedule']) {
		wp_clear_scheduled_hook('my_hourly_event');
		wp_clear_scheduled_hook('peacedev_hourly_event');
		wp_schedule_event(time(), $_POST['timeschedule'], 'peacedev_hourly_event');

		echo '<div class="updated fade"><p>Site map generation scheduled</p></div>';
	}
	if($_POST['submit']) {
		$st = image_sitemap_loop_peacedev();
		$st2 = mobile_sitemap_loop_peacedev();
		$st3 = video_sitemap_loop_peacedev();
		?>
		<div class="updated fade">
			<p>The <a target="_blank" href="<?php echo get_bloginfo('url') . '/sitemap-image.xml'; ?>">XML Image Sitemap</a> was generated successfully</p>
			<p>The <a target="_blank" href="<?php echo get_bloginfo('url') . '/sitemap-mobile.xml'; ?>">XML Mobile Sitemap</a> was generated successfully</p>
			<p>The <a target="_blank" href="<?php echo get_bloginfo('url') . '/sitemap-video.xml'; ?>">XML Video Sitemap</a> was generated successfully</p>
		</div>
	<?php } ?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2>Media Sitemap Settings</h2>
			<div id="poststuff" class="ui-sortable meta-box-sortables">
				<div class="postbox">
					<h3>Settings</h3>
					<div class="inside">
						<?php $sitemapurl = get_bloginfo('url') . '/sitemap-image.xml'; ?>
						<?php $sitemapmobileurl = get_bloginfo('url') . '/sitemap-mobile.xml'; ?>
						<?php $sitemapvideourl = get_bloginfo('url') . '/sitemap-video.xml'; ?>
						<p>Read more about <a href="http://support.google.com/webmasters/bin/topic.py?hl=en&topic=20986" rel="external">specialized media sitemaps here</a>.</p>
						<form id="options_form" method="post" action="">
							<p>
								<input type="submit" name="submit" id="sb_submit" class="button button-primary" value="Generate Media Sitemap">
							</p>
							<p>
								<label>Schedule</label> 
								<select name="timeschedule">
									<option value="daily">daily</option>
									<option value="weekly">weekly</option>
									<option value="monthly">monthly</option>
								</select> <input type="submit" name="schedule" id="sb_submit2" class="button button-secondary" value="media sitemap generation">
							</p>
							<hr>
							<p>
								View existing/generated <a target="_blank" href="<?php echo $sitemapurl; ?>">XML Image Sitemap</a><br>
								View existing/generated <a target="_blank" href="<?php echo $sitemapmobileurl; ?>">XML Mobile Sitemap</a><br>
								View existing/generated <a target="_blank" href="<?php echo $sitemapvideourl; ?>">XML Video Sitemap</a>
							</p>
						</form>
					</div>
				</div>
			</div>
		</div>
	<?php
}

function image_sitemap_loop_peacedev () {
	global $wpdb;

	$posts = $wpdb->get_results("SELECT id, post_parent, post_content, guid, post_type FROM $wpdb->posts wposts
                                     WHERE ((wposts.post_type = 'post') and (wposts.post_status='publish'))
                                     OR    ((wposts.post_type = 'page') and (wposts.post_status='publish'))
                                     OR    ((wposts.post_type = 'attachment') and (wposts.post_status='inherit')
                                           and ((wposts.post_mime_type = 'image/jpg') or (wposts.post_mime_type = 'image/gif') 
                                           or (wposts.post_mime_type = 'image/jpeg') or (wposts.post_mime_type = 'image/png')))
                                     ");

	if(empty($posts)) {
		return false;
	} else {
		$xml  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$xml .= '<!-- Generated-on="'.date("F j, Y, g:i a").'" -->'."\n";
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

		foreach($posts as $post) {
			if($post->post_type == 'attachment') {
				if($post->post_parent != 0)
					$images[$post->post_parent][] = $post->guid;
			}
			else if(preg_match_all("/[\'\"](http:\/\/.[^\'\"]+\.(?:jpe?g|png|gif))[\'\"]/ui", $post->post_content, $matches, PREG_SET_ORDER)) {
				foreach($matches as $match) {
					$images[$post->id][] = $match[1];
				}
			}
		}

		foreach($images as $k => $v) {
			$permalink = get_permalink($k);
			$img = '<image:image><image:loc>' . implode('</image:loc></image:image><image:image><image:loc>', $v) . '</image:loc></image:image>';
			$xml .= '<url><loc>' . $permalink . '</loc>' . $img . '</url>';
		}

		$xml .= "\n</urlset>";
	}

	$image_sitemap_url = $_SERVER['DOCUMENT_ROOT'] . '/sitemap-image.xml';

	if(IsPeacedevImageSitemapWritable($_SERVER['DOCUMENT_ROOT']) || IsPeacedevImageSitemapWritable($image_sitemap_url)) {
		if(file_put_contents($image_sitemap_url, $xml))
			return true;
	}
	return false;
}

function mobile_sitemap_loop_peacedev() {
	global $wpdb;

	$posts = $wpdb->get_results("SELECT id, post_modified_gmt FROM $wpdb->posts 
							WHERE post_status = 'publish' 
							AND (post_type = 'post' OR post_type = 'page')
							ORDER BY post_date");

	if(empty ($posts)) {
		return false;
	} else {
		$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<!-- Generated-on="' . date("F j, Y, g:i a") .'" -->' . "\n";		     
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:mobile="http://www.google.com/schemas/sitemap-mobile/1.0">' . "\n";

		foreach($posts as $post) { 
			$permalink = get_permalink($post->id); 
			$xml .= "<url>\n";
			$xml .= " <loc>$permalink</loc>\n";
			$xml .= " <lastmod>" . date (DATE_W3C, strtotime ($post->post_modified_gmt)) . "</lastmod>\n";
			$xml .= " <mobile:mobile />\n";
			$xml .= "</url>\n";
		}

		$xml .= "\n</urlset>";
	}

	$mobile_sitemap_url = $_SERVER['DOCUMENT_ROOT'] . '/sitemap-mobile.xml';

	if(IsPeacedevImageSitemapWritable($_SERVER['DOCUMENT_ROOT']) || IsPeacedevImageSitemapWritable($mobile_sitemap_url)) {
		if(file_put_contents($mobile_sitemap_url, $xml))
			return true;
	}
	return false;
}

function video_sitemap_loop_peacedev() {
	global $wpdb;

	$posts = $wpdb->get_results("SELECT id, post_title, post_content, post_date_gmt, post_excerpt 
    FROM $wpdb->posts WHERE post_status = 'publish' 
    AND (post_type = 'post' OR post_type = 'page')
    AND post_content LIKE '%youtube.com%' 
    ORDER BY post_date DESC");

	if(empty ($posts)) {
		return false;
	} else {
		$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";       
		$xml .= '<!-- Generated-on="' . date("F j, Y, g:i a") .'" -->' . "\n";             
		$xml .= '<?xml-stylesheet type="text/xsl" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/google-multi-sitemap/video-sitemap.xsl"?>' . "\n" ;        
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . "\n";

		$videos = array();

		foreach($posts as $post) {
			$c = 0;
			if(preg_match_all("/youtube.com\/(v\/|watch\?v=|embed\/)([a-zA-Z0-9\-_]*)/", $post->post_content, $matches, PREG_SET_ORDER)) {
				$excerpt = ($post->post_excerpt != '') ? $post->post_excerpt : $post->post_title; 
				$permalink = get_permalink($post->id); 

				foreach($matches as $match) {
					$id = $match [2];
					$fix = $c++ == 0 ? '' : ' [Video ' . $c . '] ';

					if(in_array($id, $videos))
						continue;

					array_push($videos, $id);

					$xml .= "\n <url>\n";
					$xml .= " <loc>$permalink</loc>\n";
					$xml .= " <video:video>\n";
					$xml .= "  <video:player_loc allow_embed=\"yes\" autoplay=\"autoplay=1\">http://www.youtube.com/v/$id</video:player_loc>\n";
					$xml .= "  <video:thumbnail_loc>http://i.ytimg.com/vi/$id/hqdefault.jpg</video:thumbnail_loc>\n";
					$xml .= "  <video:title>" . htmlspecialchars($post->post_title) . $fix . "</video:title>\n";
					$xml .= "  <video:description>" . $fix . htmlspecialchars($excerpt) . "</video:description>\n";

					if($_POST['time'] == 1) {
						$duration = youtube_duration_peacedev($id);
						if($duration != 0)
							$xml .= '<video:duration>' . youtube_duration_peacedev($id) . "</video:duration>\n";
					}

					$xml .= '<video:publication_date>' . date(DATE_W3C, strtotime($post->post_date_gmt)) . "</video:publication_date>\n";

					$posttags = get_the_tags($post->id);
					if($posttags) {
						$tagcount = 0;
						foreach($posttags as $tag) {
							if($tagcount++ > 32)
								break;
							$xml .= "<video:tag>$tag->name</video:tag>\n";
						}
					}

					$postcats = get_the_category($post->id);
					if($postcats) {
						foreach($postcats as $category) {
							$xml .= "<video:category>$category->name</video:category>\n";
							break;
						}
					}

					$xml .= "</video:video>\n </url>";
				}
			}
		}
		$xml .= "\n</urlset>";
	}

	$video_sitemap_url = $_SERVER['DOCUMENT_ROOT'] . '/sitemap-video.xml';
	if(IsPeacedevImageSitemapWritable($_SERVER['DOCUMENT_ROOT']) || IsPeacedevImageSitemapWritable($video_sitemap_url)) {
		if(file_put_contents($video_sitemap_url, $xml))
			return true;
	} 
}

# given a video id, get the duration.
# might give this a delay to avoid running into issues with YouTube.
function youtube_duration_peacedev($id) {
	try {
		$ch = curl_init ();
		curl_setopt ($ch, CURLOPT_URL, "http://gdata.youtube.com/feeds/api/videos/$id");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec ($ch);
		curl_close ($ch);

		preg_match("/duration=['\"]([0-9]*)['\"]/", $data, $match);
		return $match [1];
	} catch(Exception $e) {
		# returning 0 if the YouTube API fails for some reason.
		return '0';
	}
}

function schedule_event_multi_sitemap() {
	$st = image_sitemap_loop_peacedev();
	$st2 = mobile_sitemap_loop_peacedev();
	$st3 = video_sitemap_loop_peacedev();
}

add_action('peacedev_hourly_event', 'schedule_event_multi_sitemap');

function cron_add_weekly($schedules) {
	// Adds once weekly to the existing schedules.
	$schedules['weekly'] = array(
		'interval' => 60*60*24*7,
		'display' => __('Once Weekly', 'w3p')
	);
	return $schedules;
}
function cron_add_monthly($schedules) {
	// Adds once weekly to the existing schedules.
	$schedules['monthly'] = array(
		'interval' => 60*60*24*30,
		'display' => __('Once Monthly', 'w3p')
	);
	return $schedules;
}

add_filter('cron_schedules', 'cron_add_weekly');
add_filter('cron_schedules', 'cron_add_monthly');
?>
