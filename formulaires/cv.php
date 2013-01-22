<?php

/* on va chercher l'url de la page */
$monUrl = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 

/*on récupère le contenu de la balise <title> lié à cette url

Avant cela on crée l'affichage du titre de l'annonce en <title> de la page par l'ajout de 
themes/intranet/head/page_voir_annonce.html qui contient:
<BOUCLE_annonce_head(ARTICLES) {id_article}>
<title>[(#TITRE|textebrut)]</title>
</BOUCLE_annonce_head>
*/
function RecupererTitre($Site)
{
	$Titre = 'Pas de titre';

	$Fichier = file_get_contents($Site);
	
	if (eregi("<title>(.*)</title>", $Fichier, $Sortie)) $Titre = $Sortie[1];
	
	return $Titre;
}


/* Exemple: */
/* echo RecupererTitre($monUrl); */



/* on stock le titre de la page dans une nouvelle variable */
$bip = RecupererTitre($monUrl);

/* echo $bip; */








$GLOBALS['formats'] = array(
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'txt' => 'text/plain',
    'odt' => 'application/msword',  
    'log' => 'text/plain'   
);

function formulaires_cv_charger_dist($id_article =null ){
    $valeurs = array('email'=>'','texte'=>'','nom'=>'','prenom'=>'','sujet'=>'','adresse'=>'','cp'=>'','ville'=>'','tel'=>'' );
    return $valeurs;
}

function formulaires_cv_verifier_dist($id_article = null){
    $formats = $GLOBALS['formats'];
    $erreurs = array();
    
    include_spip('inc/filtres');

    if (!$nom=_request('nom'))
        $erreurs['nom'] = _T("info_obligatoire");

    if (!$prenom=_request('prenom'))
        $erreurs['prenom'] = _T("info_obligatoire");
    
    if (!$email_from = _request('email'))
        $erreurs['email'] = _T("form_prop_indiquer_email");
    elseif(!email_valide($email_from))
        $erreurs['email'] = _T('form_prop_indiquer_email');

    //Connaitre les piéces jointes
    $_FILES ? $_FILES : $GLOBALS['HTTP_POST_FILES'];
    
    
    //Est ce qu'il y a une pièce jointe ? CV
    if($_FILES['fichier']['error'] == UPLOAD_ERR_NO_FILE)
        $erreurs['fichier'] .= _T("info_obligatoire");

    //Test de la taille
    if($_FILES['fichier']['error'] == UPLOAD_ERR_FORM_SIZE || $_FILES['fichier']['size'] > 2097152)
        $erreurs['fichier'] .= "La taille de votre CV exc&egrave;de la taille autoris&eacute;e";

    //Test des extensions
    $fichier = pathinfo($_FILES['fichier']['name']);
    if (!in_array($fichier['extension'],array_keys($formats))) 
        $erreurs['fichier'] .= "Le fichier de votre CV n'a pas un format accept&eacute;";
        
    //Est ce qu'il y a une pièce jointe ? Lettre
    if($_FILES['lettre']['error'] == UPLOAD_ERR_NO_FILE)
        $erreurs['lettre'] .= _T("info_obligatoire");

    //Test de la taille
    if($_FILES['lettre']['error'] == UPLOAD_ERR_FORM_SIZE || $_FILES['lettre']['size'] > 2097152)
        $erreurs['lettre'] .= "La taille de votre CV exc&egrave;de la taille autoris&eacute;e";

    //Test des extensions
    $lettre = pathinfo($_FILES['lettre']['name']);
    if (!in_array($lettre['extension'],array_keys($formats))) 
        $erreurs['lettre'] .= "Le fichier de votre lettre de motivation n'a pas un format accept&eacute;";



    /*
if (!$sujet=_request('sujet'))
        $erreurs['sujet'] = _T("info_obligatoire");
*/
    
    if (count($erreurs))
        $erreurs['message_erreur'] = 'Ce formulaire contient des erreurs... les champs pr&eacute;c&eacute;d&eacute;s d\' un * sont obligatoires.';
    return $erreurs;
}

function formulaires_cv_traiter_dist($id_article = null){
    $formats = $GLOBALS['formats'];

    $envoyer_mail = charger_fonction('envoyer_mail','inc');
    $email_to = 'intranet@secours-catholique.org';
    $email_from = _request('email');
    $sujet = _request('sujet');
    $texte = _request('texte');
    $nom= _request('nom');
    $prenom= _request('prenom');
    $soc= _request('societe');
    $adresse= _request('adresse');
    $cp= _request('cp');
    $ville= _request('ville');
    $adres= _request('email');
    $tel= _request('tel');
    $sujet= 'Candidature';


    if ($id_article) {
        $article = "en r&eacute;f&eacute;rence &agrave; l'article : " . generer_url_public("article","id_article=".$id_article) . "\n";
        $res =sql_select(
            'email',
            array('spip_auteurs, spip_auteurs_articles'),
            array(
                'spip_auteurs_articles.id_article = '.$id_article,
                'spip_auteurs_articles.id_auteur = spip_auteurs.id_auteur'
            )
        );
        $auteurs = sql_fetch_all($res);
    }
    

    
    
   
    
    //Déclarer un mail en partie multiple
    $boundary .= "piecejointe";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n";
    
    //Le corps de mail
    $message_mail ="--".$boundary."\n";
    $message_mail .="Content-Type: text/plain; charset=utf-8\r\n\n";
    $message_mail .= " \r\n";
    $message_mail .= "offre d'emploi interne : \r\n";
    $message_mail .= " \r\n";
    $message_mail .= " \r\n";
    $message_mail .= $bip;
    $message_mail .= " \r\n";
    $message_mail .= " \r\n";
    $message_mail .= "Un cv a &eacute;t&eacute; post&eacute; depuis le site par ".$prenom." ".$nom." \n";
    $message_mail .= $article;
    $message_mail .= " \r\n";
    $message_mail .= " \r\n";
    $message_mail .= "E-mail: ".$email_from."\n";
    $message_mail .= "Adresse: ".$adresse." ".$cp." ".$ville." \n";
    $message_mail .= "T&eacute;l&eacute;phone: ".$tel." \n";
    $message_mail .= "Sujet: ".$sujet." \n";
    $message_mail .= "Message « ".$texte." »\n\n";

    //Connaitre les piéces jointes
    $_FILES ? $_FILES : $GLOBALS['HTTP_POST_FILES'];

    //Connaitre l'extension
    $fichier = pathinfo($_FILES['fichier']['name']);
    $extension = $fichier['extension'];
    
    $lettre = pathinfo($_FILES['lettre']['name']);
    $extension = $lettre['extension'];


    //Formater le fichier pour le mail
    $fichier=file_get_contents($_FILES['fichier']['tmp_name']);
    
    $lettre=file_get_contents($_FILES['lettre']['tmp_name']);
    /* On utilise aussi chunk_split() qui organisera comme il faut l'encodage fait en base 64 pour se conformer aux standards */
    $fichier=chunk_split( base64_encode($fichier) );
    
    $message_mail.= "--".$boundary."\n";
    $message_mail.= "Content-Type: ".$formats[$extension]."; name=\"".$_FILES['fichier']['name']."\"\n";
    $message_mail.= "Content-Transfer-Encoding: base64\n";
    $message_mail.= "Content-Disposition: attachment; filename=\"".$_FILES['fichier']['name']."\"\r\n\n";
    $message_mail.= $fichier;
    
    
    
    $fichier=chunk_split( base64_encode($lettre) );
    
    $message_mail.= "--".$boundary."\n";
    $message_mail.= "Content-Type: ".$formats[$extension]."; name=\"".$_FILES['lettre']['name']."\"\n";
    $message_mail.= "Content-Transfer-Encoding: base64\n";
    $message_mail.= "Content-Disposition: attachment; filename=\"".$_FILES['lettre']['name']."\"\r\n\n";
    $message_mail.= $lettre;


    //Cloture du mail
    $message_mail.= "--".$boundary."--";

    $envoyer_mail($email_to,$sujet,$message_mail,$email_from,$headers);
    foreach($auteurs as $row) {
        if (!empty($row['email']))
            $envoyer_mail($row['email'],$sujet,$message_mail,$email_from,$headers);
    }
    return array('message_ok'=>'Merci pour votre candidature, elle a bien été prise en compte. Vous allez recevoir un mail de confirmation de dépôt de votre candidature. Nous vous contacterons prochainement.');}
    
    
    
     
// Consruction du message de confirmation d'envoi 
$confirm_message .= "Bonjour ".$_POST['prenom']." ".$_POST['nom'].", \r\n";
$confirm_message .= " \r\n";
$confirm_message .= "En référence à l'offre d'emploi interne : \r\n";
$confirm_message .= $bip;
$confirm_message .= " \r\n";
$confirm_message .= " \r\n";
$confirm_message .= "Nous avons bien reçu votre candidature et nous vous en remercions. \r\n";
$confirm_message .= "Nous allons étudier votre dossier et un responsable ressources humaines prendra contact avec vous dans les plus proches délais. \r\n";
$confirm_message .= $article;
$confirm_message .= " \r\n";
$confirm_message .= "Cordialement, \r\n";
$confirm_message .= " \r\n";
$confirm_message .= "L'équipe Pôle développement RH \r\n";
$confirm_message .= " \r\n";
$confirm_message .= "Vos coordonnées : \r\n"; 
$confirm_message .= "Nom : ".$_POST['nom']."\r\n"; 
$confirm_message .= "Prénom : ".$_POST['prenom']."\r\n"; 
$confirm_message .= "Adresse : ".$_POST['adresse']."\r\n"; 
$confirm_message .= "Code postal : ".$_POST['cp']."\r\n"; 
$confirm_message .= "Ville : ".$_POST['ville']."\r\n"; 
$confirm_message .= "Téléphone : ".$_POST['tel']."\r\n";

// Destinataire 
$confirm_to = _request('email');


// Expéditeur
/* $email_from = 'herve.ledantec@secours-catholique.org';  */
// Sujet 
$confirm_subject = "CONFIRMATION DE DEPOT DE VOTRE CANDIDATURE "; 
// Envoi du mail 
mail($confirm_to, $confirm_subject,$confirm_message);  

/* header ("Location: contact_ok.html"); */ // page à afficher quand le message est envoyé 
    
    
    
    
?>
