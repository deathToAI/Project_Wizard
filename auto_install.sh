#!/bin/bash

# Define variáveis de cor para a saída
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # Sem Cor

echo -e "${GREEN}--- Iniciando a instalação automatizada do Sistema de Refeições ---${NC}"

# --- PASSO 1: Instalação de Pacotes ---
echo -e "\n${YELLOW}[PASSO 1/4] Instalando pacotes necessários (php, sqlite, intl, composer)...${NC}"
if ! sudo apt-get update || ! sudo apt-get install -y php php-sqlite3 php-intl composer; then
    echo -e "${RED}ERRO: Falha ao instalar pacotes. Verifique os logs do apt-get.${NC}"
    exit 1
fi
echo -e "${GREEN}Pacotes instalados com sucesso.${NC}"

echo -e "\n${YELLOW}[PASSO 2/4] Instalando dependências do PHP com o Composer...${NC}"
# O nome correto do pacote é phpoffice/phpspreadsheet
if ! composer require phpoffice/phpspreadsheet; then
    echo -e "${RED}ERRO: Falha ao instalar dependências com o Composer.${NC}"
    exit 1
fi
echo -e "${GREEN}Dependências do Composer instaladas com sucesso.${NC}"

# A instalação do pacote php-intl geralmente já ativa a extensão.
# Este passo é uma verificação adicional para garantir que tudo está correto.
PHP_INI_PATH=$(php -r "echo php_ini_loaded_file();")
if [ -f "$PHP_INI_PATH" ]; then
    echo -e "\n${YELLOW}Verificando configuração do PHP em: $PHP_INI_PATH...${NC}"
    # Garante que as extensões estão ativas
    if ! grep -q "^extension=intl" "$PHP_INI_PATH"; then
        echo "Ativando a extensão 'intl'..."
        sudo sed -i 's/;extension=intl/extension=intl/' "$PHP_INI_PATH"
    fi
    if ! grep -q "^extension=pdo_sqlite" "$PHP_INI_PATH"; then
        echo "Ativando a extensão 'pdo_sqlite'..."
        sudo sed -i 's/;extension=pdo_sqlite/extension=pdo_sqlite/' "$PHP_INI_PATH"
    fi
    echo -e "${GREEN}Configuração do PHP verificada.${NC}"
else
    echo -e "${YELLOW}AVISO: Não foi possível encontrar o arquivo php.ini. A verificação das extensões foi pulada.${NC}"
fi

# --- PASSO 3: Configuração do Usuário e Banco de Dados ---
echo -e "\n${YELLOW}[PASSO 3/4] Configurando o usuário Admin e Furriel...${NC}"

if [ ! -f "gerar_pass.php" ]; then
    echo -e "${RED}ERRO: O arquivo 'gerar_pass.php' não foi encontrado no diretório atual.${NC}"
    exit 1
fi

# Solicita a senha do Admin
read -s -p "Digite a nova senha para o usuário 'admin': " ADMIN_PASS
echo
read -s -p "Confirme a senha do 'admin': " ADMIN_PASS_CONFIRM
echo
if [ "$ADMIN_PASS" != "$ADMIN_PASS_CONFIRM" ] || [ -z "$ADMIN_PASS" ]; then
    echo -e "${RED}ERRO: As senhas do admin não coincidem ou estão vazias. Abortando.${NC}"
    exit 1
fi

# Solicita a senha do Furriel
read -s -p "Digite a nova senha para o usuário 'furriel': " FURRIEL_PASS
echo
read -s -p "Confirme a senha do 'furriel': " FURRIEL_PASS_CONFIRM
echo
if [ "$FURRIEL_PASS" != "$FURRIEL_PASS_CONFIRM" ] || [ -z "$FURRIEL_PASS" ]; then
    echo -e "${RED}ERRO: As senhas do furriel não coincidem ou estão vazias. Abortando.${NC}"
    exit 1
fi

# Usa 'sed' para substituir as senhas padrão no arquivo
sed -i "s|'senha'    => 'AdminPass123',|'senha'    => '$ADMIN_PASS',|g" gerar_pass.php
sed -i "s|'senha'    => 'furriel',|'senha'    => '$FURRIEL_PASS',|g" gerar_pass.php
echo -e "${GREEN}Senhas atualizadas no script de configuração.${NC}"

echo -e "\n${YELLOW}[PASSO 4/4] Executando o script para criar o banco de dados e os usuários...${NC}"
php gerar_pass.php

echo -e "\n\n${GREEN}--- Instalação e Configuração Inicial Concluídas! ---${NC}"
echo -e "${RED}=======================================================================${NC}"
echo -e "${RED}  AVISO IMPORTANTE: Por segurança, o arquivo 'gerar_pass.php' deve   ${NC}"
echo -e "${RED}  ser DELETADO agora, pois ele não é mais necessário.                ${NC}"
echo -e "${RED}=======================================================================${NC}"

read -p "Deseja deletar o arquivo 'gerar_pass.php' agora? (s/N) " -n 1 -r
echo
if [[ $REPLY =~ ^[Ss]$ ]]; then
    rm gerar_pass.php
    echo -e "${GREEN}'gerar_pass.php' foi deletado.${NC}"
else
    echo -e "${YELLOW}Lembre-se de deletar 'gerar_pass.php' manualmente!${NC}"
fi

echo -e "\n${GREEN}Tudo pronto! Faça login como 'admin' com a senha que você definiu.${NC}"