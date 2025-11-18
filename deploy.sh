#!/bin/bash

# Executar git pull via SSH
sudo git pull origin main  # Substitua 'main' pelo branch desejado

# Limpar cache do Laravel
sudo php artisan optimize:clear

# Construir assets com npm
sudo npm run build

echo "Processo concluído!"
