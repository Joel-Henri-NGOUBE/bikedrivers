- ### Dans le but de réaliser le déploiement de mon application, j'ai eu à créé et ajouter dans le dossier .github/workflows les fichiers de workflow nécessaires pour avoir de l'automatisation au niveau de mon pipeline CI/CD

- ### L'exécution du déploiement de l'application commence à partir du moment où un push est effectué sur la branche `main` du dépôt comme je l'ai configuré dans le fichier `deploy.yaml`

    ![alt text](/DeploymentDoc/DeployMdImages/image.png)

- ### Dès lors que GitHub s'aperçoit qu'un nouveau commit est disponible (sur la branche main), il initie donc l'exécution du déploiement en réalisant les jobs correspondant au fichier `deploy.yaml`

- ### L'exécution du déploiement dans l'application est impossible si au préalable tous les autres jobs n'ont pas réussi à s'exécuter comme le défini le la configuration suivante:

    ![alt text](/DeploymentDoc/DeployMdImages/image2.png)

- ### Ainsi, le workflow audit se réalisera en premier dans la chaîne d'exécution du workflow de déploiement, ensuite viendra le workflow quality pour la qualité de code qui ne s'exécutera que si le workflow d'audit a réussi. De la même façon, le workflow de test ne s'exécutera que si le workflow de qualité a réussi au préalable. Ainsi, le workflow de déploiement ne pourra s'exécuter si tous les autres workflow n'ont pas réussi. Il est en mesure d'initier les autres workflows grâce à une configuration précisée à l'intérieur de chaque autre workflow

    ![alt text](/DeploymentDoc/DeployMdImages/image3.png)

#### On peut remarquer que ces autres workflows ne s'exécute que sur toute autre branche distincte de la branche `main`

- ### Lorsque tous ces workflow se seront exécutés, le job de déploiement peux dès lors entamer son exécution en commençant récupérer et mettre à l'intérieur du runner les fichiers et les dossiers présents à l'intérieur dans le dépôt

![alt text](/DeploymentDoc/DeployMdImages/image4.png)

- ### Ensuite, il y a une connexion qui se fait sur Docker avec mes identifiants grâce à une Action de GitHub

    ![alt text](/DeploymentDoc/DeployMdImages/image5.png)

- ### Puis il y a création d'une image sur la base de mon Dockerfile et un push de cette image sur Docker.

    ![alt text](/DeploymentDoc/DeployMdImages/image6.png)

- ### Enfin, il y a exécution d'une requête en GET sur le serveur où mon application est hébergée pour initier la récupération de l'image Docker ainsi crée et le déploiement de l'API de BikeDrivers sur ce serveur

    ![alt text](/DeploymentDoc/DeployMdImages/image7.png)

- ### Une logique analogue se produit pour le déploiement du frontend

    ![alt text](/DeploymentDoc/DeployMdImages/image8.png)

- ### En ce qui concerne la base de données, elle a été directement déployée sur le serveur et les modalités de connexion ont été injectées dans l'environnement de l'API:

##### Variables d'environnement du conteneur MySQL

![alt text](/DeploymentDoc/DeployMdImages/image9.png)

#### Variables d'environnement du conteneur du backend

![alt text](/DeploymentDoc/DeployMdImages/image10.png)

![alt text](/DeploymentDoc/DeployMdImages/image11.png)