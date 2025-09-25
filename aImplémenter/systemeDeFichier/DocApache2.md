# Installation d'Apache2 et PHP sur Linux

Ce guide explique comment installer, vérifier et administrer Apache2 et SSH, puis configurer un dossier pour les fichiers uploadés par une application web (PHP).

---

```bash
sudo apt update
sudo apt install apache2 php libapache2-mod-php
```

Astuce: si vous utilisez PHP-FPM au lieu du module Apache, remplacez libapache2-mod-php par php-fpm et activez la passerelle Apache ↔ PHP-FPM via proxy_fcgi.
## Vérification du statut du service Apache2

```bash
sudo systemctl status apache2
```
## Démarrer/arrêter/redémarrer Apache2

```bash
sudo systemctl start apache2   # Démarrer le service
sudo systemctl stop apache2    # Arrêter le service
sudo systemctl restart apache2 # Redémarrer le service
```

---

## Connexion SSH

### Installer le serveur SSH

```bash
sudo apt update
sudo apt install openssh-server
```
### Vérification du statut du service SSH

```bash
sudo systemctl status ssh
```
### Démarrer/arrêter/redémarrer SSH

```bash
sudo systemctl start ssh   # Démarrer le service
sudo systemctl stop ssh    # Arrêter le service
sudo systemctl restart ssh # Redémarrer le service
```

### Connexion distante via SSH

```bash
ssh username@server_ip_address
```
Remplacez `username` par votre nom d'utilisateur sur le serveur et `server_ip_address` par l'adresse IP du serveur.

Note: Connexion initiale avec un utilisateur non-root, puis élévation en root si nécessaire (sudo).


# Configuration du serveur web

## Créer le dossier pour les fichiers uploadés
Créer un dossier dédié accessible par Apache. Exemple sous Debian/Ubuntu (utilisateur www-data):

```bash
sudo mkdir -p /var/www/uploads
sudo chown -R www-data:www-data /var/www/uploads
sudo chmod -R 775 /var/www/uploads
```

Remplacez `www-data` par `apache` sur CentOS/RHEL/AlmaLinux. Évitez `chmod 777` (risque sécurité).

Conseils sécurité:
- Si SELinux est activé (CentOS/RHEL):
	```bash
	sudo chcon -R -t httpd_sys_rw_content_t /var/www/uploads
	```
- Si AppArmor restreint PHP/Apache, autorisez le chemin si nécessaire.

## Redémarrer Apache pour prendre en compte les changements
```bash
sudo systemctl restart apache2
```
## Déployer l'application web

Placez vos fichiers HTML/PHP dans `/var/www/html/` (VirtualHost par défaut) ou configurez un VirtualHost dédié:

```apache
<VirtualHost *:80>
	ServerName exemple.local
	DocumentRoot /var/www/html
	<Directory /var/www/html>
		Options Indexes FollowSymLinks
		AllowOverride All
		Require all granted
	</Directory>
</VirtualHost>
```

Exemples de chemins utiles:
- Racine web par défaut: `/var/www/html/`
- Dossier d'uploads (ex.): `/var/www/uploads/`
- Logs Apache: `/var/log/apache2/` (Debian/Ubuntu) ou `/var/log/httpd/` (CentOS/RHEL)

Vérification rapide de PHP avec Apache:
```bash
echo "<?php phpinfo();" | sudo tee /var/www/html/phpinfo.php > /dev/null
```
Puis ouvrez http://<ip_serveur>/phpinfo.php et supprimez le fichier ensuite:
```bash
sudo rm /var/www/html/phpinfo.php
```

---

### Dépannage rapide
- Service ne démarre pas: `sudo journalctl -u apache2 -e`
- Erreur permissions upload: vérifier propriétaire/droits sur `/var/www/uploads` et SELinux/AppArmor
- Taille upload: dans php.ini, ajuster `upload_max_filesize` et `post_max_size` (et côté Nginx `client_max_body_size` si applicable)


