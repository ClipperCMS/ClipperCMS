<?php
/**
 * Resource Manager Module - francais.inc.php
 * 
 * Purpose: Contains the language strings for use in the module.
 * Author: Garry Nutting   Traduction : David Molli�re
 * For: MODx CMS (www.modxcms.com)
 * Date:29/09/2006 Version: 1.6
 * 
 */
 
//-- ENGLISH LANGUAGE FILE
 
//-- titles
$_lang['RM_module_title'] = 'ResManager';
$_lang['RM_action_title'] = 'Selectionnez une op�ration';
$_lang['RM_range_title'] = 'Sp�cifiez une plage d\'ID';
$_lang['RM_tree_title'] = 'Selectionnez les documents dans l\'arbre';
$_lang['RM_update_title'] = 'Mise � jour effectu�e';
$_lang['RM_sort_title'] = 'Editeur d\'index de Menu';

//-- tabs
$_lang['RM_doc_permissions'] = 'Permissions des documents';
$_lang['RM_template_variables'] = 'Variables de mod�le';
$_lang['RM_sort_menu'] = 'Trier les items de menu';
$_lang['RM_change_template'] = 'Modifier le mod�le';
$_lang['RM_publish'] = 'Publier/D�publier';
$_lang['RM_other'] = 'Autres propri�t�s';
 
//-- buttons
$_lang['RM_close'] = 'Fermer ResManager';
$_lang['RM_cancel'] = 'Retour';
$_lang['RM_go'] = 'Ex�cuter';
$_lang['RM_save'] = 'Sauvegarder';
$_lang['RM_sort_another'] = 'Trier un autre';

//-- templates tab
$_lang['RM_tpl_desc'] = 'Choisissez le mod�le � partir de la liste ci-dessous et sp�cifiez les ID de documents qui doivent �tre modifi�s. Vous pouvez sp�cifier soit une plage d\'ID, soit en utilisant l\'arbre des documents.';
$_lang['RM_tpl_no_templates'] = 'Mod�le introuvable';
$_lang['RM_tpl_column_id'] = 'ID';
$_lang['RM_tpl_column_name'] = 'Nom';
$_lang['RM_tpl_column_description'] ='Description';
$_lang['RM_tpl_blank_template'] = 'Mod�le vide (_blank)';

$_lang['RM_tpl_results_message']= 'Utilisez le bouton "Retour" si vous souhaitez faire d\'autres modifications. Le cache du site a �t� automatiquement vid�.';

//-- template variables tab
$_lang['RM_tv_desc'] = 'Pr�cisez l\'ID du(des) document(s) qui doit(doivent) �tre modifi�(s), soit en sp�cifiant une plage d\'ID ou via l\'arbre des document, puis choisissez le mod�le dans la liste (les variables de mod�le associ�es seront charg�es). Saisissez les variables de mod�les souhait�es puis validez.';
$_lang['RM_tv_template_mismatch'] = 'Ce document n\'utilise pas le mod�le s�lectionn�.';
$_lang['RM_tv_doc_not_found'] = 'Ce document n\'est pas dans la base de donn�es.';
$_lang['RM_tv_no_tv'] = 'Pas de variable de mod�le pour ce mod�le.';
$_lang['RM_tv_no_docs'] = 'Aucun document s�lectionn� pour la mise � jour.';
$_lang['RM_tv_no_template_selected'] = 'Pas de mod�le s�lectionn�.';
$_lang['RM_tv_loading'] = 'Variables de mod�le en cours de chargement...';
$_lang['RM_tv_ignore_tv'] = 'Ignorer ces variables de mod�le (liste s�par�e par des virgules):';
$_lang['RM_tv_ajax_insertbutton'] = 'Ins�rer';

//-- document permissions tab
$_lang['RM_doc_desc'] = 'Choisir le groupe de document � partir de la liste ci-dessous et si celuci doit �tre ajout� ou supprimer du groupe. Ensuite, pr�cisez l\'ID des documents qui doivent �tre modifi�es. Vous pouvez sp�cifier soit une plage d\'ID, soit en utilisant l\'arbre des documents.';
$_lang['RM_doc_no_docs'] = 'Ce groupe de document n\'existe pas.';
$_lang['RM_doc_column_id'] = 'ID';
$_lang['RM_doc_column_name'] = 'Nom';
$_lang['RM_doc_radio_add'] = 'Ajouter un groupe de documents';
$_lang['RM_doc_radio_remove'] = 'Supprimer un groupe de documents';

$_lang['RM_doc_skip_message1'] = 'Le document dont l\'ID est';
$_lang['RM_doc_skip_message2'] = 'fait d�j� partie du groupe de document s�lectionn� (non pris en compte)';

//-- sort menu tab
$_lang['RM_sort_pick_item'] = 'Merci de cliquer sur l\'item de l\'arborescence du document que vous souhaitez trier.'; 
$_lang['RM_sort_updating'] = 'Mise � jour ...';
$_lang['RM_sort_updated'] = 'Mis � jour.';
$_lang['RM_sort_nochildren'] = 'Ce parent n\'a aucun enfant';
$_lang['RM_sort_noid']='Aucun document selectionn�. Merci de revenir en arri�re et de s�lectionner un document.';

//-- other tab
$_lang['RM_other_header'] = 'R�glages divers de document';
$_lang['RM_misc_label'] = 'R�glages disponibles:';
$_lang['RM_misc_desc'] = 'Merci de choisir un item du menu d�roulant ainsi que l\'option requise. Un seul item peut �tre modifi� � la fois.';

$_lang['RM_other_dropdown_publish'] = 'Publier/D�publier';
$_lang['RM_other_dropdown_show'] = 'Montrer/Masquer dans le menu';
$_lang['RM_other_dropdown_search'] = 'Recherchable/Non recherchable';
$_lang['RM_other_dropdown_cache'] = 'A mettre en cache/A ne pas mettre en cache';
$_lang['RM_other_dropdown_richtext'] = 'Editeur/Sans Editeur';
$_lang['RM_other_dropdown_delete'] = 'Effacer/Restaurer';

//-- radio button text
$_lang['RM_other_publish_radio1'] = 'Publier'; 
$_lang['RM_other_publish_radio2'] = 'D�publier';
$_lang['RM_other_show_radio1'] = 'Masquer dans le menu'; 
$_lang['RM_other_show_radio2'] = 'Afficher dans le menu';
$_lang['RM_other_search_radio1'] = 'Recherchable'; 
$_lang['RM_other_search_radio2'] = 'Non recherchable';
$_lang['RM_other_cache_radio1'] = 'A mettre en cache'; 
$_lang['RM_other_cache_radio2'] = 'A ne pas mettre en cache';
$_lang['RM_other_richtext_radio1'] = 'Editeur WYSIWYG'; 
$_lang['RM_other_richtext_radio2'] = 'Pas d\'�diteur WYSIWYG';
$_lang['RM_other_delete_radio1'] = 'Effacer'; 
$_lang['RM_other_delete_radio2'] = 'Restaurer';

//-- adjust dates 
$_lang['RM_adjust_dates_header'] = 'D�finir les dates des documents';
$_lang['RM_adjust_dates_desc'] = 'N\'importe lequel des option de date peuvent �tre modifi�s. Utiliser "Voir le calendrier" pour d�finir les dates.';
$_lang['RM_view_calendar'] = 'Voir le calendrier';
$_lang['RM_clear_date'] = 'Remettre les dates � z�ro';

//-- adjust authors
$_lang['RM_adjust_authors_header'] = 'Red�finir les auteurs';
$_lang['RM_adjust_authors_desc'] = 'Utiliser la liste d�roulante pour d�finir le nouvel auteur du document.';
$_lang['RM_adjust_authors_createdby'] = 'Cr�� par:';
$_lang['RM_adjust_authors_editedby'] = 'Edit� par:';
$_lang['RM_adjust_authors_noselection'] = 'Aucune modification';

 //-- labels
$_lang['RM_date_pubdate'] = 'Date de publication:';
$_lang['RM_date_unpubdate'] = 'Date de d�publication:';
$_lang['RM_date_createdon'] = 'Date de cr�ation:';
$_lang['RM_date_editedon'] = 'Date de modification:';
//$_lang['RM_date_deletedon'] = 'Deleted On Date';

$_lang['RM_date_notset'] = ' (ind�fini)';
//deprecated
$_lang['RM_date_dateselect_label'] = 'S�lectionner une date: ';

//-- document select section
$_lang['RM_select_submit'] = 'Envoi';
$_lang['RM_select_range'] = 'Revenir � la d�finition de la plage de document';
$_lang['RM_select_range_text'] = '<p><strong>Cl� (ou n est une ID de document):</strong><br /><br />
							  n* - Modifier le r�glage pour ce document et ses enfants imm�diats<br /> 
							  n** - Modifier le r�glage pour ce document et tous ses enfants<br /> 
							  n-n2 - Modifier le r�glage pour cette plage de documents<br /> 
							  n - Modifier le r�glage pour un document</p> 
							  <p>Exemple: 1*,4**,2-20,25 - Cela modifiera le r�glage s�lectionn� pour le document 1 et ses enfants, le document 4 et tous ses enfants, et les documents 2 � 20, ainsi que le document 25</p>';
$_lang['RM_select_tree'] ='Afficher et s�lectionner les documents en utilisant l\'Arbre des documents';

//-- process tree/range messages
$_lang['RM_process_noselection'] = 'Aucune s�lection effectu�e. ';
$_lang['RM_process_novalues'] = 'Aucune valeur d�finie.';
$_lang['RM_process_limits_error'] = 'Limite sup�rieure plus petite que la limite inf�rieure:';
$_lang['RM_process_invalid_error'] = 'Valeur incorrecte:';
$_lang['RM_process_update_success'] = 'La mise � jour s\'est correctement d�roul�e, sans erreurs.';
$_lang['RM_process_update_error'] = 'La mise � jour a �t� effectu�e mais a g�n�r� des erreurs:';
$_lang['RM_process_back'] = 'Retour';

//-- manager access logging
$_lang['RM_log_template'] = 'Resource Manager: Mod�le(s) modifi�(s).';
$_lang['RM_log_templatevariables'] = 'Resource Manager: Variable(s) de mod�le modifi�e(s).';
$_lang['RM_log_docpermissions'] ='Resource Manager: Permission(s) du(des) document(s) modidifi�e(s).';
$_lang['RM_log_sortmenu']='Resource Manager: Modification de l\'index de menu effectu�e.';
$_lang['RM_log_publish']='Resource Manager: R�glages de publication/d�publication modifi�s.';
$_lang['RM_log_hidemenu']='Resource Manager: Option(s) de masquage/affichage du(des) document(s) dans le menu modifi�e(s).';
$_lang['RM_log_search']='Resource Manager:Option(s) de recherche du(des) document(s) dans le menu modifi�e(s).';
$_lang['RM_log_cache']='Resource Manager: Option(s) de cache du(des) document(s) dans le menu modifi�e(s)..';
$_lang['RM_log_richtext']='Resource Manager: Option(s) d\'�dition du(des) document(s) dans le menu modifi�e(s)..';
$_lang['RM_log_delete']='Resource Manager: Option(s) d\'effacement/de restauration du(des) document(s) dans le menu modifi�e(s).';
$_lang['RM_log_dates']='Resource Manager: Date(s) de cr�ation/�dition du(des) document(s) modifi�e(s).';
$_lang['RM_log_authors']='Resource Manager: Auteur du(des) document(s) modifi�(s).';

?>
