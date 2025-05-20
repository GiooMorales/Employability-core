# Employabilit-Core

Sistema de gerenciamento de perfil profissional e portfólio.

## Requisitos

- PHP 8.1 ou superior
- Composer
- MySQL 5.7 ou superior
- Node.js e NPM

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/SEU_USUARIO/Employabilit-Core.git
cd Employabilit-Core
```

2. Instale as dependências do PHP:
```bash
composer install
```

3. Copie o arquivo de ambiente:
```bash
cp .env.example .env
```

4. Gere a chave da aplicação:
```bash
php artisan key:generate
```

5. Configure o banco de dados no arquivo `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=employabilit_core
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

6. Execute as migrações do banco de dados:
```bash
php artisan migrate
```

7. Instale as dependências do Node.js e compile os assets:
```bash
npm install
npm run dev
```

8. Inicie o servidor de desenvolvimento:
```bash
php artisan serve
```

O projeto estará disponível em `http://localhost:8000`

## Estrutura do Projeto

- `app/` - Contém os controllers, models e lógica principal
- `resources/` - Views, assets e arquivos de frontend
- `public/` - Arquivos públicos e compilados
- `database/` - Migrações e seeds
- `routes/` - Definição das rotas da aplicação

## Funcionalidades

- Gerenciamento de perfil profissional
- Portfólio de projetos
- Certificações
- Experiências profissionais
- Formação acadêmica
- Habilidades técnicas

## Contribuição

1. Faça um Fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.
