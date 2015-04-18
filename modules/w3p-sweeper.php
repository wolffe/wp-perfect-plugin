<?php
$cleanedup = '';

if(isset($_POST['cleanup-rev']))
    $cleanedup .= unit9cleanup('revision');	
if(isset($_POST['cleanup-revall']))
    $cleanedup .= unit9cleanup('revisionall');	
if(isset($_POST['cleanup-spam']))
    $cleanedup .= unit9cleanup('spam');
if(isset($_POST['cleanup-unapproved']))
    $cleanedup .= unit9cleanup('pending');
if(isset($_POST['cleanup-tags']))
    $cleanedup .= unit9cleanup('tag');
if(isset($_POST['cleanup-postmeta']))
    $cleanedup .= unit9cleanup('meta');

if(isset($_POST['cleanup-agent']))
    $cleanedup .= unit9cleanup('commentagent');

if(isset($_POST['cleanup-pingbacks']))
    $cleanedup .= unit9cleanup('pingback');
if(isset($_POST['cleanup-trackbacks']))
    $cleanedup .= unit9cleanup('trackback');
if(isset($_POST['cleanup-transients']))
    $cleanedup .= unit9cleanup('transient');
if(isset($_POST['cleanup-users']))
    $cleanedup .= unit9cleanup('users');
if(isset($_POST['cleanup-trash']))
    $cleanedup .= unit9cleanup('trash');

if(isset($_POST['cleanup-mysql']))
    $cleanedup .= unit9cleanup('optimize');

if(isset($_POST['like_email_query'])) {
    global $wpdb;

	$like_email = $_POST['like_email'];
	$wpdb->query("DELETE FROM " . $wpdb->users . " WHERE user_email LIKE '%" . $like_email . "%'");

	echo '<div class="updated fade"><p>Query performed successfully.</p></div>';
}
if(isset($_POST['remove_cpt_query'])) {
	$n = 0;
	$args = array(
		'post_type' => $_POST['cpt_to_remove'],
		'posts_per_page' => -1
	);
	$todelete = get_posts($args);

	foreach($todelete as $deletethis) {
		wp_delete_post($deletethis->ID, true);
		++$n;
	}

	echo '<div class="updated fade"><p>' . $n . ' post(s) deleted succesfully.</p></div>';
}

function unit9cleanup($type) {
    global $wpdb;

    if($type == 'pingback')
        $wpdb->query("DELETE FROM " . $wpdb->comments . " WHERE comment_type = 'pingback'");
    if($type == 'trackback')
        $wpdb->query("DELETE FROM " . $wpdb->comments . " WHERE comment_type = 'trackback'");
    if($type == 'transient')
        $wpdb->query("DELETE FROM " . $wpdb->prefix . "options WHERE option_name LIKE ('_transient%_feed_%')");

    if($type == 'revision') {
        $revision_ids = $wpdb->get_col($wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE post_type = 'revision' AND post_name NOT LIKE '%-autosave%'"));
        foreach($revision_ids as $revision_id) {
            wp_delete_post_revision($revision_id);
        }
        return '<div class="updated fade"><p>Query performed successfully.</p></div>';
    }
    if($type == 'revisionall') {
        $revision_ids = $wpdb->get_col($wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE post_type = '%s'", 'revision'));
        foreach($revision_ids as $revision_id) {
            wp_delete_post_revision($revision_id);
        }
        return '<div class="updated fade"><p>Query performed successfully.</p></div>';
    }

    if($type == 'spam') {
        $wpdb->query("DELETE FROM " . $wpdb->comments . " WHERE comment_approved = 'spam'");
        $wpdb->query("DELETE FROM " . $wpdb->commentmeta . " WHERE comment_id NOT IN (SELECT comment_id FROM " . $wpdb->comments . ")");
        $wpdb->query("DELETE FROM " . $wpdb->commentmeta . " WHERE meta_key LIKE '%akismet%'");
    }
    if($type == 'pending') {
        $wpdb->query("DELETE FROM " . $wpdb->comments . " WHERE comment_approved = '0'");
        $wpdb->query("DELETE FROM " . $wpdb->commentmeta . " WHERE comment_id NOT IN (SELECT comment_id FROM " . $wpdb->comments . ")");
        $wpdb->query("DELETE FROM " . $wpdb->commentmeta . " WHERE meta_key LIKE '%akismet%'");
    }
    if($type == 'trash') {
        $wpdb->query("DELETE FROM " . $wpdb->comments . " WHERE comment_approved = 'trash'");
        $wpdb->query("DELETE FROM " . $wpdb->commentmeta . " WHERE comment_id NOT IN (SELECT comment_id FROM " . $wpdb->comments . ")");
        $wpdb->query("DELETE FROM " . $wpdb->commentmeta . " WHERE meta_key LIKE '%akismet%'");

        $wpdb->query("DELETE FROM " . $wpdb->posts . " WHERE post_status = 'trash'");
    }

    if($type == 'tag') {
        $wpdb->query("DELETE wt, wtt FROM " . $wpdb->terms . " wt INNER JOIN " . $wpdb->term_taxonomy . " wtt ON wt.term_id = wtt.term_id WHERE wtt.taxonomy = 'post_tag' AND wtt.count = 0");

        $wpdb->query("DELETE FROM " . $wpdb->terms . " WHERE term_id IN (SELECT term_id FROM " . $wpdb->term_taxonomy . " WHERE count = 0)");
        $wpdb->query("DELETE FROM " . $wpdb->term_taxonomy . " WHERE term_id not IN (SELECT term_id FROM " . $wpdb->terms . ")");
        $wpdb->query("DELETE FROM " . $wpdb->term_relationships . " WHERE term_taxonomy_id not IN (SELECT term_taxonomy_id FROM " . $wpdb->term_taxonomy . ")");
    }
    if($type == 'meta') {
        $wpdb->query("DELETE pm FROM " . $wpdb->postmeta . " pm LEFT JOIN " . $wpdb->posts . " wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL");
    }

    if($type == 'users') {
        $wpdb->query("DELETE FROM " . $wpdb->users . " WHERE ID NOT IN (SELECT post_author FROM " . $wpdb->posts . " UNION SELECT user_id FROM " . $wpdb->comments . ")");
        $wpdb->query("DELETE FROM " . $wpdb->usermeta . " WHERE user_id NOT IN (SELECT ID FROM " . $wpdb->users . ")");

        $wpdb->query("DELETE FROM " . $wpdb->comments . " WHERE user_id > 1 AND user_id NOT IN (SELECT DISTINCT post_author FROM " . $wpdb->posts . ")");
        $wpdb->query("DELETE FROM " . $wpdb->usermeta . " WHERE user_id > 1 AND user_id NOT IN (SELECT DISTINCT post_author FROM " . $wpdb->posts . ")");
        $wpdb->query("DELETE FROM " . $wpdb->users . " WHERE ID > 1 AND ID NOT IN (SELECT DISTINCT post_author FROM " . $wpdb->posts . ")");
    }

    if($type == 'optimize') {
        $wpdb->query("OPTIMIZE TABLE " . $wpdb->comments);
        $wpdb->query("OPTIMIZE TABLE " . $wpdb->commentmeta);
        $wpdb->query("OPTIMIZE TABLE " . $wpdb->links);
        $wpdb->query("OPTIMIZE TABLE " . $wpdb->options);
        $wpdb->query("OPTIMIZE TABLE " . $wpdb->postmeta);
        $wpdb->query("OPTIMIZE TABLE " . $wpdb->posts);
        $wpdb->query("OPTIMIZE TABLE " . $wpdb->term_relationships);
        $wpdb->query("OPTIMIZE TABLE " . $wpdb->term_taxonomy);
        $wpdb->query("OPTIMIZE TABLE " . $wpdb->terms);
        $wpdb->query("OPTIMIZE TABLE " . $wpdb->users);
    }

    if($type == 'commentagent') {
        $wpdb->query("UPDATE " . $wpdb->comments . " SET comment_agent = ''");
        $wpdb->query("DELETE FROM " . $wpdb->commentmeta . " WHERE comment_id NOT IN (SELECT comment_id FROM " . $wpdb->comments . ")"); 
    }
    if($type == 'nopings') {
        $wpdb->query("UPDATE " . $wpdb->posts . " SET ping_status = 'closed'");
    }
}

// get amount of each item
function unit9amount($type) {
    global $wpdb;

    if($type == 'users') {
        $wpdb->query("SELECT ID FROM " . $wpdb->users . " WHERE ID NOT IN (SELECT post_author FROM " . $wpdb->posts . " UNION SELECT user_id FROM " . $wpdb->comments . ")");
        return $wpdb->num_rows;
    }

    if($type == 'revision') {
    	$wpdb->query("SELECT ID FROM " . $wpdb->posts . " WHERE post_type = 'revision' AND post_name NOT LIKE '%-autosave%'");
        return $wpdb->num_rows;
    }
    if($type == 'revisionall') {
    	$wpdb->query("SELECT ID FROM " . $wpdb->posts . " WHERE post_type = 'revision'");
        return $wpdb->num_rows;
    }
    if($type == 'trash') {
    	$wpdb->query("SELECT ID FROM " . $wpdb->posts . " WHERE post_type = 'trash'");
        return $wpdb->num_rows;
    }
    if($type == 'spam') {
    	$wpdb->query("SELECT comment_id FROM " . $wpdb->comments . " WHERE comment_approved = 'spam'");
        return $wpdb->num_rows;
    }
    if($type == 'commentagent') {
		$wpdb->query("SELECT comment_id FROM " . $wpdb->comments . " WHERE comment_agent != ''");
		return $wpdb->num_rows;
    }
}

// get size of each item
function unit9size($type) {
    global $wpdb;

    if($type == 'users') {
        $query = $wpdb->get_row("SHOW TABLE STATUS FROM " . $wpdb->users . " ID NOT IN (SELECT post_author FROM " . $wpdb->posts . " UNION SELECT user_id FROM " . $wpdb->comments . ")", ARRAY_A);

        $size = ($query['Avg_row_length'] * unit9amount('users')) / 1024;
        $size = round($size, 2);
        return $size;
    }

    if($type == 'revision') {
        $query = $wpdb->get_row("SHOW TABLE STATUS FROM " . DB_NAME . ' WHERE Name = "' . $wpdb->posts . '"', ARRAY_A);
        $size = ($query['Avg_row_length'] * unit9amount('revision')) / 1024;
        $size = round($size, 2);
        return $size;
    }
    if($type == 'revisionall') {
        $query = $wpdb->get_row("SHOW TABLE STATUS FROM " . DB_NAME . ' WHERE Name = "' . $wpdb->posts . '"', ARRAY_A);
        $size = ($query['Avg_row_length'] * unit9amount('revisionall')) / 1024;
        $size = round($size, 2);
        return $size;
    }
    if($type == 'trash') {
        $query = $wpdb->get_row("SHOW TABLE STATUS FROM " . DB_NAME . ' WHERE Name = "' . $wpdb->posts . '"', ARRAY_A);
        $size = ($query['Avg_row_length'] * unit9amount('trash')) / 1024;
        $size = round($size, 2);
        return $size;
    }
    if($type == 'spam') {
        $query = $wpdb->get_row("SHOW TABLE STATUS FROM " . DB_NAME . ' WHERE Name = "' . $wpdb->posts . '"', ARRAY_A);
        $size = ($query['Avg_row_length'] * unit9amount('spam')) / 1024;
        $size = round($size, 2);
        return $size;
    }
}

function unit9orphans() {
	global $wpdb;

	$wpdb->query("SELECT " . $wpdb->postmeta . " . * FROM " . $wpdb->postmeta . " LEFT JOIN " . $wpdb->posts . " ON " . $wpdb->postmeta . ".post_id = " . $wpdb->posts . ".ID WHERE " . $wpdb->posts . ".ID IS NULL AND " . $wpdb->postmeta . ".post_id IS NOT NULL");
	return $wpdb->num_rows;
}


##############################################
# THIS IS THE PART FOR CALCULATING THE STATS #
##############################################

# Function for getting the total size of the Wordpress database
function DatabaseSize() {
    global $wpdb;

	$totalusedspace = 0;

    $results = $wpdb->get_results("SHOW TABLE STATUS FROM " . DB_NAME, ARRAY_A);
    foreach($results as $row) {
		$usedspace 		 = $row['Data_length'] + $row['Index_length'];
		$usedspace 		 = $usedspace / 1024;
		$usedspace 		 = round($usedspace, 2);
		$totalusedspace += $usedspace;
    }

	return $totalusedspace;
}

# Function for getting the total amount of pingbacks
function PingbacksAmount() {
	global $wpdb;

	$query = "SELECT COUNT(`comment_id`) FROM " . $wpdb->comments . " WHERE `comment_type` = 'pingback'";
	$pingbacks = $wpdb->get_var($query);

	return $pingbacks;
}
# Function for getting the total size of pingbacks
function PingbacksSize() {
	global $wpdb;

	$query 			= "SELECT COUNT(`comment_id`) FROM " . $wpdb->comments . " WHERE `comment_type` = 'pingback'";
	$pingbacks		= $wpdb->get_var($query);

	$query 			= $wpdb->get_row("SHOW TABLE STATUS FROM " . DB_NAME . " WHERE Name = '" . $wpdb->comments . "'", ARRAY_A);
	$size			= ($query['Avg_row_length'] * $pingbacks) / 1024;
	$size			= round($size, 2);

	return $size;
}

# Function for getting the total amount of trackbacks
function TrackbacksAmount() {
	global $wpdb;

	$query = "SELECT COUNT(`comment_id`) FROM " . $wpdb->comments . " WHERE `comment_type` = 'trackback'";
	$trackbacks = $wpdb->get_var($query);

	return $trackbacks;
}
# Function for getting the total size of trackbacks
function TrackbacksSize() {
	global $wpdb;

	$query 			= "SELECT COUNT(`comment_id`) FROM " . $wpdb->comments . " WHERE `comment_type` = 'trackback'";
	$trackbacks		= $wpdb->get_var($query);

	$query 			= $wpdb->get_row("SHOW TABLE STATUS FROM " . DB_NAME . " WHERE Name = '" . $wpdb->comments . "'", ARRAY_A);
	$size			= ($query['Avg_row_length'] * $trackbacks) / 1024;
	$size			= round($size, 2);

	return $size;
}

# Function for getting the total amount of transients
function TransientsAmount() {
	global $wpdb;

	$query = "SELECT COUNT(`option_id`) FROM " . $wpdb->options . " WHERE `option_name` LIKE ('_transient%_feed_%')";
	$transients = $wpdb->get_var($query);

	return $transients;
}
# Function for getting the total size of transients
function TransientsSize() {
	global $wpdb;

	$query 			= "SELECT COUNT(`option_id`) FROM " . $wpdb->options . " WHERE `option_name` LIKE ('_transient%_feed_%')";
	$transients		= $wpdb->get_var($query);

	$query 			= $wpdb->get_row("SHOW TABLE STATUS FROM " . DB_NAME . " WHERE Name = '" . $wpdb->options . "'", ARRAY_A);
	$size			= ($query['Avg_row_length'] * $transients) / 1024;
	$size			= round($size, 2);

	return $size;
}

# Function for getting the total size of unapproved comments of the Wordpress database
function UnapprovedCommentSize() {
	global $wpdb;

	$query 			= "SELECT COUNT(`comment_id`) FROM " . $wpdb->comments . " WHERE `comment_approved` = '0'";
	$unapproved		= $wpdb->get_var($query);

	$query 			= $wpdb->get_row("SHOW TABLE STATUS FROM " . DB_NAME . " WHERE Name = '" . $wpdb->comments . "'", ARRAY_A);
	$size			= ($result['Avg_row_length'] * $unapproved) / 1024;
	$size			= round($size, 2);

	return $size;
}

# Function for getting the total amount of unapproved comments of the Wordpress database
function UnapprovedCommentAmount() {
	global $wpdb;

	$query 			= "SELECT COUNT(`comment_id`) FROM " . $wpdb->comments . " WHERE `comment_approved` = '0'";
	$unapproved		= $wpdb->get_var($query);

	return $unapproved;
}

# Function for getting the total size of unused MySQL Data of the Wordpress database
function UnusedMySQLSizeInnoDB() {
	global $wpdb;

	$totalunusedspace = 0;

    $results = $wpdb->get_results("SHOW TABLE STATUS FROM " . DB_NAME . " WHERE ENGINE = 'InnoDB' LIMIT 1", ARRAY_A);
    foreach($results as $row) {
		$unusedspace 		 = $row['Data_free'] / 1024;
		$unusedspace 		 = round($unusedspace, 2);
		$totalunusedspace   += $unusedspace;
	}

	return $totalunusedspace;
}
function UnusedMySQLSizeMyISAM() {
	global $wpdb;

	$totalunusedspace = 0;

    $results = $wpdb->get_results("SHOW TABLE STATUS FROM " . DB_NAME . " WHERE ENGINE = 'MyISAM'", ARRAY_A);
    foreach($results as $row) {
		$unusedspace 		 = $row['Data_free'] / 1024;
		$unusedspace 		 = round($unusedspace, 2);
		$totalunusedspace   += $unusedspace;
	}

	return $totalunusedspace;
}

# Function for getting the amount of Wordpress data of the Wordpress database
function WordpressData() {
	$useful = DatabaseSize() - UnusedMySQLSizeMyISAM() - UnusedMySQLSizeInnoDB() - unit9size('revisionall') - UnapprovedCommentSize() - unit9size('spam');
	
	return $useful;
}

# Function for getting the total size of unused post meta in the Wordpress database
function UnusedPostMetaSize() {
	global $wpdb;

	$query = "SELECT COUNT(pm.meta_id) FROM " . $wpdb->postmeta . " pm LEFT JOIN " . $wpdb->posts . " wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL";
	$postmeta = $wpdb->get_var($query);

	$query 			= $wpdb->get_row("SHOW TABLE STATUS FROM " . DB_NAME . " WHERE Name = '" . $wpdb->postmeta . "'", ARRAY_A);
	$size			= ($query['Avg_row_length'] * $postmeta) / 1024;
	$size			= round($size, 2);
	
	return $size;
}

# Function for getting the total number of unused post meta in the Wordpress database
function UnusedPostMetaAmount() {
	global $wpdb;
	
	$query = "SELECT COUNT(pm.meta_id) FROM " . $wpdb->postmeta . " pm LEFT JOIN " . $wpdb->posts . " wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL";
	$postmeta = $wpdb->get_var($query);
	$size = round($postmeta, 2);

	return $size;
}

# Function for getting the total size of unused tags in the Wordpress database
function UnusedTagsSize() {
	global $wpdb;

	$query = "SELECT COUNT(wt.term_id) FROM " . $wpdb->terms . " wt INNER JOIN " . $wpdb->term_taxonomy . " wtt ON wt.term_id=wtt.term_id WHERE wtt.taxonomy='post_tag' AND wtt.count=0";
	$tags 			= $wpdb->get_var($query);

    $query 			= $wpdb->get_row("SHOW TABLE STATUS FROM " . DB_NAME . " WHERE Name = '" . $wpdb->terms . "'", ARRAY_A);
	$size			= ($query['Avg_row_length'] * $tags) / 1024;
	$size			= round($size, 2);

	return $size;
}

# Function for getting the total number of unused tags in the Wordpress database
function UnusedTagsAmount() {
	global $wpdb;
	
	$query = "SELECT COUNT(wt.term_id) FROM " . $wpdb->terms . " wt INNER JOIN " . $wpdb->term_taxonomy . " wtt ON wt.term_id=wtt.term_id WHERE wtt.taxonomy='post_tag' AND wtt.count=0";
	$tags 			= $wpdb->get_var($query);
	$size			= round($tags, 2);

	return $size;
}

# Function to divide
function Divide($total,$divide) {
	$divide = ($divide / $total) * 100;
	$divide = round($divide, 2);
	
	return $divide;
}
?>

<?php if($cleanedup <> '') { ?>
<div id="message" class="updated"><?php echo $cleanedup; ?>
</div>
<?php } ?>

<div class="wrap">
	<h2>WordPress Sweeper</h2>

	<div id="poststuff" class="ui-sortable meta-box-sortables">
		<div class="postbox">
			<h3>WordPress Sweeper</h3>
			<div class="inside">
				<p>WordPress Sweeper will clean up your WordPress database, by performing the following actions: removes all post revisions, removes all spam comments, removes all unapproved comments, removes all unused tags, removes all unused post meta, optimizes MySQL tables by removing all unused table space (both MyISAM and InnoDB).</p>
				<p>Check each box in the table below to select which data will be removed. Make sure you have a backup of you WordPress database before cleanup, as all actions are permanent and undoable.</p>

				<hr>
				<?php if(unit9orphans() == 0) { ?>
					<div><span class="dashicons dashicons-yes"></span> Congratulations! You have no orphaned entries in the database!</div>
				<?php } else { ?>
					<div><span class="dashicons dashicons-no"></span> <b><?php echo unit9orphans(); ?></b> orphaned entries detected in the database!</div>
				<?php } ?>
			</div>
		</div>
	</div>

	<div id="poststuff" class="ui-sortable meta-box-sortables">
        <div class="postbox">
            <h3>Detailed Report</h3>
            <div class="inside">
                <form action="#" method="post" id="cleanup-form">
                    <table class="wp-list-table widefat">
                        <thead>
                            <th class="manage-column column-cb check-column"></th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Size</th>
                            <th>Percentage of total</th>
                        </thead>
                        <tr>
                            <td></td>
							<td><strong>Current Database Size</strong></td>
                            <td></td>
							<td><strong><?php echo DatabaseSize(); ?> kb</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>WordPress Data</td>
                            <td></td>
                            <td><?php echo WordpressData(); ?> kb</td>
                            <td><?php echo Divide(DatabaseSize(),WordpressData()); ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row" class="check-column"><input type="checkbox" name="cleanup-rev" id="cleanup-rev"></th>
                            <td>Revisions (no autosaves)</td>
                            <td><?php echo unit9amount('revision'); ?></td>
                            <td><?php echo unit9size('revision'); ?> kb</td>
                            <td><?php echo Divide(DatabaseSize(), unit9size('revision')); ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row" class="check-column"><input type="checkbox" name="cleanup-revall" id="cleanup-revall"></th>
                            <td>Revisions (all)</td>
                            <td><?php echo unit9amount('revisionall'); ?></td>
                            <td><?php echo unit9size('revisionall'); ?> kb</td>
                            <td><?php echo Divide(DatabaseSize(), unit9size('revisionall')); ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row" class="check-column"><input type="checkbox" name="cleanup-trash" id="cleanup-trash"></th>
                            <td>Trash</td>
                            <td><?php echo unit9amount('trash'); ?></td>
                            <td><?php echo unit9size('trash'); ?> kb</td>
                            <td><?php echo Divide(DatabaseSize(), unit9size('trash')); ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row" class="check-column"><input type="checkbox" name="cleanup-spam" id="cleanup-spam"></th>
                            <td>Spam</td>
                            <td><?php echo unit9amount('spam'); ?></td>
                            <td><?php echo unit9size('spam'); ?> kb</td>
                            <td><?php echo Divide(DatabaseSize(), unit9size('spam')); ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row" class="check-column"><input type="checkbox" name="cleanup-unapproved" id="cleanup-unapproved"></th>
                            <td>Unapproved Comments</td>
                            <td><?php echo UnapprovedCommentAmount(); ?></td>
                            <td><?php echo UnapprovedCommentSize(); ?> kb</td>
                            <td><?php echo Divide(DatabaseSize(),UnapprovedCommentSize()); ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row" class="check-column"><input type="checkbox" name="cleanup-pingbacks" id="cleanup-pingbacks"></th>
                            <td>Pingbacks</td>
                            <td><?php echo PingbacksAmount(); ?></td>
                            <td><?php echo PingbacksSize(); ?> kb</td>
                            <td><?php echo Divide(DatabaseSize(), PingbacksSize()); ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row" class="check-column"><input type="checkbox" name="cleanup-trackbacks" id="cleanup-trackbacks"></th>
                            <td>Trackbacks</td>
                            <td><?php echo TrackbacksAmount(); ?></td>
                            <td><?php echo TrackbacksSize(); ?> kb</td>
                            <td><?php echo Divide(DatabaseSize(), TrackbacksSize()); ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row" class="check-column"><input type="checkbox" name="cleanup-transients" id="cleanup-transients"></th>
                            <td>Transients</td>
                            <td><?php echo TransientsAmount(); ?></td>
                            <td><?php echo TransientsSize(); ?> kb</td>
                            <td><?php echo Divide(DatabaseSize(), TransientsSize()); ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row" class="check-column"><input type="checkbox" name="cleanup-users" id="cleanup-users"></th>
                            <td>Users with no posts or comments</td>
                            <td><?php echo unit9amount('users'); ?></td>
                            <td><?php echo unit9size('users'); ?> kb</td>
                            <td><?php echo @Divide(unit9size('users'), unit9amount('users')); ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row" class="check-column"><input type="checkbox" name="cleanup-tags" id="cleanup-tags"></th>
                            <td>Unused (Orphaned) Tags</td>
                            <td><?php echo UnusedTagsAmount(); ?></td>
                            <td><?php echo UnusedTagsSize(); ?> kb</td>
                            <td><?php echo Divide(DatabaseSize(),UnusedTagsSize()); ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row" class="check-column"><input type="checkbox" name="cleanup-postmeta" id="cleanup-postmeta" ></th>
                            <td>Unused (Orphaned) Post Meta</td>
                            <td><?php echo UnusedPostMetaAmount(); ?></td>
                            <td><?php echo UnusedPostMetaSize(); ?> kb</td>
                            <td><?php echo Divide(DatabaseSize(),UnusedPostMetaSize()); ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row" class="check-column"><input type="checkbox" name="cleanup-mysql" id="cleanup-mysql"></th>
                            <td><div class="dashicons dashicons-chart-pie"></div> Unused MySQL Data (MyISAM)</td>
                            <td></td>
                            <td><?php echo UnusedMySQLSizeMyISAM(); ?> kb</td>
                            <td><?php echo Divide(DatabaseSize(),UnusedMySQLSizeMyISAM()); ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row" class="check-column"><input type="checkbox" name="cleanup-mysql" id="cleanup-mysql"></th>
                            <td><div class="dashicons dashicons-chart-pie"></div> Unused MySQL Data (InnoDB)</td>
                            <td></td>
                            <td><?php echo UnusedMySQLSizeInnoDB(); ?> kb</td>
                            <td><?php echo Divide(DatabaseSize(),UnusedMySQLSizeInnoDB()); ?>%</td>
                        </tr>
                        <tr>
                            <th scope="row" class="check-column"><input type="checkbox" name="cleanup-nopings" id="cleanup-nopings"></th>
                            <td><div class="dashicons dashicons-admin-tools"></div> Close trackbacks and pings on all posts</td>
                            <td>n/a</td>
                            <td>n/a</td>
                            <td>n/a</td>
                        </tr>
                        <tr>
                            <th scope="row" class="check-column"><input type="checkbox" name="cleanup-agent" id="cleanup-agent"></th>
                            <td><div class="dashicons dashicons-admin-tools"></div> Cleanup and optimize comment meta</td>
                            <td><?php echo unit9amount('commentagent'); ?></td>
                            <td>n/a</td>
                            <td>n/a</td>
                        </tr>
                    </table>

					<?php
					/**
					$wp_roles = new WP_Roles();
					$names = $wp_roles->get_names();
					echo '<pre>'; print_r($names); echo '</pre>';

					$wp_roles = new WP_Roles();
					$wp_roles->remove_role("your_role");
					/**/
					?>

                    <p>
                        <input type="submit" name="submit" value="Sweep selected items" class="button button-primary"> <label>Did you back up your database?</label>
                    </p>
                </form>
            </div>
        </div>
    </div>

	<div id="poststuff" class="ui-sortable meta-box-sortables">
        <div class="postbox">
            <h3>Additional cleanup<br><small>Did you back up your database?</small></h3>
            <div class="inside">
                <form action="#" method="post" id="cleanup-form">
					<p>
						<input type="text" id="like_email" name="like_email" class="regular-text" placeholder="@hotmail"> 
						<input type="submit" name="like_email_query" value="Perform query" class="button button-secondary">
						<br><label for="like_email">Perform a query: <code>DELETE FROM wp_users WHERE user_email LIKE "%<b>@hotmail</b>%"</code></label>
					</p>
				</form>
                <form action="#" method="post" id="cleanup-form">
					<p>
						<datalist id="cpt_to_remove">
							<select name="cpt_to_remove" id="cpt_to_remove">
								<?php
								$args = array(
									'public' => true,
									//'exclude_from_search' => true,
									//'_builtin' => true
								);
								$post_types = get_post_types($args, 'names');
								foreach($post_types as $post_type) {
									echo '<option value="' . $post_type . '">' . $post_type . '</option>';
								}
								?>
							</select>
						</datalist>
						<input type="text" class="regular-text" id="cpt_to_remove" name="cpt_to_remove" list="cpt_to_remove" placeholder="Select a custom post type to delete...">
						<input type="submit" name="remove_cpt_query" value="Delete custom post type" class="button button-secondary">
						<br><label for="cpt_to_remove">Delete custom post type and all related data</label>
					</p>
				</form>
			</div>
		</div>
	</div>

</div>