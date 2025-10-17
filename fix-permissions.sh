#!/bin/bash

# Script para corrigir permissões dos arquivos do backend

echo "🔧 Corrigindo permissões dos arquivos..."

# Obter UID e GID do usuário atual
USER_ID=$(id -u)
GROUP_ID=$(id -g)

echo "📋 Seu UID: $USER_ID, GID: $GROUP_ID"

# Parar containers se estiverem rodando
echo "⏹️ Parando containers..."
docker compose down

# Reconstruir o container backend com as permissões corretas
echo "🔨 Reconstruindo container backend..."
USER_ID=$USER_ID GROUP_ID=$GROUP_ID docker compose build --no-cache backend

# Iniciar containers
echo "▶️ Iniciando containers..."
docker compose up -d

# Aguardar containers iniciarem
sleep 5

# Corrigir permissões finais
echo "🔐 Aplicando permissões finais..."
docker compose exec backend chown -R www-data:www-data /var/www/html

echo "✅ Permissões corrigidas com sucesso!"
echo "🎉 Agora você pode editar os arquivos do backend sem sudo!"

