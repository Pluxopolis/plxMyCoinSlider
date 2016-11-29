<?php

class coinslider {

	public $config = null; # fichier des données
	public $aSlides = array(); # tableau des slides

	public function __construct() {

		$lang = (isset($_COOKIE["plxMyMultiLingue"]) and !empty($_COOKIE["plxMyMultiLingue"])) ? '.'.$_COOKIE["plxMyMultiLingue"] : '';
		if(defined('PLX_CONF')) # version PluXml < 5.1.7
			$this->config = dirname(PLX_CONF).'/coinslider.config'.$lang.'.xml';
		else # version PluXml >= 5.1.7
			$this->config = PLX_ROOT.PLX_CONFIG_PATH.'/plugins/coinslider.config'.$lang.'.xml';	
	}

	/**
	 * Méthode qui parse le fichier des slides et alimente le tableau aSlides
	 *
	 * @param	filename	emplacement du fichier XML des slides
	 * @return	null
	 * @author	Stéphane F
	 **/
	public function getSlides() {

		if(!is_file($this->config)) return;

		# Mise en place du parseur XML
		$data = implode('',file($this->config));
		$parser = xml_parser_create(PLX_CHARSET);
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$values,$iTags);
		xml_parser_free($parser);
		if(isset($iTags['slide']) AND isset($iTags['title'])) {
			$nb = sizeof($iTags['title']);
			$size=ceil(sizeof($iTags['slide'])/$nb);
			for($i=0;$i<$nb;$i++) {
				$attributes = $values[$iTags['slide'][$i*$size]]['attributes'];
				$number = $attributes['number'];
				# Recuperation du titre
				$this->aSlides[$number]['title']=plxUtils::getValue($values[$iTags['title'][$i]]['value']);
				# Onclick
				$this->aSlides[$number]['onclick']=plxUtils::getValue($values[$iTags['onclick'][$i]]['value']);
				# Recuperation de la description
				$this->aSlides[$number]['description']=plxUtils::getValue($values[$iTags['description'][$i]]['value']);
				# Recuperation de lien de l'image
				$this->aSlides[$number]['url']=plxUtils::getValue($values[$iTags['url'][$i]]['value']);
				# Récuperation état activation de la catégorie dans le menu
				$this->aSlides[$number]['active']=isset($attributes['active'])?$attributes['active']:'1';
			}
		}
	}

	/**
	 *  Méthode qui retourne le prochain id d'un slide
	 *
	 * @return	string		id d'un nouveau slide sous la forme 001
	 * @author	Stephane F.
	 **/
	 public function nextIdSlide() {
		if(is_array($this->aSlides)) {
			$idx = key(array_slice($this->aSlides, -1, 1, true));
			return str_pad($idx+1,3, '0', STR_PAD_LEFT);
		} else {
			return '001';
		}
	}

	/**
	 * Méthode qui édite le fichier XML des slides selon le tableau $content
	 *
	 * @param	content	tableau multidimensionnel des catégories
	 * @param	action	permet de forcer la mise àjour du fichier
	 * @return	string
	 * @author	Stephane F
	 **/
	public function editSlides($content, $action=false) {

		$save = $this->aSlides;

		# suppression
		if(isset($content['selection']) AND $content['selection']=='delete' AND isset($content['idSlide'])) {
			foreach($content['idSlide'] as $slide_id) {
				# suppression du parametre
				unset($this->aSlides[$slide_id]);
				$action = true;
			}
		}
		# ajout d'un nouveau slide à partir du gestionnaire de médias
		if(isset($content['selection']) AND !empty($content['selection']) AND isset($content['idFile'])) {
			$plxAdmin = plxAdmin::getInstance();
			$root = $plxAdmin->aConf['medias'];
			if($content['folder']=='.') $content['folder']='';
			foreach($content['idFile'] as $filename) {
				$slide_id = $this->nextIdSlide();
				$this->aSlides[$slide_id]['url'] = $root.$content['folder'].$filename;
				$this->aSlides[$slide_id]['title'] = $filename;
				$this->aSlides[$slide_id]['ordre'] = intval($slide_id);
				$this->aSlides[$slide_id]['active'] = 0;
				$this->aSlides[$slide_id]['onclick'] = "";
				$action = true;
			}
		}
		# mise à jour de la liste
		elseif(!empty($content['update'])) {
			foreach($content['slideNum'] as $slide_id) {
				if($content[$slide_id.'_url']!='') {
					$this->aSlides[$slide_id]['url'] = trim($content[$slide_id.'_url']);
					$this->aSlides[$slide_id]['title'] = trim($content[$slide_id.'_title']);
					$this->aSlides[$slide_id]['description'] = trim($content[$slide_id.'_description']);
					$this->aSlides[$slide_id]['ordre'] = intval($content[$slide_id.'_ordre']);
					$this->aSlides[$slide_id]['active'] = intval($content[$slide_id.'_active']);
					$this->aSlides[$slide_id]['onclick'] = trim($content[$slide_id.'_onclick']);
					$action = true;
				}
			}
			# On va trier les clés selon l'ordre choisi
			if(sizeof($this->aSlides)>0) uasort($this->aSlides, create_function('$a, $b', 'return $a["ordre"]>$b["ordre"];'));
		}
		# sauvegarde
		if($action) {
			# On génére le fichier XML
			$xml = "<?xml version=\"1.0\" encoding=\"".PLX_CHARSET."\"?>\n";
			$xml .= "<document>\n";
			foreach($this->aSlides as $slide_id => $slide) {
				$xml .= "\t<slide number=\"".$slide_id."\" active=\"".$slide['active']."\">\n";
				$xml .= "\t\t<url><![CDATA[".plxUtils::cdataCheck($slide['url'])."]]></url>\n";
				$xml .= "\t\t<title><![CDATA[".plxUtils::cdataCheck($slide['title'])."]]></title>\n";
				$xml .= "\t\t<onclick><![CDATA[".plxUtils::cdataCheck($slide['onclick'])."]]></onclick>\n";
				$xml .= "\t\t<description><![CDATA[".plxUtils::cdataCheck($slide['description'])."]]></description>\n";
				$xml .= "\t</slide>\n";
			}
			$xml .= "</document>";
			# On écrit le fichier
			if(plxUtils::write($xml,$this->config))
				return plxMsg::Info(L_SAVE_SUCCESSFUL);
			else {
				$this->aSlides = $save;
				return plxMsg::Error(L_SAVE_ERR.' '.$this->config);
			}
		}
	}


}
?>