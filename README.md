# SAE_3.01

## Contributeur

SlicedPotatoes  -> Masmejean Kevin
Steenkiste      -> van Steenkiste Dimitri
Pileaucou59     -> Pileaucou Louis
Casi223         -> Godisiabois Isaac
LPtornad0       -> Helin Esteban
Valyn5          -> Dascotte Yann

## Déploiement de Liquibase

### Etape 1 : Le répertoire

Dans le terminal, merci de se mettre dans le répertoire `./SQL` du projet avant faire la ligne de commande suivante :
`liquibase init project`. Cela va créer un répertoire `liquibase` dans le répertoire `./SQL` du projet.

### Etape 2 : Le fichier de configuration

Dans le répertoire `./SQL/liquibase`, supprimer le fichier `example-changelog.sql`.  
Puis dans le fichier `liquibase.properties`, modifier les lignes suivantes :

```
changeLogFile= ...
liquibase.command.url= ...
liquibase.command.username= ...
liquibase.command.password= ...
```

en

```
changeLogFile= CodeDB.sql
liquibase.command.url=jdbc:postgresql://tommytech.net:5432/postgres
liquibase.command.username=[votre nom d'utilisateur]```
liquibase.command.password=[votre mot de passe]
```

Si vous n'utilisez pas de BDD locale, vous pouvez commenter les lignes suivantes :

```
# Enter URL for the source database
#liquibase.command.referenceUrl: jdbc:h2:tcp://localhost:9090/mem:integration

# Enter the username for your source database
#liquibase.command.referenceUsername: dbuser

# Enter the password for your source database
#liquibase.command.referencePassword: letmein
```

## Project Structure
├── README.md (this file) <br />
├── SQL <br />
└── src <br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├── Model <br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└── Mail <br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├── Presentation <br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└── View <br />
└── lib <br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└── PHPMailer-master <br />
