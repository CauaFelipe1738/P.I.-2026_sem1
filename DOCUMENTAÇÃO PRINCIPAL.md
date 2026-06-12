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
- Os questionários são separados por área.

## Requisitos Não-Funcionais
- Ambiente seguro <br>
- Baixa complexidade <br>
- Escalabilidade: deve existir a possibilidade de alterar/adicionar usuários, equipes, etc. <br>
- Deve prover engajamento <br>
- Ambiente seguro e aderente à cultura tecnológica da empresa, reforçando valores de inovação e aprendizagem contínua <br>
- Elementos visuais dinâmicos <br>

## Diagrama de Casos de Uso
<img width="1498" height="1501" alt="Casos de uso - PIsem1" src="https://github.com/user-attachments/assets/0f695fb6-c16b-472a-a4b8-f9383de29b43" />

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
1. Acessar a página principal, o dashboard do sistema, lá estão todas as informações acessíveis.

### *Personalização do site*

Atores: Administradores
Descrição: Aplicar mudanças como criação de novas perguntas, recompensas associados a pontuação, novas listas de perguntas, etc.
Pré-requisitos: Usuário deve ter autenticação de administrador.

_Fluxo Principal_
1. Acessar a página principal, o dashboard
2. Escolher uma das opções de personalização
3.  Submeter as informações corretas para personalização
3.a - <b>Informações incorretas</b>
  O processo é abortado
  O sistema joga uma mensagem de erro
  O usuário pode tentar novamente

### *Acessar Ranking*
Atores: Usuários
Descrição: O usuário vê a posição relativa dos usuários num ranking global.
Pré-requisitos: O usuário deve estar autenticado

_Fluxo Principal_
1. Acessar a página principal, o dashboard, onde está as informações de ranking.
2. Ver a listagem.
2.a - *Sem acesso à Internet*
 A listagem não está disponível.

# Tecnologia da Informação e da Conectividade

## Descrição da Comunicação do Sistema
A plataforma possui um sistema de comunicação entre o Front-end, Back-end e o Banco de Dados. o sistema funciona baseado em arquitetura cliente-servidor: Os clientes fazem requisições para o servidor que dispõe do conteúdo da plataforma e armazena os dados. Os hosts devem estar na mesma rede que o servidor para realizar requisições.
Neste caso, o front-end do site requisitado é guardado no navegador do cliente, e todas as informações do banco de dados conversam com o Front-end pelo uso de APIs, que pegam a informação bruta, convertem para uma formatação que o front-end entenda e vice-versa. Cada requisição enviada para o servidor contém as especificação de uma ação que incluem visualização e alteração de dados do banco.

## Topologia de Rede
Para uma implementação real da rede, propõe-se a topologia estrela, onde o switch serve como ponte para comunicação de todos os outros hosts.

## Protocolos Utilizados
Entre os principais protocolos que são utilizados para comunicação, destaca-se: 
- DNS: que resolve o endereço IP do servidor da plataforma para um nome acessível e intuitivo;
- DHCP: efetua endereçamento dinâmico de computadores, prevenindo erros de endereçamento e trabalho repetitivo;
- HTTP: atua na camada de aplicação do modelo OSI, mostrando o conteúdo do site para o usuário e processando as ações que ele realiza para solicitar ao servidor.
- TCP: atua na camada de transporte, fazendo a ponte entre um dado e outro enquanto garante que cada pacote seja enviado corretamente;
- IP: atua na camada de rede, ele define uma separação lógica entre os dispositivos.

## Segurança Básica
O sistema de rede possui restrição de administradores para certas ações como alteração de dados, previnindo ações arriscadas que comprometem a integridade dos dados. Além disso, a plataforma é acessada somente pela rede interna do servidor (Intranet), que aplicado à ambientes reais, seria acessado somente pelo hosts dentro da rede de uma empresa.
Os dados sensíveis são criptografados para manter a integridade dos dados.

## Escalabilidade
Como o sistema atua numa arquitetura cliente-servidor, adicionar novos servidores seria uma solução para atender mais clientes, uma vez que iria diminuir a sobrecarga de requisições.
Outra possibilidade- que não foi concebida nesta implementação- é de usuários acessarem a plataforma fora da rede local do servidor (Internet), que exigiria um servidor em uma rede pública para atender usuários exteriores.

## Diagrama de Rede
<img width="797" height="405" alt="Captura de tela 2026-06-11 194344" src="https://github.com/user-attachments/assets/50838897-68ad-483d-8c94-0255629992e6" />
Na figura acima, os computadores se comunicam dentro de uma rede interna, e acessam os serviços da plataforma. Dessa forma, o acesso é feito exclusivamente pelo usuários dentro da empresa.

# Banco de dados

*complete aqui*

# Linguagem de programação
<img width="1534" height="1291" alt="Diagrama sem nome drawio (1)" src="https://github.com/user-attachments/assets/5568c545-eea8-45e3-a563-484ec7ccc513" />




