<?php
	if(!defined('PLX_ROOT')) exit; 
	/**
		* Plugin 			tagOptions
		*
		* @CMS required		PluXml 
		* @page				-wizard.php
		* @version			1.0
		* @date				2024-01-12
		* @author 			G.Cyrille
	**/		
	
	# pas d'affichage dans un autre plugin !	
	if(isset($_GET['p'])&& $_GET['p'] !== 'tagOptions' ) {goto end;}
	
	# on charge la class du plugin pour y accéder
	$plxMotor = plxMotor::getInstance();
	$plxPlugin = $plxMotor->plxPlugins->getInstance( 'tagOptions'); 
	
	# On vide la valeur de session qui affiche le Wizard maintenant qu'il est visible.
	if (isset($_SESSION['justactivatedtagOptions'])) {unset($_SESSION['justactivatedtagOptions']);}
	
	# initialisation des variables propres à chaque lanque 
	$langs = array();
	
	# initialisation des variables communes à chaque langue	
	$var = array();
	
	
	#affichage
?>
<link rel="stylesheet" href="<?= PLX_PLUGINS ?>tagOptions/css/wizard.css" media="all" />
<input id="closeWizard" type="checkbox">
<div class="wizard">	
	<div class="container">	
		<div class='title-wizard'>
			<h2>Edition et Option des mots clés<br><?= $plxPlugin->aInfos['version']?></h2>
			<img src="<?php echo PLX_PLUGINS. 'tagOptions'?>/icon.png">
			<div><q> Made in FRANCE <br> By <?= $plxPlugin->aInfos['author']?> </q></div>
		</div>
		<p></p>
		
		<div id="tab-status">
			<span class="tab active">1</span>
		</div>		
		<form action="plugin.php?p=<?php echo 'tagOptions' ?>"  method="post">
			<div role="tab-list">		
				<div role="tabpanel" id="tab1" class="tabpanel">
					<h2>Bienvenue dans l’extension <br><b style="font-family:cursive;color:crimson;font-variant:small-caps;font-size:2em;vertical-align:-.5rem;display:inline-block;"><?= $plxPlugin->aInfos['title']?></b></h2>
					<p>Elle permet de donner à vos page de Mots Clés:</p>
					<ul>
						<li> une image d'accroche</li>
						<li> Une description texte</li>
						<li> ainsi que de remplir les metas 'description' et 'keywords'</li>
					<li> et de personnaliser la balise title</li></ul>
				</div>	
				<div role="tabpanel" id="tab2" class="tabpanel hidden title">
					<h2>Activation</h2>
					<p><br><br></p>
					<p>&Agrave; la premiere activation, le plugin génére un fichier de configuration <code>tagOptions.xml</code>.</p>
					<p>Vous pourrez aussitôt l'éditer.</p>
				</div>		
				<div role="tabpanel" id="tabUpdate" class="tabpanel hidden">
					<h2>Mise à jour des mots clés.</h2>
					<p>Vous pouvez à tous moments editer les options de vos mots clés.</p>
					<p>Si de nouveaux mots clés sont inserés depuis vos articles, un message vous indique de mettre à jour votre liste</p>
				</div>			
				<div role="tabpanel" id="tabTemplate" class="tabpanel title hidden">
					<h2>Description et image d'accroche</h2>
					<p>Un hook est à inserer dans le fichier tags.php de votre thème.</p>
				</div>				
				<div role="tabpanel" id="tabHooked" class="tabpanel hidden">
					<h2>Hook et édition du thème</h2>
					<p>Cette fonction est simmilaire à celle des description et image des catégorie, elle s'insere logiquement entre le fil d'Ariane et la liste des articles.
					</p>
					<p>Reperer cette portion de code dans <code>themes/nom_du_theme_actif/tags.php</p>
					<pre><code>&lt;ul class="repertory menu breadcrumb">
    &lt;li>&lt;a href="&lt;?php $plxShow->racine() ?>">&lt;?php $plxShow->lang('HOME'); ?>&lt;/a>&lt;/li>
    &lt;li>&lt;?php $plxShow->tagName(); ?>&lt;/li>
&lt;/ul></code></pre>
					<p>Et remplacer la avec </p>
					<pre><code>&lt;ul class="repertory menu breadcrumb">
    &lt;li>&lt;a href="&lt;?php $plxShow->racine() ?>">&lt;?php $plxShow->lang('HOME'); ?>&lt;/a>&lt;/li>
    &lt;li>&lt;?php $plxShow->tagName(); ?>&lt;/li>
&lt;/ul>
&lt;?php eval($plxShow->callHook('tagDescription')) ?></code></pre>
				</div>			
				<div role="tabpanel" id="tabEnd" class="tabpanel hidden title">
					<h2>Céfini</h2>
					<p>C'est la fin de ce wiz'aide.</p>
				</div>		
				<div class="pagination">
					<a class="btn hidden" id="prev"><?php $plxPlugin->lang('L_PREVIOUS') ?></a>
					<a class="btn" id="next"><?php $plxPlugin->lang('L_NEXT') ?></a>
					<?php echo plxToken::getTokenPostMethod().PHP_EOL ?>
					<button class="btn btn-submit hidden" name="submit" id="submit"><?php $plxPlugin->lang('L_CLOSE') ?></button>
				</div>
			</div>		
		</form>			
		<p class="idConfig">
			<?php
				if(file_exists(PLX_PLUGINS. 'tagOptions/admin.php')) {echo ' 
				<a href="/core/admin/plugin.php?p='. basename(__DIR__ ).'">Page d\'administration '. basename(__DIR__ ).'</a>';}
			?>
			<label for="closeWizard"> Fermer </label>
		</p>	
	</div>	
	<script src="<?= PLX_PLUGINS ?>tagOptions/js/wizard.js"></script>
</div>
<?php end: // FIN! ?>				
