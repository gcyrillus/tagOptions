<?php if(!defined('PLX_ROOT')) exit;
	/**
		* Plugin 			tagOptions
		*
		* @CMS required			PluXml 
		*
		* @version			1.0
		* @date				2024-01-12
		* @author 			G.Cyrille
	**/
	class tagOptions extends plxPlugin {
		
		
		
		const BEGIN_CODE = '<?php' . PHP_EOL;
		const END_CODE = PHP_EOL . '?>';
		const XML_TAGS_OPTIONS = PLX_ROOT . PLX_CONFIG_PATH . 'tagsOptions.xml';
		public $tagList = array();
		public $tagListCheck = array();
		public $lang = ''; 
		public $pageTagTitle ='Mot clé';
		
		
		
		
		public function __construct($default_lang) {
			# appel du constructeur de la classe plxPlugin (obligatoire)
			parent::__construct($default_lang);
			
			
			# droits pour accèder à la page admin.php du plugin
			$this->setAdminProfil(PROFIL_ADMIN, PROFIL_MANAGER);
			
			# Personnalisation du menu admin
            $this->setAdminMenu('Mots clés', 3, 'Descriptions et metadonnées des étiquettes');
			
			
			
			# Declaration des hooks		
			$this->addHook('AdminTopBottom', 'AdminTopBottom');
			
			$this->addHook('plxShowConstruct', 'plxShowConstruct');
			
			$this->addHook('plxShowPageTitle', 'plxShowPageTitle');
			
			$this->addHook('tagDescription', 'tagOptionswidget');
			$this->addHook('wizard', 'wizard');
			$this->addHook('MyHsitemapTags', 'MyHsitemapTags');
			$this->addHook('plxShowMeta', 'plxShowMeta');
			$this->addHook('plxShowArtTags', 'plxShowArtTags');
			$this->addHook('plxShowTagList', 'plxShowTagList');
			$this->addHook('plxShowTagFeed', 'plxShowTagFeed');
			
			
		}
		
		# Activation / desactivation
		
		public function OnActivate() {
			# code à executer à l’activation du plugin
			if(!file_exists(self::XML_TAGS_OPTIONS)){
				if (touch(self::XML_TAGS_OPTIONS)) {
					$this->editXMLtagsOptions('init',self::XML_TAGS_OPTIONS );
				}
			}
			# activation du wizard
			$_SESSION['justactivated'.basename(__DIR__)] = true;
		}
		
		public function OnDeactivate() {
			# code à executer à la désactivation du plugin
		}	
		
		
		
		
		/**
			* Méthode qui affiche un message si le plugin n'a pas la langue du site dans sa traduction
			* Ajout gestion du wizard si inclus au plugin
			* @return	stdio
			* @author	Stephane F
		**/
		
		public function AdminTopBottom() {
			
			echo '<?php
			$file = PLX_PLUGINS."'.$this->plug['name'].'/lang/".$plxAdmin->aConf["default_lang"].".php";
			if(!file_exists($file)) {
			echo "<p class=\\"warning\\">'.basename(__DIR__).'<br />".sprintf("'.$this->getLang('L_LANG_UNAVAILABLE').'", $file)."</p>";
			plxMsg::Display();
			}
			?>';
			
			# affichage du wizard à la demande
			if(isset($_GET['wizard'])) {$_SESSION['justactivated'.basename(__DIR__)] = true;}
			# fermeture session wizard
			if (isset($_SESSION['justactivated'.basename(__DIR__)])) {
				unset($_SESSION['justactivated'.basename(__DIR__)]);
				$this->wizard();
			}
			
		}
		
		/**
			* Méthode statique qui affiche le widget
			*
		**/
		
		public static function tagOptionswidget($widget=false) {
			
			# récupération d'une instance de plxMotor
			$plxShow = plxShow::getInstance();
			$plxMotor = plxMotor::getInstance();
			$plxPlug = $plxMotor->plxPlugins->getInstance(basename(__DIR__));		
			include(PLX_PLUGINS.basename(__DIR__).'/widget.'.basename(__DIR__).'.php');
		}
		
		
		/** 
			* Méthode wizard
			* 
			* Descrition	:
			* @author		: '.$_POST['title'].'
			* 
		**/
		
		# insertion du wizard
		public function wizard() {
			# uniquement dans les page d'administration du plugin.
			if(basename(
			$_SERVER['SCRIPT_FILENAME']) 			=='parametres_plugins.php' || 
			basename($_SERVER['SCRIPT_FILENAME']) 	=='parametres_plugin.php' || 
			basename($_SERVER['SCRIPT_FILENAME']) 	=='plugin.php'
			) 	{	
				include(PLX_PLUGINS.__CLASS__.'/lang/'.$this->default_lang.'-wizard.php');
			}
		}
		
		/**
			* Méthode de traitement du hook plxShowConstruct
			*
			* @return	stdio
			* @author	Stephane F
		**/
		
		public function plxShowConstruct() {
			$this->readTagList(self::XML_TAGS_OPTIONS);		
		}
		
		
		
		/**
			* Méthode qui renseigne le titre de la page dans la balise html <title>
			*
			* @return	stdio
			* @author	Stephane F
		**/
		
		public function plxShowPageTitle() {
			echo self::BEGIN_CODE;
		?>
		# maj balise title pour les tags
		if(preg_match('#^tag/([\w-]+)#',$this->plxMotor->get,$capture)) {	
		$key = array_search($capture[1], array_column($this->plxMotor->plxPlugins->aPlugins["<?= basename(__DIR__)?>"]->tagList, 'url'));
		echo $this->plxMotor->plxPlugins->aPlugins["<?= basename(__DIR__)?>"]->tagList[$key+1]['name'];
		return true;
		}
		
		<?php
			echo self::END_CODE;
		}
		
		
		
		
		
		/** 
			* Méthode MyHsitemapTags
			* 
			* Descrition	:
			* @author		: TheCrok
			* 
		**/
		
		public function MyHsitemapTags() {
			# code à executer
			
			
			
		}
		
		
		/** 
			* Méthode plxShowMeta
			* 
			* Descrition	:
			* @author		: TheCrok
			* 
		**/
		
		public function plxShowMeta() {
			# code à executer
			
			echo self::BEGIN_CODE;
		?>
		$meta = strtolower($meta);
		if($meta == 'author') return true;
		# maj balise title pour les tags
		if(preg_match('#^tag/([\w-]+)#',$this->plxMotor->get,$capture)) {	
		$key = array_search($capture[1], array_column($this->plxMotor->plxPlugins->aPlugins["<?= basename(__DIR__)?>"]->tagList, 'url'));
		$datas = $this->plxMotor->plxPlugins->aPlugins["<?= basename(__DIR__)?>"]->tagList[$key+1]['meta_'.$meta];
		}
		if($this->plxMotor->mode == 'tags') {
		if($meta == 'description') echo '	<meta name="description" content="'.$datas.'" />'.PHP_EOL;
		if($meta == 'keywords')  	echo '	<meta name="keywords" content="'.$datas.'" />'.PHP_EOL;
		return true;
		
		
		}
		<?php
			echo self::END_CODE;		
		}
		
		
		/** 
			* Méthode plxShowArtTags
			* 
			* Descrition	:
			* @author		: TheCrok
			* 
		**/
		
		public function plxShowArtTags() {
			# code à executer
			
			
			echo self::BEGIN_CODE;
		?>
		// here the code to inject into native function at hook's position
		
		// return true; // use if needed : stops native function at this point
		<?php
			echo self::END_CODE;
			
			
		}
		
		
		/** 
			* Méthode plxShowTagList
			* 
			* Descrition	:
			* @author		: TheCrok
			* 
		**/
		
		public function plxShowTagList() {
			# code à executer
			
			
			echo self::BEGIN_CODE;
		?>
		// here the code to inject into native function at hook's position
		
		// return true; // use if needed : stops native function at this point
		<?php
			echo self::END_CODE;
			
			
		}
		
		
		/** 
			* Méthode plxShowTagFeed
			* 
			* Descrition	:
			* @author		: TheCrok
			* 
		**/
		
		public function plxShowTagFeed() {
			# code à executer
			
			
			echo self::BEGIN_CODE;
		?>
		// here the code to inject into native function at hook's position
		
		// return true; // use if needed : stops native function at this point
		<?php
			echo self::END_CODE;
			
			
		}
		
		
		# fonctions spécifique au plugin
		
		/* edition du fichier "Options" des étiquettes."
		*/
		
		public function editXMLtagsOptions($setup='',$filetag=self::XML_TAGS_OPTIONS) {
			$tagArray=array();
			$i=000;
			# create file on Install first time
			if($setup == 'init') {
				$this->getTagsFromConfig();
				$tagArray=$this->tagListCheck;
			}
			if($setup=='update') {
				$tagArray=$this->tagList;
			}		
			
			
			if($setup=="regenerate") {			
				$this->getTagsFromConfig();
				$i= count($this->tagList);
				foreach($this->tagListCheck as $k => $v) {
					$look='';
					$lookFor = array_search($k, array_column($this->tagList, 'name'));
					if($lookFor !='') continue;
					
					$i++;
					$this->tagList[$i]['number']=''.$i.'';
					$this->tagList[$i]['name']=$k;
					$this->tagList[$i]['url']=$this->tagListCheck[$k]['url'];
					$this->tagList[$i]['description']=$this->tagListCheck[$k]['description'];
					$this->tagList[$i]['meta_description']=$this->tagListCheck[$k]['meta_description'];
					$this->tagList[$i]['meta_keywords']=$this->tagListCheck[$k]['meta_keywords'];
					$this->tagList[$i]['title_htmltag']=$this->tagListCheck[$k]['title_htmltag'];
					$this->tagList[$i]['thumbnail']=$this->tagListCheck[$k]['thumbnail'];
					$this->tagList[$i]['thumbnail_alt']=$this->tagListCheck[$k]['thumbnail_alt'];
					$this->tagList[$i]['thumbnail_title']=$this->tagListCheck[$k]['thumbnail_title'];
					
					
					echo $this->tagListCheck[$k]['url'];
					
				}
				$tagArray=$this->tagList;
				$i=0;
				
			}
			# On génére le fichier XML
			$xml = "<?xml version=\"1.0\" encoding=\"".PLX_CHARSET."\"?>\n";
			$xml .= "<document>\n";
			
			foreach($tagArray as $tag_id => $tag) {
				$i++;
				if($setup=='update') {$i=$tag_id;}
				$xml .= "\t<tag  number=\"".$i."\" url=\"".$tag['url']."\" >";
				$xml .= "<name><![CDATA[".$tag['name']."]]></name>";
				$xml .= "<description><![CDATA[".$tag['description']."]]></description>";
				$xml .= "<meta_description><![CDATA[".plxUtils::cdataCheck($tag['meta_description'])."]]></meta_description>";
				$xml .= "<meta_keywords><![CDATA[".plxUtils::cdataCheck($tag['meta_keywords'])."]]></meta_keywords>";
				$xml .= "<title_htmltag><![CDATA[".plxUtils::cdataCheck($tag['title_htmltag'])."]]></title_htmltag>";
				$xml .= "<thumbnail><![CDATA[".plxUtils::cdataCheck($tag['thumbnail'])."]]></thumbnail>";
				$xml .= "<thumbnail_alt><![CDATA[".plxUtils::cdataCheck($tag['thumbnail_alt'])."]]></thumbnail_alt>";
				$xml .= "<thumbnail_title><![CDATA[".plxUtils::cdataCheck($tag['thumbnail_title'])."]]></thumbnail_title>";
				# Hook plugins
				// just for infos because there's one for categories
				$xml .= "</tag>\n";
			}
			$xml .= "</document>";
			# On écrit le fichier
			if(plxUtils::write($xml, $filetag))
			return plxMsg::Info(L_SAVE_SUCCESSFUL);
			else 
			return plxMsg::Error(L_SAVE_ERR.' '.path($filetag));		
			
			
			
		}
		
		/*
			* Methode qui extrait les tags dans un tableau
			*
		*/
		
		public function getTagsFromConfig() {
			
			global $plxAdmin;		
			# On verifie qu'il y a des tags
			if ($plxAdmin->aTags) {
				$now = date('YmdHi');
				foreach ($plxAdmin->aTags as $idart => $tag) {
					if (isset($plxAdmin->activeArts[$idart]) and $tag['tags'] !='' and $tag['date'] <= $now and $tag['active']) {
						if ($tags = array_map('trim', explode(',', $tag['tags']))) {
							foreach ($tags as $tag) {
								if (!empty($tag)) {
									if (!array_key_exists($tag, $this->tagListCheck)) {
										$this->tagListCheck[$tag] = array();
										$this->tagListCheck[$tag]['name']= $tag;
										$this->tagListCheck[$tag]['description']= '';
										$this->tagListCheck[$tag]['meta_description']= '';
										$this->tagListCheck[$tag]['meta_keywords']= $tag ;
										$this->tagListCheck[$tag]['thumbnail']= '';
										$this->tagListCheck[$tag]['thumbnail_title']= '';
										$this->tagListCheck[$tag]['thumbnail_alt']= '';
										$this->tagListCheck[$tag]['title_htmltag']= L_ARTICLE_TAGS_FIELD.' '.$tag;
										$this->tagListCheck[$tag]['url']= plxUtils::title2url($tag);
									} 
								}
							}
						}
					}
				}
			}
			ksort($this->tagListCheck);	
			$this->tagListCheck = $this->tagListCheck;
			
		}
		
		public function readTagList($filetag) {		
			
			# a t-on un fichier à lire ?
			if(!is_file($filetag)) return;
			
			# Mise en place du parseur XML
			$data = implode('',file($filetag));
			$parser = xml_parser_create(PLX_CHARSET);
			xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
			xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
			xml_parse_into_struct($parser,$data,$values,$iTags);
			xml_parser_free($parser);
			if(isset($iTags['tag']) AND isset($iTags['name'])) {		
				$nb = sizeof($iTags['name']);
				$size=ceil(sizeof($iTags['tag'])/$nb);
				for($i=0;$i<$nb;$i++) {
					$attributes = $values[$iTags['tag'][$i*$size]]['attributes'];
					$number = $attributes['number'];
					# Recuperation du nom du tag
					$this->tagList[$number]['number']=$number;
					# Recuperation du nom du tag
					// plxUtils::getValue($values[$iTags['meta_description'][$i]]['value']);
					$this->tagList[$number]['name']=plxUtils::getValue($values[$iTags['name'][$i]]['value']);
					# Recuperation du nom de la description
					$this->tagList[$number]['description']=plxUtils::getValue($values[$iTags['description'][$i]]['value']);
					# Recuperation de la balise title
					$this->tagList[$number]['title_htmltag']=plxUtils::getValue($values[$iTags['title_htmltag'][$i]]['value']);
					# Recuperation du meta description
					$this->tagList[$number]['meta_description']=plxUtils::getValue($values[$iTags['meta_description'][$i]]['value']);
					# Recuperation du meta keywords
					$this->tagList[$number]['meta_keywords']=plxUtils::getValue($values[$iTags['meta_keywords'][$i]]['value']);
					# Recuperation de l'url de la categorie
					$this->tagList[$number]['url']=strtolower($attributes['url']);
					# Récupération des informations de l'image représentant le tag
					$this->tagList[$number]['thumbnail']=plxUtils::getValue($values[$iTags['thumbnail'][$i]]['value']);
					$this->tagList[$number]['thumbnail_title']=plxUtils::getValue($values[$iTags['thumbnail_title'][$i]]['value']);
					$this->tagList[$number]['thumbnail_alt']=plxUtils::getValue($values[$iTags['thumbnail_alt'][$i]]['value']);				
				}				
				# Hook plugins
				// because similar categorie function got one about here
			}
		}		
	}			