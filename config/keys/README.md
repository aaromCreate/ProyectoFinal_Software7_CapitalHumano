# Llaves OpenSSL

Genere las llaves antes de guardar colaboradores o perfiles laborales:

```bash
openssl genrsa -out private.pem 2048
openssl rsa -in private.pem -pubout -out public.pem
```

La llave privada esta excluida del repositorio por `.gitignore`.
