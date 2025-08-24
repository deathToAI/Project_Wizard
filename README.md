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


```sh
cat /etc/issue
#Ubuntu 24.04.2 LTS \n \l

uname -a
#Linux factory 6.14.0-27-generic #27~24.04.1-Ubuntu SMP PREEMPT_DYNAMIC Tue Jul 22 17:38:49 UTC 2 x86_64 x86_64 x86_64 GNU/Linux

php -v
#PHP 8.3.6 (cli) (built: Jul 14 2025 18:30:55) (NTS)
```

##  Tecnologias Utilizadas
- **Backend**: PHP + PHP Composer(PHPSpreadsheets)
- **Banco de Dados**: SQLite(com pdo do PHP)
- **Frontend**: HTML, CSS e JavaScript puro

## Como Instalar

### Instalação do Ambiente de Desenvolvimento

#### 0. Instalação Rápida (Recomendado)
Para um ambiente baseado em Debian/Ubuntu, você pode usar o script de instalação automatizada. Ele cuidará de todos os passos abaixo.

```bash
chmod +x auto_install.sh
./auto_install.sh
```

Se preferir a instalação manual, siga os passos abaixo.

#### 1. Instalação de Pacotes
```
sudo apt install php php-sqlite3 php-pdo composer 
composer init
composer require phpspreadsheet
```
#### 2. Configuração do php
```
nano /etc/php/8.3/cli/php.ini 
#Descomentar extension=intl (linha 946)
#Descomentar extension=pdo_sqlite (linha 960)
```

#### 3. Modificar a senha de administrador em <span style="color:red">*'gerar_pass.php'*</span> e rodar o script com 
`php gerar_pass.php`. Isso fará o seguinte:
    a. Criará a database em **/raiz/do/projeto/database/refeicoes.sqlite** (Pode ser modificado desde que as conexões em **database.php** também sejam modificadas)
    b.Criará as tabelas de **'user'** e **'arranchados'**
    c.Criará o user **'admin'** e **'furriel'** com a senha já criptografa
## Lembre-se de **DELETAR gerar_pass.php**

#### 4. Fazer login como admin e inserir os usuários
