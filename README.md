# BankShop

Une application web pour gérer votre compte bancaire en ligne !
((
Faites composer install quand vous récuperer les fichiers pour créer ceux qui manque

ajouter ce code dans le fichier config/route.yaml

home:
    path: /
    controller: App\Controller\HomeController::index.


Le fichier devrait ressembler à ça :

controllers:
    resource:
        path: ../src/Controller/
        namespace: App\Controller
    type: attribute

home:
    path: /
    controller: App\Controller\HomeController::index

))


L'objectif de BankShop est de permettre aux client de consulter leur compte, de faire des retraits,
des dépots d'argent mais aussi de faire des virements dans un environnement intuitif et sécurisé .
Chaque personne peut ouvrir jusqu'a 5 comptes maximum (épargne ou courant) toute en bénéficiant d'une 
sécurité maximale garantissant le bien-être des clients

Nos Principales fonctionnalités sont :

Consultation des dépots et retraits de son compte
Consultation des users et transactions pour les admin
Inscription et connexion sécurisée


Backend : Symfony (php)
Base de données : 
Frontend : twig
