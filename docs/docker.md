# Generate image from Dockerfile
# ===========================

# Build image from Dockerfile
```bash
docker build -t angelcakes .
```
# Stop apache
```bash
sudo service apache2 stop
```
# Run container
```bash
docker run --rm --network="host" --env-file ./config/.env -d angelcake-valdaso:latest
docker run --rm -p 80:80 -p 8080:8080 --env-file ./config/.env -d angelcake-valdaso:latest
docker run --rm --network="host" -d angelcakes:latest
```
# Stop UFW
```bash
sudo ufw disable
```
# clear docker
```bash
docker system prune -a
```