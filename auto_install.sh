#!/bin/bash

# Define variáveis de cor para a saída
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # Sem Cor

# --- Variáveis de Configuração ---
PROJECT_NAME="Project_Wizard"
APACHE_WWW_DIR="/var/www/html"
PROJECT_DEST_DIR="$APACHE_WWW_DIR/$PROJECT_NAME"
PROJECT_SOURCE_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"

# --- Funções Auxiliares ---
check_root() {
    if [ "$EUID" -ne 0 ]; then
        echo -e "${RED}ERRO: Este script precisa ser executado com privilégios de superusuário (root).${NC}"
        echo -e "${YELLOW}Por favor, execute com: sudo ./auto_install.sh${NC}"
        exit 1
    fi
}

# --- Início do Script ---
clear
echo -e "${GREEN}--- Iniciando a instalação automatizada do ${PROJECT_NAME} ---${NC}"
echo -e "Este script irá instalar o Apache, PHP, e configurar o projeto."
read -p "Deseja continuar? (s/N) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    echo "Instalação cancelada."
    exit 0
fi

# --- PASSO 0: Verificação de Root ---
check_root

# --- PASSO 1: Instalação de Pacotes ---
echo -e "\n${YELLOW}[PASSO 1/6] Instalando pacotes do sistema (Apache, PHP, Composer...)${NC}"
if ! apt-get update || ! apt-get install -y apache2 libapache2-mod-php php php-sqlite3 php-intl composer; then
    echo -e "${RED}ERRO: Falha ao instalar pacotes. Verifique os logs do apt-get.${NC}"
    exit 1
fi
echo -e "${GREEN}Pacotes do sistema instalados com sucesso.${NC}"

# --- PASSO 2: Mover Projeto e Definir Permissões ---
echo -e "\n${YELLOW}[PASSO 2/6] Movendo arquivos do projeto para ${PROJECT_DEST_DIR}...${NC}"
mkdir -p "$PROJECT_DEST_DIR"
rsync -a --exclude='.git' --exclude='auto_install.sh' "$PROJECT_SOURCE_DIR/" "$PROJECT_DEST_DIR/"
chown -R www-data:www-data "$PROJECT_DEST_DIR"
echo -e "${GREEN}Arquivos movidos e permissões definidas.${NC}"

# --- PASSO 3: Instalar Dependências do PHP ---
echo -e "\n${YELLOW}[PASSO 3/6] Instalando dependências do PHP com o Composer...${NC}"
cd "$PROJECT_DEST_DIR" || exit
if ! sudo -u www-data composer require phpoffice/phpspreadsheet; then
    echo -e "${RED}ERRO: Falha ao instalar dependências com o Composer.${NC}"
    exit 1
fi
echo -e "${GREEN}Dependências do Composer instaladas com sucesso.${NC}"

# --- PASSO 4: Configuração do Banco de Dados ---
echo -e "\n${YELLOW}[PASSO 4/6] Configurando o banco de dados e usuários...${NC}"

if [ ! -f "gerar_pass.php" ]; then
    echo -e "${RED}ERRO: O arquivo 'gerar_pass.php' não foi encontrado em ${PROJECT_DEST_DIR}.${NC}"
    exit 1
fi

# Solicita a senha do Admin
read -s -p "Digite a nova senha para o usuário 'admin': " ADMIN_PASS
echo
if [ "$ADMIN_PASS" != "$ADMIN_PASS_CONFIRM" ] || [ -z "$ADMIN_PASS" ]; then
    echo -e "${RED}ERRO: As senhas do admin não coincidem ou estão vazias. Abortando.${NC}"
    exit 1
fi

# Solicita a senha do Furriel
read -s -p "Digite a nova senha para o usuário 'furriel': " FURRIEL_PASS
echo
if [ "$FURRIEL_PASS" != "$FURRIEL_PASS_CONFIRM" ] || [ -z "$FURRIEL_PASS" ]; then
    echo -e "${RED}ERRO: As senhas do furriel não coincidem ou estão vazias. Abortando.${NC}"
    exit 1
fi

# Usa 'sed' para substituir as senhas padrão no arquivo
sed -i "s|'senha'    => 'AdminPass123'|'senha'    => '$ADMIN_PASS'|g" gerar_pass.php
sed -i "s|'senha'    => 'furriel'|'senha'    => '$FURRIEL_PASS'|g" gerar_pass.php
echo -e "${GREEN}Senhas atualizadas no script de configuração.${NC}"

echo "Executando o script para criar o banco de dados e os usuários..."
sudo -u www-data php gerar_pass.php
echo -e "${GREEN}Banco de dados e usuários criados.${NC}"

read -p "Deseja deletar o arquivo 'gerar_pass.php' agora? (s/N) " -n 1 -r
echo
if [[ $REPLY =~ ^[Ss]$ ]]; then
    rm gerar_pass.php
    echo -e "${GREEN}'gerar_pass.php' foi deletado.${NC}"
else
    echo -e "${YELLOW}Lembre-se de deletar 'gerar_pass.php' manualmente!${NC}"
fi

# --- PASSO 5: Configuração do Apache ---
echo -e "\n${YELLOW}[PASSO 5/6] Configurando o Apache (SSL e VirtualHost)...${NC}"

a2enmod ssl rewrite
mkdir -p /etc/apache2/ssl

if [ ! -f "/etc/apache2/ssl/apache.key" ]; then
    echo "Gerando certificado SSL autoassinado..."
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout /etc/apache2/ssl/apache.key \
        -out /etc/apache2/ssl/apache.crt \
        -subj "/C=BR/ST=Dev/L=Dev/O=Dev/CN=localhost"
fi

SSL_CONF_PATH="/etc/apache2/sites-available/${PROJECT_NAME}-ssl.conf"
echo "Criando arquivo de configuração do Apache em ${SSL_CONF_PATH}..."
cat > "$SSL_CONF_PATH" <<EOF
<VirtualHost *:443>
    ServerName localhost
    DocumentRoot ${PROJECT_DEST_DIR}

    SSLEngine on
    SSLCertificateFile /etc/apache2/ssl/apache.crt
    SSLCertificateKeyFile /etc/apache2/ssl/apache.key

    <Directory ${PROJECT_DEST_DIR}>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

echo "Configurando redirecionamento HTTP -> HTTPS..."
cat > /etc/apache2/sites-available/000-default.conf <<EOF
<VirtualHost *:80>
    ServerName localhost
    Redirect permanent / https://localhost/
</VirtualHost>
EOF

a2ensite "${PROJECT_NAME}-ssl.conf"
a2dissite 000-default.conf

# --- PASSO 6: Finalização ---
echo -e "\n${YELLOW}[PASSO 6/6] Reiniciando o Apache para aplicar as configurações...${NC}"
if ! systemctl restart apache2; then
    echo -e "${RED}ERRO: Falha ao reiniciar o Apache. Verifique a configuração com 'apache2ctl configtest'.${NC}"
    exit 1
fi

echo -e "\n\n${GREEN}--- Instalação e Configuração Concluídas! ---${NC}"
echo -e "${YELLOW}O projeto foi instalado em: ${PROJECT_DEST_DIR}${NC}"
echo -e "${GREEN}Acesse o sistema em: https://localhost${NC}"
echo -e "${YELLOW}NOTA: Você verá um aviso de segurança no navegador por causa do certificado autoassinado. Isso é normal. Apenas aceite o risco para continuar.${NC}"
echo -e "\n${GREEN}Tudo pronto! Faça login como 'admin' com a senha que você definiu.${NC}"