# Tahap 1: build aplikasi Vue dengan Node.js
FROM node:20-alpine3.20 AS build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
# Alamat API relatif: browser memanggil /api di alamat frontend, lalu Nginx meneruskannya ke backend
ARG VITE_API_BASE_URL=/api
ENV VITE_API_BASE_URL=$VITE_API_BASE_URL
RUN npm run build

# Tahap 2: sajikan hasil build dengan Nginx (tanpa node_modules)
FROM nginx:1.27-alpine
# Nama service backend di jaringan Docker Compose (bukan alamat IP)
ENV BACKEND_HOST=backend \
    BACKEND_PORT=8000
COPY nginx/default.conf.template /etc/nginx/templates/default.conf.template
COPY --from=build /app/dist /usr/share/nginx/html
EXPOSE 80
