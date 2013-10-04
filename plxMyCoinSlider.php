<?php
/**
 * Plugin plxMyCoinSlider
 * @author	Stephane F
 **/

include(dirname(__FILE__).'/lib/class.plx.coinslider.php');

class plxMyCoinSlider extends plxPlugin {

	public $coinslider = null; # objet coinslider

	public function __construct($default_lang) {

		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accèder à la page config.php et admin.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);
		$this->setAdminProfil(PROFIL_ADMIN);

		$this->addHook('AdminMediasTop', 'AdminMediasTop');
		$this->addHook('AdminMediasPrepend', 'AdminMediasPrepend');

		$this->coinslider = new coinslider();
		$this->coinslider->getSlides();

		# déclaration des hooks
		if($this->coinslider->aSlides) {
			$this->addHook('ThemeEndHead', 'ThemeEndHead');
			$this->addHook('ThemeEndBody', 'ThemeEndBody');
			$this->addHook('MyCoinSlider', 'MyCoinSlider');
		}

	}

	public function AdminMediasTop() {

		echo '<?php
		$arr = array("MyCoinSlider" => array("coinslider_add" => "Ajouter au diaporama"));
		$selectionList = array_merge($selectionList, $arr);
		?>';

	}

	public function AdminMediasPrepend() {

		if(isset($_POST['selection']) AND ($_POST['selection'][0] == 'coinslider_add' OR $_POST['selection'][1] == 'coinslider_add') AND isset($_POST['idFile'])) {
			$this->coinslider->editSlides($_POST);
			header('Location: medias.php');
			exit;
		}

	}

	public function MyCoinSlider() {

		if($this->coinslider->aSlides) {
			echo "\n<div id=\"coin-slider\">\n";
			foreach($this->coinslider->aSlides as $slide) {
				if($slide['active']) {
					$onclick = $slide['onclick']!='' ? $slide['onclick'] : $slide['url'];
					echo '<a href="'.plxUtils::strCheck($onclick).'"><img alt="" src="'.plxUtils::strCheck($slide['url']).'" title="'.plxUtils::strCheck($slide['title']).'" /><span>'.strip_tags($slide['description'], '<strong><b><em><br>')."</span></a>\n";
				}
			}
			echo "</div>\n";
		}
	}

	public function ThemeEndHead() {
		echo '<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'plxMyCoinSlider/coin-slider/coin-slider-styles.css" media="screen" />';
		echo '
<style type="text/css">
.coin-slider { width: '.$this->getParam('width').'px !important }
</style>'."\n";
	}

	public function ThemeEndBody() {

		$keys = array('width','height','spw','sph','delay','sDelay','opacity','titleSpeed','effect','navigation','links','hoverPause');
		$parms = $this->getParams();
		$array= array();
		foreach($parms as $key => $value) {
			if(in_array($key, $keys) AND ($value['value']!='' OR $value['value']==1)) {
				if(in_array($key, array('navigation','links','hoverPause')))
					$array[] = $key.':'.($value['value']==1?'true':'false');
				elseif($value['type']=='numeric')
					$array[] = $key.':'.$value['value'];
				else
					$array[] = $key.":'".$value['value']."'";
			}
		}
		$string = $array ? implode(',',$array) : '';

		if($this->getParam('jquery')) {
			echo "\n".'
<script type="text/javascript">
if (typeof jQuery == "undefined") {
	document.write(\'<script type="text\/javascript" src="'.PLX_PLUGINS.'plxMyCoinSlider\/coin-slider\/jquery.min.js"><\/script>\');
}
</script>';
		}
			echo '
<script type="text/javascript" src="'.PLX_PLUGINS.'plxMyCoinSlider/coin-slider/coin-slider.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#coin-slider").coinslider({'.$string.'});
});
</script>
';
	}
}
?>