# Proyecto Pokémon - App Local

Este proyecto proporciona una aplicación de ejemplo para gestionar información de Pokémon, utilizando una arquitectura con **Docker**, **PHP**, **Node.js** y **MySQL**. Sigue los pasos a continuación para clonar el repositorio, instalar las dependencias y ejecutar la aplicación localmente.

## Requisitos previos

1. **Docker Desktop**  
   Debes tener Docker instalado en tu máquina para poder ejecutar los contenedores.  
   [Descargar Docker Desktop](https://www.docker.com/get-started/)

2. **Docker Compose**  
   Docker Compose se usa para definir y ejecutar múltiples contenedores Docker. Asegúrate de tener Docker Compose instalado, aunque generalmente viene incluido con Docker Desktop.

3. **Git**  
   Debes tener **Git** instalado para clonar el repositorio.  
   [Descargar Git](https://git-scm.com/)

## Pasos para ejecutar la aplicación localmente

### 1. Clonar el repositorio

Abre tu terminal y clona este repositorio en tu máquina:

```
git clone https://github.com/AtsukaDeus/poke_app_prueba_tecnica.git
```

Luego accede a la carpeta que contiene el proyecto
```
cd poke_app_prueba_tecnica
```

### 2. Instalar las imágenes de Docker
Asegúrate de tener las imágenes necesarias para el backend, frontend y base de datos. Para ello, ejecuta los siguientes comandos:

```
docker pull php:8.2-apache
```
```
docker pull node:18
```
```
docker pull mysql:8.0
```

### 3. Levantar los contenedores
Para iniciar todos los servicios (frontend, backend y base de datos), ejecuta el siguiente comando en la raíz del proyecto (esto ejecutará docker-compose para levantar todos los contenedores):
** IMPORTANTE: (Docker Engine debe estar ejecutandose, es decir, se debe abrir docker desktop antes de ejecutar el siguiente comando)
```
docker-compose up --build
```
Este comando construirá las imágenes de Docker y levantará los contenedores. La base de datos se configurará automáticamente con las tablas definidas en el archivo init.sql que está incluido en el repositorio.


### 4. Acceder a la aplicación
El frontend estará disponible en http://localhost:3000.
El backend estará disponible en http://localhost:8000.
La base de datos MySQL estará disponible en localhost:3306 con las credenciales definidas en el archivo docker-compose.yml:

Usuario: admin
Contraseña: admin
Base de datos: poke_database

### 5. Detener los contenedores
Para detener los contenedores cuando hayas terminado, ejecuta el siguiente comando:
```
docker-compose down
```

Notas adicionales
PHP y Node.js están configurados para trabajar juntos. El backend usa PHP y el frontend usa Node.js.
MySQL se inicializa automáticamente con las tablas definidas en init.sql gracias al volumen montado en el contenedor de la base de datos.
