<?php  if(!defined('PLX_ROOT')) exit; ?><div id="tagOptions"><?php 
	# on recupere le tableau des options des tags
	$plxPlug->readTagList($plxPlug::XML_TAGS_OPTIONS);
	
	# on recherche l'index à partir du nom
	$key = array_search(plxUtils::strCheck($plxShow->plxMotor->cibleName), array_column($plxPlug->tagList, 'name'));
	# une description ?
	$desc = $plxPlug->tagList[$key+1]['description'];
	if ($desc !='') echo '<p>'.strip_tags($desc, '<a><strong><b><u><i>').'</p>';
	#une image?
	if($plxPlug->tagList[$key+1]['thumbnail'] !='') {
		echo '<p><img src="'.$plxPlug->tagList[$key+1]['thumbnail'].'" title="'.$plxPlug->tagList[$key+1]['thumbnail_title'].'" alt="'.$plxPlug->tagList[$key+1]['thumbnail_alt'].'"></p>';
	}
	?></div>