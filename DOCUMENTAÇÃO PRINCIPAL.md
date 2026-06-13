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
<img width="797" height="405" alt="Captura de tela 2026-06-11 194344" src="https://github.com/user-attachments/assets/50838897-68ad-483d-8c94-0255629992e6" /> <br>
Na figura acima, os computadores se comunicam dentro de uma rede interna, e acessam os serviços da plataforma. Dessa forma, o acesso é feito exclusivamente pelo usuários dentro da empresa.

# Banco de dados

## Levantamento de dados
Um *usuário* acessa o sistema pelo seu username e senha. Ali, ele pode responder uma *lista* que possui uma certa quantidade de *perguntas* de alguma *area* (tema) com uma certa quantidade de *respostas*. Cada pergunta, se respondida corretamente, dá como recompensa ao usuário um valor de pontos. Além disso, os usuários com maior pontuação são ordenados em um *ranking*, que oferece títulos àqueles com maior pontuação, propondo uma competição amigável. <br>
Uma lista pode ter várias perguntas, e uma pergunta pode pertencer a várias listas; As listas com perguntas podem ser respondidas por vários usuários, e um usuário pode responder várias listas.

## Diagrama de Entidade-Relacionamento
<img width="1088" height="670" alt="der" src="https://github.com/user-attachments/assets/f9d2beb7-8d6f-4155-a37b-6ca95edb521b" />

## Modelo Lógico
<img width="833" height="724" alt="modelo-logico" src="https://github.com/user-attachments/assets/2dfb26b7-628b-4612-a6ee-21ab62a94493" />

## Dicionário de Dados
### Tabela de RANKING
<table>
        <tr>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Tipo</th>
            <th>Tamanho</th>
            <th>Domínio</th>
            <th>Formato</th>
            <th>Restrições</th>
        </tr>
        <tr>
            <td>id_ranking</td>
            <td>id do ranque</td>
            <td>inteiro</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>não deve ser nulo</td>
        </tr>
        <tr>
            <td>qtd_pessoas</td>
            <td>até que posição do ranque geral para receber o ranque</td>
            <td>inteiro</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>deve ser um inteiro positivo e único</td>
        </tr>
        <tr>
            <td>titulo</td>
            <td>titulo que o funcionario recebera caso esteja dentro do ranque</td>
            <td>texto</td>
            <td>30</td>
            <td>-</td>
            <td>-</td>
            <td>não deve ser nulo</td>
        </tr>
        <tr>
            <td>sobre</td>
            <td>informações sobre o ranque, preferivelmente para colocar os benefícios de estar nele</td>
            <td>texto</td>
            <td>65,535</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
        </tr>
</table>

### Tabela de FUNCIONÁRIOS
<table>
  <tr>
    <th>Nome</th>
    <th>Descrição</th>
    <th>Tipo</th>
    <th>Tamanho</th>
    <th>Domínio</th>
    <th>Formato</th>
    <th>Restrições</th>
  </tr>
  <tr>
    <td>id_funcionario</td>
    <td>id do funcionario</td>
    <td>inteiro</td>
    <td>-</td>
    <td>-</td>
    <td>-</td>
    <td>não deve ser nulo</td>
  </tr>
  <tr>
    <td>username</td>
    <td>nome de usuário para o funcionario logar</td>
    <td>texto</td>
    <td>40</td>
    <td>-</td>
    <td>-</td>
    <td>não deve ser nulo e único</td>
  </tr>
  <tr>
    <td>nome_funcionario</td>
    <td>nome do funcionario</td>
    <td>texto</td>
    <td>40</td>
    <td>-</td>
    <td>-</td>
    <td>não deve ser nulo</td>
  </tr>
  <tr>
    <td>senha</td>
    <td>senha utilizada para acesso o site</td>
    <td>texto</td>
    <td>40</td>
    <td>-</td>
    <td>-</td>
    <td>não deve ser nulo</td>
  </tr>
  <tr>
    <td>admin</td>
    <td>determina de o funcionario tem privilégios de administrador</td>
    <td>booleano</td>
    <td>-</td>
    <td>TRUE = é administrador<br>FALSE = não é administrador</td>
    <td>-</td>
    <td>não deve ser nulo</td>
  </tr>
  <tr>
    <td>pontos</td>
    <td>quantidade de pontos que o funcionario possui</td>
    <td>inteiro</td>
    <td>-</td>
    <td>-</td>
    <td>-</td>
    <td>deve ser um inteiro positivo</td>
  </tr>
</table>

### Tabela de LISTA
<table>
        <tr>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Tipo</th>
            <th>Tamanho</th>
            <th>Domínio</th>
            <th>Formato</th>
            <th>Restrições</th>
        </tr>
        <tr>
            <td>id_lista</td>
            <td>id da lista</td>
            <td>inteiro</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>não deve ser nulo</td>
        </tr>
        <tr>
            <td>inicio</td>
            <td>data que a lista estará disponivel para ser feita</td>
            <td>data</td>
            <td>-</td>
            <td>-</td>
            <td>"AAAA-MM-DD"</td>
            <td>não deve ser nulo</td>
        </tr>
        <tr>
            <td>fim</td>
            <td>data que a lista deixara de estar disponivel para ser feita</td>
            <td>data</td>
            <td>-</td>
            <td>-</td>
            <td>"AAAA-MM-DD"</td>
            <td>o fim deve ser após o inicio e não deve ser nulo</td>
        </tr>
</table>

### Tabela de AREA
<table>
  <tr>
    <th>Nome</th>
    <th>Descrição</th>
    <th>Tipo</th>
    <th>Tamanho</th>
    <th>Domínio</th>
    <th>Formato</th>
    <th>Restrições</th>
  </tr>
  <tr>
    <td>id_area</td>
    <td>id da area</td>
    <td>inteiro</td>
    <td>-</td>
    <td>-</td>
    <td>-</td>
    <td>não deve ser nulo</td>
  </tr>
  
  <tr>
    <td>nome_area</td>
    <td>nome da area da pergunta</td>
    <td>texto</td>
    <td>30</td>
    <td>-</td>
    <td>-</td>
    <td>não deve ser nulo</td>
  </tr>
</table>

### Tabela de PERGUNTAS
<table>
    <tr>
      <th>Nome</th>
      <th>Descrição</th>
      <th>Tipo</th>
      <th>Tamanho</th>
      <th>Domínio</th>
      <th>Formato</th>
      <th>Restrições</th>
    </tr>
    <tr>
      <td>id_pergunta</td>
      <td>id da pergunta</td>
      <td>inteiro</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>não deve ser nulo</td>
    </tr>
    <tr>
      <td>idf_area</td>
      <td>id da area da pergunta</td>
      <td>inteiro</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>não deve ser nulo</td>
    </tr>
    <tr>
      <td>pergunta</td>
      <td>a pergunta</td>
      <td>texto</td>
      <td>65,535</td>
      <td>-</td>
      <td>-</td>
      <td>não deve ser nulo</td>
    </tr>
    <tr>
      <td>valor</td>
      <td>a quantidade de pontos que vale a pergunta</td>
      <td>inteiro</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>deve ser um inteiro positivo</td>
    </tr>
    <tr>
      <td>imagem</td>
      <td>possível imagem que pode ser atribuída à pergunta (deve ser um link)</td>
      <td>texto</td>
      <td>65,535</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
    </tr>
</table>

### Tabela de RESPOSTAS
<table>
    <tr>
      <th>Nome</th>
      <th>Descrição</th>
      <th>Tipo</th>
      <th>Tamanho</th>
      <th>Domínio</th>
      <th>Formato</th>
      <th>Restrições</th>
    </tr>
    <tr>
      <td>id_resposta</td>
      <td>id da resposta</td>
      <td>inteiro</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>não deve ser nulo</td>
    </tr>
    <tr>
      <td>idf_pergunta</td>
      <td>id da pergunta da qual a resposta faz parte</td>
      <td>inteiro</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>não deve ser nulo</td>
    </tr>
    <tr>
      <td>resposta</td>
      <td>a resposta</td>
      <td>texto</td>
      <td>300</td>
      <td>-</td>
      <td>-</td>
      <td>não deve ser nulo</td>
    </tr>
    <tr>
      <td>solucao</td>
      <td>identificar se ela é a resposta correta</td>
      <td>booleano</td>
      <td>-</td>
      <td>TRUE = resposta correta / FALSE = resposta incorreta</td>
      <td>-</td>
      <td>não deve ser nulo</td>
    </tr>
</table>

### Tabela de PERGUNTA_LISTA
<table>
    <tr>
      <th>Nome</th>
      <th>Descrição</th>
      <th>Tipo</th>
      <th>Tamanho</th>
      <th>Domínio</th>
      <th>Formato</th>
      <th>Restrições</th>
    </tr>
    <tr>
      <td>id_pergunta_lista</td>
      <td>id da relação de uma pergunta e lista específica</td>
      <td>inteiro</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>não deve ser nulo</td>
    </tr>
    <tr>
      <td>idf_pergunta</td>
      <td>id da pergunta</td>
      <td>inteiro</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>relação com idf_lista deve ser único e não deve ser nulo</td>
    </tr>
    <tr>
      <td>idf_lista</td>
      <td>id da lista que a pergunta está presente</td>
      <td>inteiro</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>relação com idf_pergunta deve ser único e não deve ser nulo</td>
    </tr>
</table>

### Tabela de FUNCIONÁRIO_PERGUNTA_LISTA
<table>
    <tr>
      <th>Nome</th>
      <th>Descrição</th>
      <th>Tipo</th>
      <th>Tamanho</th>
      <th>Domínio</th>
      <th>Formato</th>
      <th>Restrições</th>
    </tr>
    <tr>
      <td>idf_funcionario</td>
      <td>id do funcionario</td>
      <td>inteiro</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>não deve ser nulo</td>
    </tr>
    <tr>
      <td>idf_pergunta_lista</td>
      <td>id da pergunta de uma lista específica</td>
      <td>inteiro</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>não deve ser nulo</td>
    </tr>
    <tr>
      <td>idf_resposta</td>
      <td>id da resposta do funcionario</td>
      <td>inteiro</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>não deve ser nulo</td>
    </tr>
</table>

## Modelo Físico

### DDL
```sql

CREATE TABLE ranking (
id_ranking int auto_increment PRIMARY KEY,
qtd_pessoas int UNSIGNED NOT NULL unique,
titulo varchar(30) NOT NULL,
sobre text
);
CREATE TABLE funcionario (
id_funcionario int auto_increment PRIMARY KEY,
username varchar(40) not NULL unique,
nome_funcionario varchar(40) NOT NULL,
senha varchar(40) NOT NULL,
admin boolean not NULL,
pontos int UNSIGNED NOT NULL default 0
);
CREATE TABLE lista (
id_lista int auto_increment PRIMARY KEY,
inicio date NOT NULL,
fim date NOT NULL,
CONSTRAINT fim_depois CHECK (fim > inicio)
);
CREATE TABLE area (
id_area int auto_increment PRIMARY KEY,
nome_area varchar(30) NOT NULL
);
CREATE TABLE pergunta (
id_pergunta int auto_increment PRIMARY KEY,
idf_area int NOT NULL,
pergunta text NOT NULL,
valor int UNSIGNED NOT NULL,
image text,
FOREIGN KEY (idf_area) REFERENCES area(id_area)
);
CREATE TABLE resposta (
id_resposta int auto_increment PRIMARY KEY,
idf_pergunta int NOT NULL,
resposta varchar(300) NOT NULL,
solucao boolean NOT NULL,
FOREIGN KEY (idf_pergunta) REFERENCES pergunta(id_pergunta)
);
CREATE TABLE pergunta_lista (
id_pergunta_lista int auto_increment PRIMARY KEY,
idf_pergunta int NOT NULL,
idf_lista int NOT NULL,
unique (idf_pergunta, idf_lista),
FOREIGN KEY (idf_pergunta) REFERENCES pergunta(id_pergunta),
FOREIGN KEY (idf_lista) REFERENCES lista(id_lista)
);
CREATE TABLE funcionario_pergunta_lista (
idf_funcionario int NOT NULL,
idf_pergunta_lista int NOT NULL,
idf_resposta int NOT NULL,
PRIMARY KEY (idf_funcionario, idf_pergunta_lista),
FOREIGN KEY (idf_funcionario) REFERENCES funcionario(id_funcionario),
FOREIGN KEY (idf_pergunta_lista) REFERENCES pergunta_lista(id_pergunta_lista),
FOREIGN KEY (idf_resposta) REFERENCES resposta(id_resposta)
);
```

### Scripts e DML

```sql
--Uma view que mostra o funcionário e seu ranque (mostra a qtd_pessoas que também serve de identificador):
CREATE OR REPLACE VIEW funcio_ranque AS
select funcionario.id_funcionario, username, nome_funcionario, admin, pontos, titulo, sobre
from funcionario
inner join (
select funcionario.id_funcionario, min(qtd_pessoas) as minimo
from funcionario
inner join (
select id_funcionario, ROW_NUMBER() OVER (ORDER BY pontos DESC) AS row_position
from funcionario
) as positions on positions.id_funcionario = funcionario.id_funcionario
left join ranking on row_position <= qtd_pessoas
group by id_funcionario
) as asociacao on asociacao.id_funcionario = funcionario.id_funcionario
left join ranking on ranking.qtd_pessoas = asociacao.minimo;


--Função que determina, dependo se a resposta é correta ou não, os pontos do funcionario:
DELIMITER //
--z: id da resposta
CREATE FUNCTION vrecompensa (z int)
RETURNS int
DETERMINISTIC
BEGIN
DECLARE recompensa int;
select (valor * solucao) into recompensa from resposta
inner join pergunta on resposta.idf_pergunta = pergunta.id_pergunta
where id_resposta = z;
RETURN recompensa;
END //

DELIMITER ;

--Procedure que define uma nova relação funcionário, resposta e pergunta de certa lista, e atualiza os pontos do funcionário se a resposta for a correta
DELIMITER //

CREATE PROCEDURE responder(IN x INT, in y int, in z int)
-- x = id do usuario logado
-- y = id da pergunta em certa lista
-- z = id da resposta
BEGIN
DECLARE recompensa INT;
set recompensa = vrecompensa(z);
INSERT INTO funcionario_pergunta_lista (idf_funcionario,idf_pergunta_lista,idf_resposta) VALUE (x,y,z);
if recompensa > 0 then
update funcionario set pontos = pontos + recompensa where id_funcionario = x;
end if;
END //

DELIMITER ;

--Registra um novo usuário
INSERT INTO funcionario (username, nome_funcionario, senha, admin) value (w,x,y,z);

--Edita uma pergunta
UPDATE pergunta set pergunta = w, idf_area = x, image = y, valor = z where id_pergunta = a;

--Exclui um ranque
DELETE from ranking where id_ranking = a;

--Consulta de relações pergunta_questionario e perguntas (das perguntas) do questionario
select id_pergunta_lista, pergunta from pergunta_lista
inner join pergunta on id_pergunta = idf_pergunta
where idf_lista = a;
```

## Normalização
Foram necessários certos cuidados em relação às normalizações de banco de dados.
<img width="1063" height="558" alt="antes-da-norma" src="https://github.com/user-attachments/assets/af44b5e4-f3cb-4ec2-9bea-bd50f0f3dcf0" /> <br>
Nesta versão antiga do DER, a tabela funcionario apresentava a coluna idf_area que seria um array (conjunto de valores) das áreas do sistema que ligaria diretamente com o funcionário, aplicando a 1° normalização, essa chave estrangeira foi descartada.

# Linguagem de programação
<img width="1534" height="1291" alt="Diagrama sem nome drawio (1)" src="https://github.com/user-attachments/assets/5568c545-eea8-45e3-a563-484ec7ccc513" />




