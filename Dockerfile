# Gunakan base image Nginx dari Alpine
FROM nginx:alpine

# Salin file konfigurasi Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Salin source code aplikasi PHP ke direktori yang sesuai di Nginx
COPY html/ /usr/share/nginx/html/

# Expose port untuk Nginx dan PHP-FPM
EXPOSE 80 9000 

# Jalankan Nginx di foreground
CMD ["nginx", "-g", "daemon off;"]
