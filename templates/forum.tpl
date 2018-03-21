<div id="forum"><div id="forumWrap">

<?php

if ($mode == 'banned') {
	print "<p>$content</p>";
} else {
global $scopeBuster;
global $theNumbers;
//q($scopeBuster);

////////////////////////////////////////////////////////////////////////

//$scopeBuster->forum->isMod = 1;

////////////////////////////////////////////////////////////////////////
if ($_SERVER['DOCUMENT_ROOT'] == 'C:/xampp/xampp/Web') {
	print '<div id="forum"><form id="tehForm" onsubmit="return submitForm(this);" action="?" method="post">';
} else {
	print '<div id="forum"><form id="tehForm" onsubmit="return submitForm(this);">';
}
//$debug[] = $posts;
if (isset($debug)) {
	print '<pre>';
	var_dump($debug, '::debug');
	print '</pre><br /><br />';
}
//q($info, 'info');
//q($mode, 'mode');

$rowItt = 0;
if ($mode == 'index') {

	if ($scopeBuster->forum->isMod) { print '<div class="nav top"><span style="top:11px; position:relative; display:block; margin:5px 0; height:20px; width:100%" class="adNav"><a href="javascript:void(0);" onclick="windowAction (this, \'action=boardAdmin\');">Board Admin</a><a href="javascript:void(0);" onclick="windowAction (this, \'action=banAdmin\');">Ban Admin</a></span></div>'; }

	print '<table id="index">';
		foreach ($boards as $board) {

			if($board['guestable'] == '0' && $board['private'] != '1') {
				$Waff = ' <span style="display:block; padding-top:5px;">[Guest Read Only]</span>';
			} elseif($board['private'] == '1') {
				$Waff = ' <span style="display:block; padding-top:5px;">[Private]</span>';
			} else {
				$Waff = '';
			}
			print "<tr class=\"R{$rowItt}\"><td class=\"boardSummary CA\"><a href=\"#\" onclick=\"windowAction (this, 'action=viewBoard&amp;board=$board[id]');\">$board[title]</a><p>$board[desc]{$Waff}</p></td><td class=\"boardStats CB\">$board[post_count] posts in $board[topic_count] topics.</td><td class=\"boardLatest CA\">";

			if (($board['last_topic_id'] == '') || (intval($board['last_topic_id']) == 0)) {
				print '<p>No topics present yet.</p>';
			} else {
				print "<p>Latest post in <a href=\"#\" onclick=\"windowAction (this, 'action=viewTopic&amp;topic=$board[last_topic_id]');\">$board[last_topic_title]</a> by $board[last_poster]</p>";
			}
			print '</td></tr>';
			$rowItt = ($rowItt==0) ? 1 : 0;
		}
	print '</table>';

} elseif ($mode == 'board') {

	print '<div class="nav top"><a href="javascript:void(0);" onclick="windowAction (this, \'action=forum\');">Forum Home</a>';
	//
	$theNumbers->draw('viewBoard', 'board', $info['board'], true);

	if ($scopeBuster->forum->isMod) { print '<span class="adNav"><a href="javascript:void(0);" onclick="windowAction (this, \'action=topicAdmin&amp;board='.$info['board'].'&amp;pageNumber='.(array_key_exists('pageNumber', $scopeBuster->input) ? $scopeBuster->input['pageNumber'] : 0).'\');">Topic Admin</a></span>'; }

	print '</div>'."<h2 class=\"boardTitle\">Viewing board: <span>$info[boardTitle]</span></h2><table id=\"topicList\"><thead><tr><th class=\"CA\">Post Title</th><th class=\"CB\">Created</th><th class=\"CA\">Replies</th><th class=\"CB\">Last post</th></tr></thead><tbody>";

	if ($info['empty'] == 'true') {

		print "<tr class=\"R0\"><td class=\"post CA\" colspan=\"4\"><p class=\"cellNotice\">No topics to display.</p></td></tr>";

	} else {
		$icons = array(1 => ' <acronym title="announcement">[A]</acronym>', 2 => ' <acronym title="sticky">(S)</acronym>', 3 => '');
		foreach ($topics as $topic) {
		//var_dump($topic);
			print "<tr class=\"R{$rowItt}\"><td class=\"post CA\"><a href=\"#\" onclick=\"windowAction (this, 'action=viewTopic&amp;topic=$topic[id]');\">$topic[title]</a></td><td class=\"posterInfo CB\">Posted by $topic[posterName] ";
			print "on $topic[date]";
			print "{$icons[$topic['type']]}</td>";
			print "<td class=\"postCount CA\">$topic[postcount]</td>";
			print "<td class=\"lastInfo CB\">Last post by $topic[lastposter] ";
			print "on $topic[lastpost]</td></tr>";
			$rowItt = ($rowItt==0) ? 1 : 0;
		}
	}
	print '</tbody></table><div class="entry"><input type="hidden" class="hidden" name="action" value="newTopic" /><input type="hidden" class="hidden" name="board" value="'.$info['board'].'" /><input id="titleBox" name="title" type="text" value="topic title" onclick="this.value=\'\'; this.onclick=\'\';" /><textarea name="post"></textarea><input id="save" name="Save" type="submit" value="Create Topic" /></div><div class="nav base"><a href="javascript:void(0);" onclick="windowAction (this, \'action=forum\');">Forum Home</a></div>';

	$theNumbers->draw('viewBoard', 'board', $info['board']);
//q($theNumbers, '###');


} elseif ($mode == 'topic') {

	print '<div class="nav top"><a href="javascript:void(0);" onclick="windowAction (this, \'action=forum\');">Forum Home</a><a href="javascript:void(0);" onclick="windowAction (this, \'action=viewBoard&amp;board='.$info['board'].'\');">Return to '.$info['boardTitle'].'</a></div>';

	$theNumbers->draw('viewTopic', 'topic', $info['topic'], true);

	print '<h2 class="topicTitle">Viewing thread: <span>'.$info['topicTitle'].'</span></h2><table id="postList">';
		foreach ($posts as $post) {
			print "<tr class=\"R{$rowItt}\"><td class=\"info CA\"><p>$post[poster]";
			if ($scopeBuster->forum->isMod) { print '<span class="userID"> (#'.$post['posterID'].')</span>'; }
			print "</p><span class=\"number\">#$post[number]</span><span class=\"info\">Posted on $post[created]</span>";

			if ($post['canEdit']) {
				print '<span class="miniMod"><a href="javascript:void(0);" onclick="windowAction (this, \'action=editPost&amp;topic='.$info['topic'].'&amp;postID='.$post['ID'].'\');"><acronym title="edit this post">[E]</acronym></a>';
				if ($scopeBuster->forum->isMod) { print '<a href="javascript:void(0);" onclick="var r=confirm(\'Are you sure you want to remove post #'.$post['number'].'?\'); if (r==true) { windowAction (this, \'action=deletePost&amp;postID='.$post['ID'].'\');}"><acronym title="delete this post">[X]</acronym></a>'; }
				print '</span>';
			}
			print '</td><td class="post CB">'.$post['content'];
			if ($post['edits'] != 0) {
				print "<span class=\"editInfo\">Last edited on $post[editedTime] by $post[editBy] (edited $post[edits] times in total).";
			}
			print '</td></tr>';
			$rowItt = ($rowItt==0) ? 1 : 0;
		}
		print '</table><div class="entry"><input type="hidden" class="hidden" name="action" value="reply" /><input type="hidden" class="hidden" name="topic" value="'.$info['topic'].'" /><textarea name="post"></textarea><input id="save" name="Save" type="submit" value="Reply" /></div><div class="nav base"><a href="javascript:void(0);" onclick="windowAction (this, \'action=forum\');">Forum Home</a><a href="javascript:void(0);" onclick="windowAction (this, \'action=viewBoard&amp;board='.$info['board'].'\');">Return to '.$info['boardTitle'].'</a></div>';

$theNumbers->draw('viewTopic', 'topic', $info['topic']);



} elseif ($mode == 'editPost') {

	print '<div class="nav top"><a href="javascript:void(0);" onclick="windowAction (this, \'action=forum\');">Forum Home</a><a href="javascript:void(0);" onclick="windowAction (this, \'action=viewBoard&amp;board='.$info['board'].'\');">'.$info['boardTitle'].'</a><a href="javascript:void(0);" onclick="windowAction (this, \'action=viewTopic&amp;topic='.$info['topic'].'\');">Back to Main Topic</a></div><div class="editing">';

	if ($info['firstReply'] == true) {
		print '<input id="titleBox" name="title" type="text" value="'.$info['topicTitle'].'" />';
	}
	print '<input type="hidden" class="hidden" name="topic" value="'.$info['topic'].'" /><input type="hidden" class="hidden" name="action" value="editedPost" /><input type="hidden" class="hidden" name="postID" value="'.$info['postID'].'" /><textarea name="post">'.$info['post'].'</textarea><input id="save" name="Save" type="submit" value="Save" /></div>';

	print '<div class="nav base"><a href="javascript:void(0);" onclick="windowAction (this, \'action=forum\');">Forum Home</a><a href="javascript:void(0);" onclick="windowAction (this, \'action=viewBoard&amp;board='.$info['board'].'\');">'.$info['boardTitle'].'</a><a href="javascript:void(0);" onclick="windowAction (this, \'action=viewTopic&amp;topic='.$info['topic'].'\');">Back to Main Topic</a></div>';


} elseif ($mode == 'topicAdmin') {//
print '<table id="topicAdmin">
	<thead>
		<tr>
			<th class="CA">Topic Title</th>
			<th class="CB">Announcement</th>
			<th class="CA">Sticky</th>
			<th class="CB">Normal</th>
			<th class="CA">No Change</th>
			<th class="CB">Delete Topic?</th>
		</tr>';

	function labelUp($topicType, $ID, $current) {
		if ($current == 1) {
			print '<b>
				<input type="radio" name="topicType_'.$ID.'" value="1" />
				</b>
			</td>
			<td class="Three CA">
				<input type="radio" name="topicType_'.$ID.'" value="2" />
			</td>
			<td class="Four CB">
				<input type="radio" name="topicType_'.$ID.'" value="3" />
			</td>
			<td class="Five CA">
				<input type="radio" name="topicType_'.$ID.'" value="unchanged" checked="checked" />';
		} elseif ($current == 2) {
			print '<input type="radio" name="topicType_'.$ID.'" value="1" />
			</td>
			<td class="Three CA">
					<b><input type="radio" name="topicType_'.$ID.'" value="2" /></b>
				</td>
				<td class="Four CB">
					<input type="radio" name="topicType_'.$ID.'" value="3" />
				</td>

				<td class="Five CA">
					<input type="radio" name="topicType_'.$ID.'" value="unchanged" checked="checked" />';
		} else {
			print '<input type="radio" name="topicType_'.$ID.'" value="1" />
			</td>
			<td class="Three CA">
				<input type="radio" name="topicType_'.$ID.'" value="2" />
			</td>
			<td class="Four CB">
				<b><input type="radio" name="topicType_'.$ID.'" value="3" /></b>
			</td>
			<td class="Five CA">
				<input type="radio" name="topicType_'.$ID.'" value="unchanged" checked="checked" />';
		}

	}

	print '</thead><tbody>';
	foreach ($topics as $topic) {
	?><tr class="R<?=$rowItt?>">
		<td class="One CA">
			<a href="javascript:void(0);" onclick="windowAction (this, 'action=viewTopic&amp;topic=<?=$topic['id']?>');"><?=$topic['title']?></a>
		</td>
		<td class="Two CB">
			<?php labelUp($topic['type'], $topic['id'], $topic['type']); ?>
		</td>
		<td class="Six CB">
			<span style="border:1px solid #401; background-color:#E8A8C8; padding:3px 5px;">
				<input type="checkbox" name="delete_<?=$topic['id']?>" value="true" />
			</span>
		</td>
	</tr><?php
	}
	
	print '</tbody>
	</table><br />

	<input type="hidden" class="hidden" name="action" value="topicAdminSave" />
	<input type="hidden" class="hidden" name="board" value="'.$board.'" />
	<input id="save" name="Save" type="submit" value="Save" />
	<div class="nav base">
		<a href="javascript:void(0);" onclick="windowAction (this, \'action=forum\');">Forum Home</a>
		<a href="javascript:void(0);" onclick="windowAction (this, \'action=viewBoard&amp;board='.$board.'\');">Return to Topic Listing</a>
	</div>';

} elseif ($mode == 'boardAdmin') {

print '<div class="nav top">
	<a href="javascript:void(0);" onclick="windowAction (this, \'action=forum\');">Back to Forum Home</a>
</div>
<table style="margin-top:15px;" width="90%" id="List" class="board">
	<tbody id="tbody">';
$X=0;
foreach ($boards as $board) {

//*
?><tr>
	<td style="width:20px;"><img src="static/images/forum/ArrowUp.png" alt="Move up" onclick="Shift(this, -1);" /><img src="static/images/forum/ArrowDown.png" alt="Move down" onclick="Shift(this, 1);" /></td>
	<td style="width:1%;"><span style="display:none;"><?=$board['id']?></span><input type="hidden" class="hidden" name="Position<?=$board['id']?>" value="<?=$X?>" /></td>
	<td class="ghostRow"><input type="text" class="nameBox" name="TitleNum<?=$board['id']?>" value="<?=$board['title']?>" /> <input type="text" class="descBox" name="DescNum<?=$board['id']?>" value="<?=$board['desc']?>" /></td>
	<td style="width:62px; text-align:right; padding-left:0px;"><label>Private? <input type="checkbox" name="Priv<?=$board['id']?>" id="Priv<?=$board['id']?>"<?php $board['private'] == 1 ? print ' checked="checked"' : print '' ?> /></label> <label>Guests? <input type="checkbox" name="guest_<?=$board['id']?>" <?=(($board['guestable'] == 1) ? ' checked="checked"' : '')?> /></label></td>
	<td style="width:1%;">
		<input type="hidden" class="hidden" name="Existing<?=$board['id']?>" value="<?=$board['id']?>" />
		<?php if ($board['id'] != 0) { ?><img id="<?=$board['id']?>" name="<?=$board['id']?>" src="static/images/forum/Cross.png" alt="Delete?" onclick="Purge(this, <?=$board['id']?>, 'this board');" /><?php }?>
		<input type="hidden" class="hidden" name="DelNum<?=$board['id']?>" id="DelNum<?=$board['id']?>" value="Preserve" />
	</td>
</tr><?php
//*/
//var_dump($board);
$X++;
}

print '</tbody></table>
	<input id="action" name="action" type="hidden" class="hidden" value="boardAdminSave" />
	<input id="totalRows" name="totalRows" type="hidden" class="hidden" value="'.count($boards).'" />
	<input id="save" name="Save" type="button" onclick="sortEm(this)" value="Save Changes" />
	<a id="addNewB" href="javascript:void(0);" onclick="Add();">Add New Board</a><br /><br />';

//add the ban interface (poach this code; just need user ID &amp; reason), redirect to board index when done. add a 'bans' button near the board admin one


//var_dump($board);


} elseif ($mode == 'banAdmin') {

print '<div class="nav top"><a href="javascript:void(0);" onclick="windowAction (this, \'action=forum\');">Back to Forum Home</a></div><br /><br /><table class="ban" style="margin-top:15px;" width="90%" id="List"><tbody id="tbody">';

foreach ($bans as $ban) {

//*
?>
<tr><td><?=getName($ban['user'])?> - <?=$ban['reason']?></td><td><?=date(DATETIME, $ban['time'])?></td><td style="width:1%;"><img id="<?=$ban['ID']?>" name="I<?=$ban['ID']?>" src="static/images/forum/Cross.png" alt="Delete?" onclick="Purge(this, <?=$ban['ID']?>, 'this ban');" /><input type="hidden" class="hidden" name="Existing<?=$ban['ID']?>" value="<?=$ban['ID']?>" /><input type="hidden" class="hidden" name="DelNum<?=$ban['ID']?>" id="DelNum<?=$ban['ID']?>" value="Preserve" /></td></tr><?php
//*/
//var_dump($board);

}

print '</tbody></table>';
if (count($bans) == 0) print '<p>No bans to display.</p>';
print '<input id="action" name="action" type="hidden" class="hidden" value="banAdminSave" /><input id="totalRows" name="totalRows" type="hidden" class="hidden" value="'.count($bans).'" /><input id="save" name="Save" type="button" onclick="sortEm()" value="Save Changes" /><a id="addNewB" href="javascript:void(0);" onclick="AddBan();">Add New Ban</a><br /><br />';


}//end type if

}

print '</form></div></div></div>';
?>
