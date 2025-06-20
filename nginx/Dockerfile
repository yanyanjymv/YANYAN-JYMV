# Gunakan base image Nginx dari Alpine
FROM nginx:alpine

# Salin file konfigurasi Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Expose port untuk Nginx
EXPOSE 80

# Jalankan Nginx di foreground
CMD ["nginx", "-g", "daemon off;"]
