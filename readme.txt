=== Easy-up ===
Author: Crabouille777  
Tags: upload, file, image, frontend, shortcode  
Requires at least: 6.0  
Tested up to: 6.8.1  
Requires PHP: 8.2  
Stable tag: 1.0  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html
==========================================================================
= * Note de l'auteur :                                                   =
=   Ce plugin est distribué gratuitement dans un esprit de partage.      =
=   Merci de ne pas le vendre ou monétiser sous une forme quelconque.    =
==========================================================================

Permet à vos utilisateurs d'envoyer un ou plusieurs fichiers (images, PDF) directement depuis l'interface publique de votre site via un shortcode.

== Description ==

Easy-up permet à un administrateur de créer facilement des champs personnalisés d'upload de fichiers, chacun avec :
- Un nom unique
- Une adresse e-mail destinataire
- Un nombre de fichiers maximum à uploader
- Une taille maximale autorisée par fichier
- Fichiers supportés : jpg,jpeg,png
- Option fichier pdf

Une fois le champ créé, un shortcode est généré automatiquement pour être inséré dans une page ou un article. L'utilisateur peut :
- Sélectionner un ou plusieurs fichiers
- Voir un aperçu (si image)
- Saisir son adresse e-mail
- Cliquer sur "Envoyer"

Les fichiers sont :
- Stockés dans un dossier sécurisé du plugin (`uploads/`)
- Transmis en pièce jointe à l'adresse administrateur configurée
- Accompagnés de l'adresse mail de l'utilisateur

Interface admin :
- Bouton "Ajouter un champ" pour afficher un formulaire de création
- Possibilité de modifier chaque paramètre via un menu déroulant
- Bouton supprimer un champ existant
- Bouton modifier pour modifier un shortcode
- Bouton supprimer pour supprimer un shortcode

== Installation ==

1. Uploadez le dossier `easy-up` dans le répertoire `/wp-content/plugins/`.
2. Activez le plugin dans le menu "Extensions" de WordPress.
3. Allez dans le menu "Easy-up" dans l'administration pour créer vos champs.

== Utilisation ==

1. Créez un champ via le bouton "Ajouter un champ".
2. Renseignez :
   - Nom du champ
   - Adresse e-mail administrateur
   - Taille max par fichier (facultatif)
   - Nombre max de fichiers (facultatif)
   - Option pdf oui/non
3. Cliquez sur OK pour générer le shortcode.
4. Copiez-collez le shortcode dans n’importe quelle page ou article WordPress.

== Sécurité ==

- Seuls les fichiers `.jpg`, `.jpeg`, `.png` et `.pdf` sont acceptés.
- Limite de taille configurée par l'admin.
- Uploads placés dans un dossier sécurisé inaccessible depuis l'extérieur.
- Vérification du nonce de sécurité et nettoyage des champs.

== Journal des modifications ==

= 1.0 =
* Version initiale
* Création de champs personnalisés
* Uploads sécurisés avec pièces jointes par mail
* Interface d’administration claire et légère

Auteur : Crabouille777
