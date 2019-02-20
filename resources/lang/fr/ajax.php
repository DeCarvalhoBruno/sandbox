<?php return [
   'title' => [
      'login' => 'Log In',
   ],
   'breadcrumb' => [
      'admin-blog-index' => 'Blog',
      'admin-blog-add' => 'Ajouter article',
      'admin-blog-edit' => 'Éditer',
      'admin-blog-category' => '__Categories',
      'admin-dashboard' => 'Tableau de bord',
      'admin-users-index' => 'Utilisateurs',
      'admin-groups-index' => 'Groupes',
      'admin-users-edit' => 'Éditer',
      'admin-groups-edit' => 'Éditer',
      'admin-groups-add' => 'Ajouter',
      'admin-groups-members' => 'Membres',
      'admin-blog_posts-index' => '__Blog Posts',
      'admin-blog_posts-add' => '__Create',
      'admin-blog_posts-edit' => '__Edit',
      'admin-blog_posts-category' => '__Categories',
      'admin-media-edit' => '__Edit Image',
      'admin-settings-password' => 'Mot de passe',
      'admin-settings-profile' => 'Profil',
      'admin-settings-general' => 'Général',
   ],
   'sidebar' => [
      'main_nav' => 'NAVIGATION',
      'dashboard' => 'Tableau de bord',
      'users' => 'Utilisateurs',
      'groups' => 'Groupes',
      'blog' => 'Blog',
      'list' => 'Liste',
      'add' => 'Ajouter',
      'category' => '__Categories',
   ],
   'general' => [
      'collapse all' => '__Collapse all',
      'ok' => 'OK',
      'cancel' => 'Annuler',
      'name' => 'Nom',
      'home' => 'Accueil',
      'settings' => 'Paramètres',
      'profile' => 'Profil',
      'general' => 'Général',
      'save' => 'Enregistrer',
      'save_changes' => 'Enregistrer',
      'update' => 'Mettre à jour',
      'create' => 'Créer',
      'logout' => 'Déconnexion',
      'login' => 'Connexion',
      'register' => 'Ajouter un compte',
      'success' => 'Succès!',
      'password' => 'Mot de passe',
      'actions' => 'Actions',
      'email' => 'E-mail',
      'back' => 'Retour',
      'next' => '__Next',
      'prev' => '__Previous',
      'first' => '__First',
      'last' => '__Last',
      'permission' => 'Permission|Permissions',
      'toggle' => 'Activer/Désactiver',
      'select_all' => 'Sélectionner tout',
      'apply' => 'Appliquer',
      'search' => 'Recherche',
      'reset_filters' => 'Annuler les filtres',
      'delete' => 'Supprimer',
      'reload' => 'Rafraîchir',
      'status' => 'Status',
      'expand_all' => '__Expand all',
      'collapse_all' => '__Collapse all',
      'uploaded_on' => '__Uploaded on',
      'media' => '__Media',
      'crop' => '__Crop',
      'close' => '__Close',
   ],
   'db' => [
      'user' => 'Utilisateur|Utilisateurs',
      'group' => 'Groupe|Groupes',
      'blog_post' => 'Article|Articles',
      'users' => '__User|Users',
      'groups' => '__Group|Groups',
      'blog_posts' => '__Blog Post|Blog Posts',
      'username' => 'Nom d\'utilisateur',
      'first_name' => 'Prénom',
      'last_name' => 'Nom de famille',
      'full_name' => 'Nom complet',
      'new_email' => 'Nouvel e-mail',
      'new_username' => 'Nouveau nom d\'utilisateur',
      'user_created_at' => 'Date d\'inscription',
      'group_name' => 'Nom du groupe',
      'new_group_name' => 'Nouveau nom du groupe',
      'group_mask' => 'Masque de groupe',
      'member_count' => 'Nombre de membres',
      'blog_post_title' => 'Titre de l\'article',
      'media_title' => '__Title',
      'media_alt' => '__Alt text',
      'media_description' => '__Description',
      'media_caption' => '__Caption',
   ],
   'db_raw' => [
      'full_name' => 'nom',
      'username' => 'nom_utilisateur',
      'email' => 'mail',
      'group_name' => 'groupe',
      'created_at' => 'inscription',
      'blog_post_title' => 'titre',
   ],
   'db_raw_inv' => [
      'nom' => 'full_name',
      'nom_utilisateur' => 'username',
      'mail' => 'email',
      'groupe' => 'group_name',
      'inscription' => 'created_at',
      'titre' => 'blog_post_title',
      'full_name' => '__full_name',
      'username' => '__username',
      'email' => '__email',
      'group_name' => '__group_name',
      'created_at' => '__created_at',
      'blog_post_title' => '__blog_post_title',
   ],
   'filters' => [
      'blog_title' => 'titre',
      'sortBy' => 'tri',
      'order' => 'ordre',
      'users_name' => 'nom',
      'users_group' => 'groupe',
      'users_created' => 'inscription',
      'blog_posts_title' => '__title',
      'asc' => 'asc',
      'desc' => 'desc',
      'day' => 'jour',
      'week' => 'semaine',
      'month' => 'mois',
      'year' => 'annee',
   ],
   'filter_labels' => [
      'users_groupe' => 'Groupe :',
      'users_nom' => 'Nom :',
      'blog_titre' => 'Titre de l\'article :',
      'users_inscription' => 'Date inscription, période :',
      'blog_title' => '__Post title:',
      'users_group' => '__Group:',
      'users_name' => '__Full name:',
      'blog_posts_title' => '__Post title:',
      'users_created' => '__Registration period:',
      'created_today' => 'Inscrit aujourd\'hui',
      'created_week' => 'Moins d\'une semaine',
      'created_month' => 'Moins d\'un mois',
      'created_year' => 'Moins d\'un an',
   ],
   'filters_inv' => [
      'registration' => 'createdAt',
      'blog_post_title' => '__title',
      'group' => 'groupe',
      'name' => 'nom',
      'sortBy' => 'tri',
      'title' => '__title',
      'order' => 'ordre',
      'fullName' => 'nom',
      'createdAt' => 'inscription',
   ],
   'constants' => [
      'BLOG_STATUS_DRAFT' => 'Brouillon',
      'BLOG_STATUS_REVIEW' => 'Relecture',
      'BLOG_STATUS_PUBLISHED' => 'Publié',
   ],
   'form' => [
      'description' => [
         'username' => 'Le nom simplifié de l\'utilisateur.',
         'first_name' => 'Le prénom de l\'utilisateur.',
         'last_name' => 'Le nom de famille de l\'utilisateur.',
         'new_email' => '"{0}" est l\'adresse e-mail actuelle.',
         'new_username' => '"{0}" est le nom d\'utilisateur actuel.',
         'group_name' => 'Le nom du groupe ne peut contenir que des caractères alphanumériques ou de soulignement.',
         'new_group_name' => '"{0}" est le nom du groupe actuel. Le nom du groupe ne peut contenir que des caractères alphanumériques ou de soulignement.',
         'group_mask' => 'Détermine la position du groupe dans sa hiérarchie. Le plus bas le masque, le plus élevé le statut du groupe.',
         'media_title' => '__Title of the media',
         'media_alt' => '__A text alternative to the image for screen readers or when the image does not load.',
         'media_description' => '__For internal purposes, to help with tracking in searches.',
         'media_caption' => '__May be displayed below the image for commentary/description purposes.',
      ],
   ],
   'modal' => [
      'error' => [
         'h' => 'Oups !',
         't' => 'Une erreur s\'est produite. Merci d\'essayer de nouveau.',
      ],
      'token_expired' => [
         'h' => 'La session a expiré.',
         't' => 'Merci de vous connecter de nouveau.',
      ],
      'unauthorized' => [
         'h' => 'Accès interdit.',
         't' => 'Vous n\'êtes pas autorisé á voir cette page.',
      ],
      'user_delete' => [
         'h' => 'Confirmer la suppression d\'utilisateur.',
         't' => 'Voulez vous vraiment supprimer l\'utilisateur {name}',
      ],
      'group_delete' => [
         'h' => '__Confirm group deletion',
         't' => '__Do you really want to delete group {name}?',
      ],
      'blog_post_delete' => [
         'h' => '__Confirm blog deletion',
         't' => '__Do you really want to delete blog post {name}?|Do you really want to delete those {number} blog posts?',
      ],
   ],
   'error' => [
      'page_not_found' => 'Page non trouvée',
      'passwords_dont_match' => 'Les mots de passe ne correspondent pas.',
      'form' => 'Ce formulaire comporte des erreurs.',
   ],
   'message' => [
      'profile_updated' => 'Votre profil a été mis á jour.',
      'password_updated' => 'Votre mot de passe a été mis á jour.',
      'user_update_ok' => 'L\'utilisateur a été mis á jour. La mise á jour des permissions peut prendre quelques secondes.',
      'user_delete_ok' => 'L\'utilisateur {name} a été supprimé.|Les utilisateurs ont été supprimés.',
      'group_update_ok' => 'Le groupe a été mis á jour. La mise á jour des permissions peut prendre quelques secondes.',
      'group_delete_ok' => 'Le groupe a été supprimé.',
      'blog_post_delete_ok' => '__Blog post {name} was deleted.',
      'group_create_ok' => 'Le groupe a été créé.',
      'media_update_ok' => '__The media was updated.',
   ],
   'pages' => [
      'media' => [
         'cropper_resize_image' => '__Resize image',
         'cropper_zoom' => '__Use mouse wheel to zoom in/out',
         'cropper_preview' => '__Preview result',
         'cropper_crop_upload' => '__Crop & Upload',
      ],
      'auth' => [
         'remember_me' => 'Se souvenir de moi.',
         'forgot_password' => 'Vous avez oublié votre mot de passe ?',
         'send_password_reset_link' => 'Envoyer le lien de réinitialisation du mot de passe',
         'confirm_password' => 'Confirmer le mot de passe',
         'reset_password' => 'Réinitialiser le mot de passe',
         'new_password' => 'Nouveau mot de passe',
      ],
      'members' => [
         'member_search' => 'Saisir le nom complet, ex: \'Paul Dubois\'',
         'group_name' => 'Groupe :',
         'edit_preview' => 'Aperçu',
         'no_changes' => 'Pas de changements pour le moment.',
         'add_members' => 'Ajouter des membres',
         'remove_members' => 'Supprimer des membres',
         'user_add_tag' => 'Les membres suivants seront ajoutés :',
         'user_no_add' => 'Aucun membre ajouté.',
         'user_remove_tag' => 'Les utilisateurs suivants seront supprimés :',
         'user_no_remove' => 'Aucun membre supprimé.',
         'user_none' => 'Ce groupe n\'a pas de membres..',
         'current_members' => 'Les utilisateurs suivants sont membres de ce groupe :',
      ],
      'users' => [
         'warning1' => 'Tout changement de permissions individuelles pour cet utilisateur va prendre la priorité sur les permissions attribuées aux groupes dont l\'utilisateur est le membre.',
         'warning2' => 'Nous recommandons d\'attribuer les permissions aux groupes d\'abord et de n\'utiliser les permissions individuelles que pour gérer des cas particuliers.',
         'filter_full_name' => 'Filtrer par nom',
         'filter_group' => 'Filtrer par groupe',
         'filter_created_at' => 'Filtrer par date d\'inscription',
      ],
      'groups' => [
         'info1' => 'Les permissions pour tous les membres du groupe sont définies ici.',
         'info2' => 'Il est également possible d\'attribuer des permissions individuelles aux utilisateurs, au quel cas ces dernières prendront la priorité',
      ],
      'settings' => [
         'language' => 'Langue',
         'avatar-tab' => 'Avatar',
         'avatar-ul-tab' => 'Ajouter l\'avatar',
         'delete_avatar' => 'Suprrimer l\'avatar',
         'click_default' => '__Click on an avatar to make it the default.',
         'image_uploading' => '__Processing in progress...',
         'image_proceed' => '__Proceed to cropping',
         'image_uploaded' => '__The avatar has been processed, you can return to the avatar tab.',
      ],
      'blog' => [
         'categories' => '__Categories',
         'media' => '__Media',
         'tab_available' => '__Available',
         'tab_upload' => '__Upload',
         'author' => 'Auteur',
         'filter_title' => 'Filtrer par titre',
         'filter_name' => '__Filter by name',
         'delete_image' => '__Delete avatar',
         'click_featured' => '__Click on an image to make it the featured image for this post.',
         'image_uploaded' => '__Upload is complete.',
         'add_post' => '__Add post',
         'add_root_button' => '__Add root category',
         'add_tag_pholder' => '__Type enter to add tag, click to remove',
         'blog_post_excerpt' => '__Excerpt',
         'excerpt_label' => '__This user-defined summary of the post can be displayed on the front page.',
         'add_success' => '__The blog post was created.',
         'save_success' => '__The blog post was updated.',
         'edit_image' => '__Edit image',
         'published_at' => '__Publishing date:',
      ],
      'blog_categories' => [
         'add_child_node' => '__Add a child element to "{name}"',
         'edit_node' => '__Edit node "{name}"',
         'delete_node' => '__Delete node "{name}"',
      ],
   ],
   'tables' => [
      'sort_ascending' => 'Trier par ordre croissant',
      'sort_descending' => 'Trier par ordre décroissant',
      'empty' => 'Aucune donnée disponible.',
      'sort_asc' => '__Sort in ascending order',
      'sort_desc' => '__Sort in descending order',
      'select_item' => 'Sélectionner {name} pour une action groupée',
      'edit_item' => 'Éditer {name}',
      'delete_item' => 'Supprimer {name}',
      'grouped_actions' => 'Action groupées',
      'option_del_user' => 'Supprimer l\'utilisateur',
      'option_del_blog' => 'Supprimer l\'article',
      'btn_apply_title' => 'Appliquer l\'action à tous les utilisateurs sélectionnés',
   ],
   'dropzone' => [
      'choose_file' => 'Déposez votre fichier ici (ou cliquez pour choisir)',
      'max_size' => 'Taille maximale :',
      'accepted_formats' => 'Formats acceptés :',
      'file_too_big' => 'Ce fichier est trop volumineux ({{filesize}} Mo, maximum autorisé : {{maxFilesize}} Mo).',
      'invalid_type' => 'Ce type de fichier n\'est pas autorisé.',
      'response_error' => 'Le serveur a répondu avec un code {{statusCode}}.',
      'cancel_upload' => 'Annuler l\'envoi',
      'cancel_confirm' => 'Confirmez-vous l\'interruption de cet envoi ?',
      'remove_file' => '',
      'max_files_exceeded' => 'Le nombre maximal de fichiers envoyables en une fois est atteint.',
      'delete_media' => 'Supprimer le média',
      'edit_media' => 'Editer le média',
   ],
   'media' => [
      'cropper_resize_image' => '__Use handles to resize image',
      'cropper_preview' => '__Cropped preview',
      'cropper_crop_upload' => '__Crop & Upload',
      'image_url_copy' => '__Copy image url to clipboard',
   ],
   'locales' => [
      'en' => 'Anglais',
      'fr' => 'Français',
   ],
   'units' => [
      'MB' => 'Mo',
   ],
   'go_home' => 'Retour à l\'accueil',
   'toggle_navigation' => 'Réduire/Agrandir le menu',

];