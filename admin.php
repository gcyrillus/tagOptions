<?php
	if(!defined('PLX_ROOT')) exit;
	/**
	* Plugin 			tagOptions
	*
	* @CMS required		PluXml 
	* @page				admin.php
	* @version			1.0
	* @date				2024-01-12
	* @author 			G.Cyrille
	**/	
	# Control du token du formulaire
	plxToken::validateFormToken($_POST);
	
	$tagListCheck = array();
	
	# recuperation du tableau de decription des tags
	$plxPlugin->readTagList($plxPlugin::XML_TAGS_OPTIONS);
	
	
		if(isset($_POST['number'])) {	
			$plxPlugin->tagList[trim($_POST['number'])]['description']=$_POST['description'];
			$plxPlugin->tagList[trim($_POST['number'])]['meta_description']=$_POST['meta_description'];
			$plxPlugin->tagList[trim($_POST['number'])]['meta_keywords']=$_POST['meta_keywords'];
			$plxPlugin->tagList[trim($_POST['number'])]['thumbnail']=$_POST['thumbnail'];
			$plxPlugin->tagList[trim($_POST['number'])]['thumbnail_title']=$_POST['thumbnail_title'];
			$plxPlugin->tagList[trim($_POST['number'])]['thumbnail_alt']=$_POST['thumbnail_alt'];
			$plxPlugin->tagList[trim($_POST['number'])]['title_htmltag']=$_POST['title_htmltag'];		
			$plxPlugin->editXMLtagsOptions('update',$plxPlugin::XML_TAGS_OPTIONS );	
			
			header("Location: plugin.php?p=".basename(__DIR__));
			exit;
		}
		if(isset($_GET['regenerate'])) {
			$plxPlugin->editXMLtagsOptions('regenerate',$plxPlugin::XML_TAGS_OPTIONS );	
			}
	
	# Liste des langues disponibles et prises en charge par le plugin
	$aLangs = array($plxAdmin->aConf['default_lang']);	
	
	//global $plxMotor;
	$plxMotor=  plxMotor::getInstance();
	$plxTag = clone $plxMotor;
	$tagsList = $plxTag->getCategories(path('../../configuration/tagsMetas.xml'));


	# init vars / remove unecessary
	
	# initialisation des variables propres à chaque lanque
	$langs = array();
	foreach($aLangs as $lang) {
	# chargement de chaque fichier de langue
	$langs[$lang] = $plxPlugin->loadLang(PLX_PLUGINS.'tagOptions/lang/'.$lang.'.php');
	$var[$lang]['mnuName'] =  $plxPlugin->getParam('mnuName_'.$lang)=='' ? $plxPlugin->getLang('L_DEFAULT_MENU_NAME') : $plxPlugin->getParam('mnuName_'.$lang);
	}
	# init static page var
		# initialisation des variables page statique
	$var['mnuDisplay'] =  $plxPlugin->getParam('mnuDisplay')=='' ? 1 : $plxPlugin->getParam('mnuDisplay');
	$var['mnuPos'] =  $plxPlugin->getParam('mnuPos')=='' ? 2 : $plxPlugin->getParam('mnuPos');
	$var['template'] = $plxPlugin->getParam('template')=='' ? 'static.php' : $plxPlugin->getParam('template');
	$var['url'] = $plxPlugin->getParam('url')=='' ? strtolower(basename(__DIR__)) : $plxPlugin->getParam('url');
	
	# On récupère les templates des pages statiques
	$glob = plxGlob::getInstance(PLX_ROOT . $plxAdmin->aConf['racine_themes'] . $plxAdmin->aConf['style'], false, true, '#^^static(?:-[\\w-]+)?\\.php$#');
	if (!empty($glob->aFiles)) {
	$aTemplates = array();
	foreach($glob->aFiles as $v)
	$aTemplates[$v] = basename($v, '.php');
	} else {
	$aTemplates = array('' => L_NONE1);
	}
	/* end template */
	
	# faut-il mettre à jour la liste des tags ?
		$plxPlugin->getTagsFromConfig();	// recupere le nombre de tags
		$nbTagOptionRecords =count($plxPlugin->tagList);
		$nbTagRegistered = count($plxPlugin->tagListCheck);
		if($nbTagOptionRecords != $nbTagRegistered) echo '<p class="alert red">Des changement ont eu lieu dans les mots clés des articles,
		une mise à jour du tableau d\'option des tags doit être effectuée ! <a href="?p=tagOptions&regenerate">MAJ</a></p>';

	?>
	<link rel="stylesheet" href="<?php echo PLX_PLUGINS."tagOptions/css/tabs.css" ?>" media="all" />
	<p>Donne la possibilité d'ajouter une description et de remplir les champs meta comme ceux que l'on a pour les catégories</p>	
	<h2><?php $plxPlugin->lang("L_ADMIN") ?></h2>
	 <a href="plugin.php?p=<?= basename(__DIR__) ?>&wizard" class="aWizard"><img src="<?= PLX_PLUGINS.basename(__DIR__)?>/img/wizard.png" style="height:2em;vertical-align:middle" alt="Wizard"> Wizard</a>

<?php 
	if(isset($_GET['edit'])) {
		$tag = $_GET['edit'];
	?>
	<form action="plugin.php?p=<?= basename(__DIR__) ?>" method="post" id="form_category">
		<div class="inline-form action-bar">
			<h2><?= $plxPlugin->lang('L_EDITTAG_PAGE_TITLE') ?> "<?php echo $plxPlugin->tagList[$tag]['name'] ?>"</h2>
			<p><a class="back" href="plugin.php?p=<?= basename(__DIR__) ?>"><?= $plxPlugin->lang('L_EDITTAG_BACK_TO_PAGE') ?></a></p>
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" value="<?= $plxPlugin->lang('L_EDITTAG_UPDATE') ?>"/>
		</div>
		
		
		
		<fieldset>
			<div class="grid">
				<div class="col sml-12">
					<label for="id_description"><?= L_EDITCAT_DESCRIPTION ?>&nbsp;:</label>
					<?php plxUtils::printArea('description',plxUtils::strCheck($plxPlugin->tagList[$tag]['description']),0,8) ?>
					<input id="number" name="number" type="hidden" value="<?= $tag ?>">
				</div>
			</div>
			
			
			<div class="grid gridthumb">
				<div class="col sml-12">
					<label for="id_thumbnail">
						<?= L_THUMBNAIL ?>&nbsp;:&nbsp;
						<a title="<?php echo L_THUMBNAIL_SELECTION ?>" id="toggler_thumbnail" href="javascript:void(0)" onclick="mediasManager.openPopup('id_thumbnail', true)" style="outline:none; text-decoration: none">+</a>
					</label>
					<?php plxUtils::printInput('thumbnail',plxUtils::strCheck($plxPlugin->tagList[$tag]['thumbnail']),'text','255',false,'full-width','','onkeyup="refreshImg(this.value)"'); ?>
					<div class="grid" style="padding-top:10px">
						<div class="col sml-12 lrg-6">
							<label for="id_thumbnail_title"><?= L_THUMBNAIL_TITLE ?>&nbsp;:</label>
							<?php plxUtils::printInput('thumbnail_title',plxUtils::strCheck($plxPlugin->tagList[$tag]['thumbnail_title']),'text','255-255',false,'full-width'); ?>
						</div>
						<div class="col sml-12 lrg-6">
							<label for="id_thumbnail_alt"><?php echo L_THUMBNAIL_ALT ?>&nbsp;:</label>
							<?php plxUtils::printInput('thumbnail_alt',plxUtils::strCheck($plxPlugin->tagList[$tag]['thumbnail_alt']),'text','255-255',false,'full-width'); ?>
						</div>
					</div>
					<div id="id_thumbnail_img">
						<?php
							$thumbnail = $plxPlugin->tagList[$tag]['thumbnail'];
							$src = false;
							if(preg_match('@^(?:https?|data):@', $thumbnail)) {
								$src = $thumbnail;
								} else {
								$src = PLX_ROOT.$thumbnail;
								$src = is_file($src) ? $src : false;
							}
							if($src) echo "<img src=\"$src\" title=\"$thumbnail\" />\n";
						?>
					</div>
				</div>
			</div>
			<div class="grid">
				<div class="col sml-12">
					<label for="id_title_htmltag"><?= L_EDITCAT_TITLE_HTMLTAG ?>&nbsp;:</label>
					<?php plxUtils::printInput('title_htmltag',plxUtils::strCheck($plxPlugin->tagList[$tag]['title_htmltag']),'text','50-255'); ?>
				</div>
			</div>
			<div class="grid">
				<div class="col sml-12">
					<label for="id_meta_description"><?= $plxPlugin->lang('L_EDITTAG_META_DESCRIPTION') ?>&nbsp;:</label>
					<?php plxUtils::printInput('meta_description',plxUtils::strCheck($plxPlugin->tagList[$tag]['meta_description']),'text','50-255') ?>
				</div>
			</div>
			<div class="grid">
				<div class="col sml-12">
					<label for="id_meta_keywords"><?= $plxPlugin->lang('L_EDITTAG_META_KEYWORDS') ?>&nbsp;:</label>
					<?php plxUtils::printInput('meta_keywords',plxUtils::strCheck($plxPlugin->tagList[$tag]['meta_keywords']),'text','50-255') ?>
				</div>
			</div>
		</form>

		<?php
		}
		else { ?>
	<div class="scroller">
	<table id="taglist">
		<thead>
			<tr>
				<th><div class="ellipsis"><?= L_ARTICLE_TAGS_FIELD; ?></div></th>
				<th><div class="ellipsis"><?= L_STATICS_ACTION ?></div></th>
				<th><div class="ellipsis"><?= L_EDITCAT_DESCRIPTION ?></div></th>
				<th><div class="ellipsis">meta description</div></th>
				<th><div class="ellipsis">meta keywords</div></th>
				<th><div class="ellipsis"><?= L_THUMBNAIL ?></div></th>
				<th><div class="ellipsis"><?= L_THUMBNAIL_TITLE ?></div></th>
				<th><div class="ellipsis"><?= L_THUMBNAIL_ALT ?></div></th>
				<th><div class="ellipsis"><?= L_EDITCAT_TITLE_HTMLTAG ?></div></th>
				<th><div class="ellipsis"><?= L_ARTICLE_URL_FIELD ?></div></th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach($plxPlugin->tagList as $tag=>$values) {
					echo '			<tr>
				<th><div class="tag">'.$values['name'].'</div></th>'.PHP_EOL.
'				<td><a href="?p='.basename(__DIR__).'&edit='.$tag.'">Editer</a> - <a href="'.$plxAdmin->urlRewrite('?tag/'.$values['url']).'" target="_blank">Voir</a></td>
				<td><div class="tag">'.$values['description'].'</div></td>
				<td><div class="tag">'.$values['meta_description'].'</div></td>
				<td><div class="tag">'.$values['meta_keywords'].'</div></td>
				<td><div class="tag">'.$values['thumbnail'].'</div></td>
				<td><div class="tag">'.$values['thumbnail_title'].'</div></td>
				<td><div class="tag">'.$values['thumbnail_alt'].'</div></td>
				<td><div class="tag">'.$values['title_htmltag'].'</div></td>
				<td><div class="tag">'.$values['url'].'</div></td>
			</tr>';	
				}
			?>
		</tbody>
	</table>
	</div>
	<?php	
			
		}
		
	?>
	<style>
	</style>	