# tagOptions
Ajoute aux tag une description, une image d'accroche et l'édition des meta tag : description et keywords


<div role="tabpanel" id="tab1" class="tabpanel">
					<h2>Bienvenue dans l’extension</h2>
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
