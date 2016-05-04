# Módulo de Integração Gerencianet para PrestaShop - Versão 0.1.1 #

O módulo Gerencianet para PrestaShop permite receber pagamentos por meio do checkout transparente da nossa API.
Compatível com as versões do PrestaShop a partir de 1.6.0.

Este é o Módulo de integração fornecido pela [Gerencianet](https://gerencianet.com.br/) para PrestaShop. Com ele, o proprietário da loja pode optar por receber pagamentos por boleto bancário e/ou cartão de crédito. Todo processo é realizado por meio do checkout transparente. Com isso, o comprador não precisa sair do site da loja para efetuar o pagamento.

Caso você tenha alguma dúvida ou sugestão, entre em contato conosco pelo site [Gerencianet](https://gerencianet.com.br/).

## Instalação

### Automática

1. Faça o download da [última versão](auto/) do módulo (arquivo 'gerencianet.zip').
2. Acesse a administração da sua loja, acesse o link "Módulos" -> "Adicionar novo Módulo" e envie o arquivo 'gerencianet.zip' que você acabou de baixar.
3. Depois de enviar o módulo para sua loja, clica em "Instalar" e aguarde a finalização do processo.
4. Configure o módulo em "Módulos" > "Gerencianet" > "Configurar" e comece a receber pagamentos.

### Manual

1. Faça o download da [última versão](auto/) do módulo (arquivo 'gerencianet.zip') e extraia seu conteúdo ou realize o download da [última versão descompactada](manual/).
2. Acesse a loja via FTP e envie toda a pasta 'gerencianet' descompactada para a pasta de destino 'modules' que se encontra na raiz dos arquivos da loja no servidor.
3. Acesse a administração da loja e acesse o link "Módulos". Procure pelo módulo da Gerencianet na lista, clique em "Instalar" e aguarde a finalização do processo.
4. Configure o módulo em "Módulos" > "Gerencianet" > "Configurar" e comece a receber pagamentos.


## Configuração

1. Instale o plugin.
2. Na área de administração da loja, acesse "Modúlos" > "Gerencianet" > "Configurar".
3. Na aba "Configurações Gerais", você poderá configurar os seguintes itens:
4. "Modo": Configure se deseja ativar o módulo em Desenvolvimento (ambiente de testes) ou Produção (cobrança real).
5. "Opções de Pagamento": Configure as opções de pagamento que deseja receber: Boleto e/ou Cartão de Crédito.
6. "Dias para vencimento do Boleto": Configure o número de dias corridos para vencimento do Boleto.
7. "Desconto para pagamento no boleto(%)": Defina se deseja aplicar desconto para pagamentos com Boleto.
8. "Atualizar status dos pedidos PrestaShop automaticamente": Configure se deseja que o módulo atualize os status dos pedidos da loja automaticamente de acordo com as notificações da Gerencianet.
9. "Status": Configure se o Módulo estará disponível para os clientes.
10. Na aba "Credenciais", você deverá inserir as credenciais de Aplicação Gerencianet. Para ter acesso à essas credenciais, você deverá criar uma nova Aplicação Gerencianet ou utilizar uma já existe. Para criar uma Aplicação, entre em sua conta Gerencianet, acesse o menu "API" e clique em "Minhas Aplicações" -> "Nova aplicação". Escolha um nome e crie a Nova Aplicação. Agora já já terá acesso às credenciais da Aplicação. Copie-as e insira nos respectivos campos da aba "Credenciais" em sua loja (Client ID e Client Secret de produção e desenvolvimento).
11. Insira o Payee Code (identificador da conta) de sua conta Gerencianet.
12. Clique em "Salvar" e agora sua loja virtual já está pronta para receber pagamentos pela Gerencianet.

Recomendamos que antes de disponibilizar pagamentos pela Gerencianet, o lojista realize testes de cobrança com o sandbox(ambiente de testes) ativado para verificar se o procedimento de pagamento está acontecendo conforme esperado.


## Requisitos

* Versão mínima do PHP: 5.4.0
* Versão mínima do PrestaShop: 1.6.x
