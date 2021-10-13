# FSG-TV
Web-basiertes System zur Anzeige von Infos und Plakaten am Fernseher am Schülercafé inklusive Admin-Panel.

## Requirements
- Apache Webserver
- PHP 7 oder so
- Fernseher (haupt-Screen) und Monitor (Admin-Screen)
- Tastatur + Maus
- Raspberry Pi 4 (mit 2 HDMI Ausgängen)

Für mehr Infos zur Raspberry Pi Installation und Setup: https://github.com/AnTheMaker/raspi-kiosk

- Klone dieses Git repo in den Apache Webroot folder (`cd /var/www/ && git clone XX html`)
- Cronjob einrichten (mit `sudo crontab -e`), der alle 2 STunden schaut, ob dieses Git Repo geupdated wurde, und ggf. die lokale Version automatisch updated. (`* */2 * * * cd /var/www/html && git pull`)
