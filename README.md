# Refeições

# Sistema simples de agendamento de refeições

A finalidade inicial é de aprendizado através da prática em javascript e PHP.

A ideia é que seja simples de implementar ao seguir a documentação, sendo executável mesmo por quem tem pouco conhecimento na área.

O software é de código aberto para utilização por qualquer pessoa que, veja nele, alguma utilidade prática.

Correções  e sugestões de segurança, eficiência e melhoria no código(Com certeza há muitas) serão sempre bem vindas. Faça um pull request, toda ajuda é bem vinda! ;)

Roadmap no link:
https://docs.google.com/spreadsheets/d/1nNVjMLiaZzlBBnzNdOK1eiQBXr0aMlWOKUVHlOuQRu0/edit?gid=0#gid=0

O projeto não tem nenhuma finalidade comercial ou lucro de qualquer natureza por parte do(s) desenvolvedor(es) e colaborador(es).

**Desenvolvido para facilitar o gerenciamento de agendamento de refeições e diminuir a dependência de sistemas arcaicos dependentes unicamente de *input* humano e de infindáveis resmas de papel!**


##  Tecnologias Utilizadas
- **Backend**: PHP 8+
- **Banco de Dados**: SQLite
- **Frontend**: HTML, CSS e JavaScript (vanilla)
- **Dependências**: Composer, PhpSpreadsheet

## Como Instalar

A forma mais fácil e recomendada de instalar o projeto em um ambiente Debian/Ubuntu é usando o script de instalação automatizada.

### Opção 1: Instalação Automatizada (Recomendado)

O script `auto_install.sh` foi criado para configurar todo o ambiente necessário, incluindo Apache, PHP, SSL e as permissões do projeto.

1.  **Clone o repositório:**
    ```bash
    git clone https://github.com/deathToAI/Project_Wizard.git
    cd Project_Wizard
    ```

2.  **Torne o script executável:**
    ```bash
    chmod +x auto_install.sh
    ```

3.  **Execute o script com `sudo`:**
    ```bash
    sudo ./auto_install.sh
    ```

O script irá guiá-lo pelo processo, solicitar as senhas para os usuários `admin` e `furriel`, e configurar o servidor web. Ao final, o sistema estará acessível em `https://localhost`.

> **Nota:** O script move o projeto para `/var/www/html/Project_Wizard`. O diretório original onde você clonou o repositório não será mais utilizado pelo servidor.

---

### Opção 2: Instalação Manual

Se você prefere configurar o ambiente manualmente, siga os passos abaixo.

#### 1. Pré-requisitos
Instale o Apache, PHP com as extensões necessárias e o Composer.

```bash
sudo apt update
sudo apt install -y apache2 libapache2-mod-php php php-sqlite3 php-intl composer
```

Se preferir a instalação manual, siga os passos abaixo.

#### 1. Instalação de Pacotes
```
sudo apt update
sudo apt install apache2 libapache2-mod-php
sudo usermod -a -G www-data dtai
sudo chown -R dtai:www-data /home/dtai/Projects/Tutorials/Project_Wizard
sudo apt install php php-sqlite3 php-pdo composer 
composer init
composer require phpspreadsheet

#Talvez seja necessário 
#sudo apt install php-xml; composer install --ignore-platform-reqs
```

#### 2. Configuração do php
```
nano /etc/php/8.3/cli/php.ini 
#Descomentar extension=intl (linha 946)
#Descomentar extension=pdo_sqlite (linha 960)
```

### 3. Configuração do Apache
```
sudo nano /etc/apache2/sites-available/project-wizard-ssl.conf
sudo nano /etc/apache2/sites-available/000-default.conf
sudo a2enmod ssl
sudo mkdir -p /etc/apache2/ssl
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/apache2/ssl/apache.key -out /etc/apache2/ssl/apache.crt
```
**Lembrar de modificar o DOCUMENT_ROOT em /etc/apache2/sites-available/project-wizard-ssl.conf para sua máquina!**

#### 4. Modificar a senha de administrador em <span style="color:red">*'gerar_pass.php'*</span> e rodar o script com 
`php gerar_pass.php`. Isso fará o seguinte:<br>
    a. Criará a database em **/raiz/do/projeto/database/refeicoes.sqlite** (Pode ser modificado desde que as conexões em **database.php** também sejam modificadas)<br>
    b.Criará as tabelas de **'user'** e **'arranchados'** <br>
    c.Criará o user **'admin'** e **'furriel'** com a senha já criptografa
## Lembre-se de **DELETAR gerar_pass.php**

#### 5. Fazer login como admin e inserir os usuários
