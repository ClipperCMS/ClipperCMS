<?php
/**
* assets/snippets/eform/german.inc.php
* German language file for eForm
*/
$_lang["ef_date_format"] = "%d.%m.%Y %H:%M:%S";
$_lang["ef_debug_info"] = "Debuginformationen: ";
$_lang['ef_debug_warning'] = '<p style="color:red;"><span style="font-size:1.5em;font-weight:bold;">WARNUNG - DEBUGGING IST EINGESCHALTET</span> <br />Stellen Sie sicher, dass es ausgeschaltet wird, bevor die Seite live geht!</p>';
$_lang["ef_error_filter_rule"] = "Text-Filter nicht erkannt";
$_lang["ef_error_formid"] = "Ungültige(r) Formular-ID oder -Name";
$_lang["ef_error_list_rule"] = "Fehler bei der Validierung des Formularfeldes! #LIST-Regel deklariert, aber keine Listen-Werte gefunden:";
$_lang["ef_error_validation_rule"] = "Validierungsregel wurde nicht erkannt";
$_lang['ef_eval_deprecated'] = "Die #EVAL Regel ist veraltet und wird in zukünftigen Versionen entfernt. Verwenden Sie stattdessen #FUNCTION.";
$_lang["ef_failed_default"] = "Falscher Wert";
$_lang["ef_failed_ereg"] = "Wert konnte nicht validiert werden";
$_lang["ef_failed_eval"] = "Wert konnte nicht validiert werden";
$_lang["ef_failed_list"] = "Wert nicht in der Liste der erlaubten Werte";
$_lang["ef_failed_range"] = "Wert nicht innerhalb des erlaubten Bereichs";
$_lang["ef_failed_upload"] = "Falscher Dateityp.";
$_lang["ef_failed_vericode"] = "Ungültiger Bestätigungs-Code.";
$_lang["ef_invalid_date"] = " ist kein gültiges Datum";
$_lang["ef_invalid_email"] = " ist keine gültige E-Mail Adresse";
$_lang["ef_invalid_number"] = " ist keine gültige Zahl";
$_lang["ef_is_own_id"] = "<span class=\"ef-form-error\"> Formulartemplate wurde auf ID des Dokuments, welches den Snippet-Aufruf enthält, gesetzt. Sie können das Formular nicht im selben Dokument wie den Snippet-Aufruf haben.</span> id=";
$_lang['ef_mail_abuse_error'] = '<strong>Ungültige oder unsichere Einträge wurden in Ihrem Formular entdeckt</strong>.';
$_lang['ef_mail_abuse_message'] = '<p>Ein Formular auf Ihrer Webseite wurde möglicherweise Ziel einer eMail-Injection Versuch. Die Details über die gesendeten Daten finden Sie darunter. Verdächtige Werte wurden in \[..]\ Tags eingebettet.</p>';
$_lang['ef_mail_abuse_subject'] = 'Möglicher E-Mail-Formular Missbrauch entdeckt für Formular ID';
$_lang["ef_mail_error"] = "Mailer konnte keine E-Mails versenden";
$_lang['ef_multiple_submit'] = "<p>Dieses Formular wurde bereits erfolgreich übermittelt. Es besteht kein Grund, die Daten mehrmals zu senden.</p>";
$_lang["ef_no_doc"] = "Dokument oder Chunk wurde nicht gefunden für Template id=";
$_lang['ef_regex_error'] = 'Fehler im regulären Ausdruck ';
$_lang["ef_required_message"] = " Folgende benötigte Felder wurden nicht ausgefüllt: {fields}<br />";
$_lang["ef_rule_failed"] = "<span style=\"color:red;\">Fehler</span> bei der Regel [+rule+] (input=\"[+input+]\")";
$_lang["ef_rule_passed"] = "Erfolgreich angewendete Regel [+rule+] (input=\"[+input+]\").";
$_lang["ef_sql_no_result"] = " wurde validiert. <span style=\"color:red;\"> SQL lieferte kein Ergebnis!</span> ";
$_lang['ef_submit_time_limit'] = "<p>Dieses Formular wurde bereits erfolgreich übermittelt. Wiederholte Übertragung wurde für ".($submitLimit/60)." Minuten deaktiviert.</p>";
$_lang["ef_tamper_attempt"] = "Missbrauchsversuch entdeckt!";
$_lang["ef_thankyou_message"] = "<h3>Danke!</h3><p>Ihre Informationen wurden erfolgreich übertragen.</p>";
$_lang["ef_thousands_separator"] = ""; //leave empty to use (php) locale, only needed if you want to overide locale setting!
$_lang["ef_upload_error"] = ": Fehler beim Hochladen der Datei.";
$_lang["ef_upload_exceeded"] = " hat die Upload-Grenze überschritten";
$_lang["ef_validation_message"] = "<div class=\"errors\"><strong>Es wurden Fehler im Formular gefunden:</strong><br />[+ef_wrapper+]</div>"; //changed
$_lang['ef_version_error'] = "<strong>WARNUNG!</strong> Die Version des eForm Snippets (version:&nbsp;$version) unterscheidet sich von der eingefügten eForm Datei (version:&nbsp;$fileVersion). Bitte stellen Sie sicher, dass Sie für beide die selbe Version verwenden.";
?>
?>