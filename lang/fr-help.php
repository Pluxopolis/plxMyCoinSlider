<?php if(!defined('PLX_ROOT')) exit; ?>
<pre style="font-size:12px">
Activation du plugin
- aller dans le menu Paramètres > Plugins
- cocher le plugin MyCoinSlider et dans le déroulant "Pour la sélection", sélectionner le menu "Activer"

Configuration du diaporama MyCoinSlider
- Aller dans le menu Paramètres > Plugins, et cliquer sur le lien Configuration du plugin MyCoinSlider

Pour ajouter des images au diaporama
- aller dans le gestionnaire des médias
- cocher les images à ajouter dans le diaporama
- dans le déroulant "Pour la sélection", sélectionner le menu MyCoinSlider > Ajouter au diaporama

Activation et personnalisation des images du diaporama
- allez dans le menu MyCoinSlider (dans le bandeau des menus de l'administration)
- renseigner les champs titre et description des images
- activer l'affichage en choisissant la valeur "Oui" dans la colonne "Active"
- cliquer sur le bouton "Modifier la liste des images" pour enregistrer les modifications

Affichage du diaporama sur la page d'accueil de son site
- éditer le fichier header.php de son theme
- ajouter la ligne suivante à l'endroit où vous souhaitez afficher le diaporama

<div style="color:#000;padding:0 10px 15px 10px;border:1px solid #dedede">
<?php echo plxUtils::strCheck('<?php eval($plxShow->callHook("MyCoinSlider")) ?>') ?>
</div>

Affichage du diaporama dans une page statique
- éditer le contenu d'une page statique et allant dans la gestion des pages statiques: menu "Pages tatiques" dans l'administration
- ajouter les lignes suivantes à l'endroit où vous souhaitez afficher le diaporama

<div style="color:#000;padding:0 10px 15px 10px;border:1px solid #dedede">
<?php 
echo plxUtils::strCheck('
<?php
global $plxShow;
eval($plxShow->callHook("MyCoinSlider")); 
?>
');
?>
</div>

<strong>Coin Slider</strong>: jQuery Image Slider Plugin with Unique Effects.
<a href="http://workshop.rs/projects/coin-slider/">http://workshop.rs/projects/coin-slider/</a>
</pre>