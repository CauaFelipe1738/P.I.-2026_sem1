# Engenharia de software

## Requisitos Funcionais
- Login/registro de usuário, diferenciando funcionário e gerentes. <br>
- O cadastro de usuário é realizado pelos administradores, que também definem se o cadastro é para um usuário comum, ou um administrador. <br>
- Plataforma deve ter quiz- baseado em Duolingo e Khan Academy- interativo, sobre temas  como segurança da informação, compliance, processos internos, governança e boas práticas técnicas. <br>
- Ao responder um questionário, o usuário recebe uma pontuação. <br>
- O gerente deve poder monitorar o desempenho dos usuários. <br>
- Padronização e controle dos treinamentos obrigatórios, centralizando conteúdos e permitindo fácil atualização. <br>
- As perguntas do questionário são objetivas e têm resposta única. <br>
- O sistema lista os melhores funcionários pela pontuação no site. <br>
- Os administradores gerenciam os clientes, podendo manipular atributos e excluir usuários.
- Os administradores fazem a personalização do site, criando listas, questionários, e recompensas de ranking.

## Requisitos Não-Funcionais
- Os questionários são separados por área; <br>
- Ambiente seguro <br>
- Baixa complexidade <br>
- Escalabilidade: deve existir a possibilidade de alterar/adicionar usuários, equipes, etc. <br>
- Deve prover engajamento <br>
- Ambiente seguro e aderente à cultura tecnológica da empresa, reforçando valores de inovação e aprendizagem contínua <br>
- Elementos visuais dinâmicos <br>

## Especialização de casos de uso
### *Realizar login*

Ator: Usuário
Descrição: Acessar as principais funcionalidades do sistemas
Pré-condições: O usuário deve estar cadastrado

_Fluxo Principal:_
1. Usuário acessa a tela de login
2. Usuário informa e-mail e senha
3. Sistema valida os dados
3.a - <b>Dados inválido</b>
  - Sistema exibe mensagem de erro
  - Usuário pode tentar novamente
4. Sistema autentica o usuário
5. Sistema redireciona para a área logada

Pós-Condições:
O usuário acessa o sistema

### *Cadastro de usuário*

Ator: administrador
Descrição: Permitir o acesso de novos usuários
Pré-condições: O ator deve ter acesso de administrador

_Fluxo Principal:_
1. O usuário acessa a página de configuração
2. O admin registra as informações do novo usuário
2a. - <b>Registrar como admin</b>
   O admin escolhe cadastrar o novo usuário como administrador
3. Sistema valida os dados
3.a - <b>Dados inválidos ou Dados já cadastrado</b>
  Sistema exibe mensagem de erro
  Usuário pode tentar novamente

Pós-condições:
Um novo cadastro de usuário é feita, e nele associado com email e senha.

### *Acessar questionário*

Atores: Usuários
Descrição: Acesso de questionário para resolução de perguntas, e feedback da resolução enviada
Pré-requisitos: O usuário deve estar logado

_Fluxo principal_
1. O usuário acessa uma lista de perguntas na página de treinamento
2. O usuário responde as perguntas
3. O sistema dá o feedback sobre a resposta dada
4. O sistema atribui uma pontuação para o usuário
4.a <b>Feedback adicional</b>
 Após o usuário completar a lista, o sistema oferece um feedback amplo

Pós-condições:
O pontuação do usuário é atualizada, e consequentemente seu ranking.

### *Feedback contínuo*

Atores: Usuários e administradores
Descrição: Os atores vêem sua condição dentro do sistema, como pontuação, ranking. Para administradores, podem ver também informações adicionais como desempenho global, colaboradores ativos, etc.
Pré-requisitos: O usuário deve estar autenticado no sistema

_Fluxo principal_
1. Acessar a página principal, o dashboard do sistema.

### *Personalização do site*

Atores: Administradores
Descrição: Aplicar mudanças como criação de novas perguntas, recompensas associados a pontuação, novas listas de perguntas, etc.
Pré-requisitos: Usuário deve ter autenticação de administrador

_Fluxo Principal_
1. Acessar a página principal, o dashboard
2. Escolher uma das opções de personalização
3.  Submeter as informações corretas para personalização
3.a - <b>Informações incorretas</b>
  O processo é abordado
  O sistema joga uma mensagem de erro
  O usuário pode tentar novamente

### *Acessar Ranking*
Atores: Usuários
Descrição: O usuário vê a posição relativa dos usuários num ranking global.
Pré-requisitos: O usuário deve estar autenticado

_Fluxo Principal_
1. Acessar a página principal, o dashboard

# Tecnologia da Informação e da Conectividade

## Descrição da Comunicação do Sistema


## Topologia de Rede

## Protocolos Utilizados

## Conceitos de Endereçamento

## Segurança Básica
 
## Escalabilidade

## Diagrama de Rede

## Clareza de Pesquisa

# Banco de dados

*complete aqui*

# Linguagem de programação

*fluxograma*

