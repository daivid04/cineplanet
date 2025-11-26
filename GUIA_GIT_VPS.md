# Guía de Despliegue: Cineplanet (Modo Multi-Proyecto)

Esta guía está diseñada para que puedas tener **Cineplanet**, **TallerDiego** y otros proyectos en el mismo VPS sin que se mezclen las llaves de seguridad.

## Parte 1: Preparar tu Código (Local)

*(Si ya hiciste esto, puedes saltarlo)*

1.  **Subir cambios a GitHub:**
    ```bash
    git add .
    git commit -m "Preparando despliegue"
    git push origin feature/script
    ```

## Parte 2: Configurar Llaves en el VPS

Conéctate a tu VPS (`ssh soporte@...`) y sigue estos pasos:

### 1. Crear la Llave (Si no la has creado aún)
```bash
ssh-keygen -t ed25519 -C "vps-cineplanet" -f ~/.ssh/clave_deploy_cineplanet
```
*(Presiona Enter si te pide passphrase para dejarla vacía)*

### 2. Configurar el "Apodo" (Alias)
Esto es el truco para que funcionen varios proyectos. Crearemos un archivo de configuración.

1.  Abre (o crea) el archivo config:
    ```bash
    nano ~/.ssh/config
    ```

2.  **Pega este contenido exacto al final del archivo:**

    ```ssh
    # --- Configuración Cineplanet ---
    Host github-cineplanet
        HostName github.com
        User git
        IdentityFile ~/.ssh/clave_deploy_cineplanet
        IdentitiesOnly yes
    
    # --- Configuración TallerDiego (Para el futuro) ---
    # Host github-tallerdiego
    #     HostName github.com
    #     User git
    #     IdentityFile ~/.ssh/clave_deploy_tallerdiego
    #     IdentitiesOnly yes
    ```

3.  Guarda: Presiona `Ctrl+O`, `Enter`, y luego `Ctrl+X`.

### 3. Copiar la Llave a GitHub
Muestra tu llave pública:
```bash
cat ~/.ssh/clave_deploy_cineplanet.pub
```
Copia el texto y agrégalo en GitHub > Repo Cineplanet > Settings > Deploy Keys.

## Parte 3: Descargar el Proyecto

Ahora usaremos el "apodo" `github-cineplanet` en lugar de `github.com`.

### 1. Preparar la carpeta
```bash
# Darte permisos a ti mismo para no usar sudo siempre
sudo chown -R $USER:www-data /var/www
chmod -R 755 /var/www

cd /var/www
```

### 2. Clonar usando el Alias
¡OJO AQUÍ! Fíjate que dice `github-cineplanet`:

```bash
git clone git@github-cineplanet:daivid04/cineplanet.git
```

*Si te pregunta "Are you sure...", escribe `yes`.*

## Parte 4: Configurar Nginx

1.  Crear configuración:
    ```bash
    sudo nano /etc/nginx/sites-available/cineplanet
    ```

2.  Contenido (ajusta tu dominio/IP):
    ```nginx
    server {
        listen 80;
        server_name TU_IP_AQUI; # Ej: 142.93.10.10

        root /var/www/cineplanet;
        index index.html;

        location / {
            try_files $uri $uri/ =404;
        }
    }
    ```

3.  Activar y Reiniciar:
    ```bash
    sudo ln -s /etc/nginx/sites-available/cineplanet /etc/nginx/sites-enabled/
    sudo nginx -t
    sudo systemctl restart nginx
    ```

## Parte 5: Actualizar en el Futuro

Cuando quieras actualizar, entra a la carpeta y haz pull. Git recordará el alias automáticamente.

```bash
cd /var/www/cineplanet
git pull origin feature/script
```
