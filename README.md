<img width="1280" height="534" alt="CorpWare2" src="https://github.com/user-attachments/assets/b1774623-69dc-48ce-abde-a093ae612166" />


<p align="center">
<img src="https://img.shields.io/badge/Windows-Supported-0078D6?style=for-the-badge&logo=windows&logoColor=white"/>
  <img src="https://img.shields.io/badge/Linux-Supported-FCC624?style=for-the-badge&logo=linux&logoColor=black"/>
  <img src="https://img.shields.io/badge/macOS-Supported-000000?style=for-the-badge&logo=apple&logoColor=white"/>
  <img src="https://img.shields.io/badge/Android-Supported-3DDC84?style=for-the-badge&logo=android&logoColor=white"/>
  <img src="https://img.shields.io/badge/iOS-Supported-000000?style=for-the-badge&logo=apple&logoColor=white"/>
</p>

## Tabela de Conteúdos

- [📖 Descrição detalhada](#-descrição-detalhada)
- [🚀 Funcionalidades](#-funcionalidades)
- [🛠 Tecnologias usadas](#-tecnologias-usadas)
- [⚙️ Requisitos](#requisitos)

## 📖 Descrição detalhada
A plataforma CorpWare tem como objetivo transformar treinamentos corporativos obrigatórios em experiências mais interativas, dinâmicas e engajadoras por meio da gamificação. Voltada para empresas de base tecnológica, a solução oferece quizzes inspirados em plataformas como Duolingo e Khan Academy, abordando temas como segurança da informação, compliance, governança, processos internos e boas práticas técnicas.

O sistema conta com autenticação de usuários, diferenciação entre funcionários e gerentes, acompanhamento de desempenho em tempo real, ranking, níveis e feedback contínuo. Além disso, permite a centralização e atualização simplificada dos conteúdos, garantindo escalabilidade, padronização dos treinamentos e maior retenção de conhecimento pelos colaboradores.

## 🚀 Funcionalidades

- Sistema de autenticação com login e registro de usuários.
- Diferenciação entre funcionários e gerentes.
- Registro de gerentes restrito a administradores autorizados.
- Plataforma de quizzes gamificados inspirada em Duolingo e Khan Academy.
- Questionários organizados por áreas e temas específicos.
- Conteúdos voltados para:
  - Segurança da informação
  - Compliance
  - Governança
  - Processos internos
  - Boas práticas técnicas
- Sistema de pontuação e feedback em tempo real.
- Ranking competitivo entre usuários baseado em desempenho.
- Recompensas virtuais e reconhecimento por desempenho.
- Painel gerencial para monitoramento de equipes.
- Acompanhamento de desempenho individual e coletivo.
- Centralização dos treinamentos obrigatórios da empresa.
- Atualização simplificada de conteúdos e questionários.
- Elementos visuais dinâmicos e interativos.
- Ambiente seguro e alinhado à cultura tecnológica corporativa.
- Estrutura escalável para expansão de usuários, equipes e conteúdos.
- Experiência focada em engajamento e aprendizagem contínua.

## 🛠 Tecnologias usadas
<table>
  <tr>
    <tr>
      <td>MySQL
      <td>PHP</td>
      <td>Laravel</td>
      <td>Laragon</td>
      <td>Composer</td>
      <td>HTML</td>
      <td>CSS</td>
      <td>Java Script</td>
      </td>
    </tr>
  <td>9.3.0</td>
  <td>8.5</td>
  <td>13.0</td>
  <td>8.6.1</td>
  <td>2.9.8</td>
    <td>5</td>
    <td>3</td>
    <td>ES2025</td>
  </tr>
</table>

## ⚙️ Requisitos

Antes de começar, você precisará ter instalado em sua máquina:

- PHP 8+
- Composer
- Laragon (Recomendado para ambiente Windows)
- MySQL (Já incluso no pacote completo do Laragon)
- Navegador atualizado

### Clone o repositório

```bash
git clone https://github.com/CauaFelipe1738/P.I.-2026_sem1.git
cd website-php
```

### Instale as dependências

Com o terminal aberto na raiz do projeto, instale as bibliotecas e dependências do PHP gerenciadas pelo Composer:

```bash
composer install
```

### Configure o ambiente

Crie o arquivo de configuração de ambiente copiando o arquivo de exemplo fornecido pelo Laravel:

```bash
cp .env.example .env
```

Também é possível simplesmente copiar o arquivo e renomeá-lo para `.env`.

Configure o banco de dados e as variáveis de ambiente conforme o arquivo `.env`. Por padrão, a seguinte configuração é colocada:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=treinamento_gamificado
DB_USERNAME=root
DB_PASSWORD=
```

### Gere a chave da aplicação

```bash
php artisan key:generate
```

### Execute as migrations

```bash
php artisan migrate --seed
```

`--seed` é uma flag que automaticamente popula o banco de dados com dados do `DatabaseSeeder.php`, como o usuário `admin` com senha `admin`, áreas e questões padrão.

### Execute o projeto

Com o terminal aberto na raiz do projeto, rode o comando de iniciar o servidor:

```bash
php artisan serve
```

Em seguida, abra o navegador e acesse http://localhost:8000. Utilize o usuário `admin` com senha `admin` como primeiro acesso.
