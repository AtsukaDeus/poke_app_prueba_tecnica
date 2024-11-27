FROM node:18

# Crear y movernos al directorio de trabajo
WORKDIR /app

# Copiar los archivos necesarios
COPY package*.json ./
RUN npm install

# Copiar el resto del código
COPY . .

# Exponer el puerto 3000 y arrancar la app
EXPOSE 3000
CMD ["npm", "start"]
