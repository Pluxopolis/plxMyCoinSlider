<?php if(!defined('PLX_ROOT')) exit; ?>
<?php

# Control du token du formulaire
plxToken::validateFormToken($_POST);

# On édite les catégories
if(!empty($_POST)) {
	$plxPlugin->coinslider->editSlides($_POST);
	header('Location: plugin.php?p=plxMyCoinSlider');
	exit;
}
?>

<form action="plugin.php?p=plxMyCoinSlider" method="post" id="form_plxMyCoinSlider">
<table class="table">
	<thead>
		<tr>
			<th class="checkbox"><input type="checkbox" onclick="checkAll(this.form, 'idSlide[]')" /></th>
			<th class="title"><?php $plxPlugin->lang('L_PICTURE') ?></th>
			<th style="width:80%"><?php $plxPlugin->lang('L_INFORMATION') ?></th>
			<th style="width:8%"><?php $plxPlugin->lang('L_ACTIVE') ?></th>
			<th style="width:80%"><?php $plxPlugin->lang('L_ORDER') ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	# Initialisation de l'ordre
	$num = 0;
	# Si on a des infos
	if($plxPlugin->coinslider->aSlides) {
		foreach($plxPlugin->coinslider->aSlides as $k=>$v) { # Pour chaque catégorie
			$ordre = ++$num;
			echo '<tr class="line-'.($num%2).'">';
			echo '<td style="vertical-align:top"><input type="checkbox" name="idSlide[]" value="'.$k.'" /><input type="hidden" name="slideNum[]" value="'.$k.'" /></td>';
			echo '<td style="vertical-align:top">';
			if(file_exists(PLX_ROOT.$v['url'])) {
				echo '<img src="'.PLX_PLUGINS.'plxMyCoinSlider/lib/timthumb.php?src='.PLX_ROOT.plxUtils::strCheck($v['url']).'&amp;h=100&amp;w=150&amp;zc=1" alt="" />';
			}
			echo '&nbsp;</td><td>';
			echo $plxPlugin->getLang('L_URL_IMAGE').' '.$k.' :<br />';
			plxUtils::printInput($k.'_url', plxUtils::strCheck($v['url']), 'text', '60-255');
			echo '<br />'.$plxPlugin->getLang('L_TITLE_IMAGE').' :<br />';
			plxUtils::printInput($k.'_title', plxUtils::strCheck($v['title']), 'text', '60-255');
			echo '<br />'.$plxPlugin->getLang('L_ONCLICK_IMAGE').' :<br />';
			plxUtils::printInput($k.'_onclick', plxUtils::strCheck($v['onclick']), 'text', '60-255');
			echo '<br />'.$plxPlugin->getLang('L_DESCRIPTION_IMAGE').' :<br />';
			plxUtils::printArea($k.'_description',plxUtils::strCheck($v['description']),60,3);
			echo '</td><td style="vertical-align:top">';
			plxUtils::printSelect($k.'_active', array('1'=>L_YES,'0'=>L_NO), $v['active']);
			echo '</td><td style="vertical-align:top">';
			plxUtils::printInput($k.'_ordre', $ordre, 'text', '3-3');
			echo '</td>';
			echo '</tr>';
		}
		# On récupère le dernier identifiant
		$a = array_keys($plxPlugin->coinslider->aSlides);
		rsort($a);
	}
	?>
	</tbody>
</table>
<p class="in-action-bar" class="center">
	<?php echo plxToken::getTokenPostMethod() ?>
	<?php plxUtils::printSelect('selection', array( '' => L_FOR_SELECTION, 'delete' => $plxPlugin->getLang('L_DELETE')), '') ?>
	<input class="button submit" type="submit" name="submit" value="<?php echo L_OK ?>" />&nbsp;&nbsp;&nbsp;
	<input class="button update " type="submit" name="update" value="<?php $plxPlugin->lang('L_UPDATE') ?>" />
</p>
</form>