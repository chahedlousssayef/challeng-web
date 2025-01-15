# challenge-web
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
