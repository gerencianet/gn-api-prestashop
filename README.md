# Módulo de Integração Gerencianet para PrestaShop - Versão 1.0.0 #


O módulo Gerencianet para PrestaShop permite receber pagamentos por meio do checkout transparente da nossa API. Compatível com a versão 1.7.x do Prestashop.

Este é o Módulo de integração fornecido pela [Gerencianet](https://gerencianet.com.br/) para PrestaShop. Com ele, o proprietário da loja pode optar por receber pagamentos por boleto bancário, cartão de crédito e/ou pix. Todo processo é realizado por meio do checkout transparente. Com isso, o comprador não precisa sair do site da loja para efetuar o pagamento.

Caso você tenha alguma dúvida ou sugestão, entre em contato conosco pelo site [Gerencianet](https://gerencianet.com.br/).

## Instalação

### Automática

1. Faça o download da [última versão](https://github.com/gerencianet/gn-api-prestashop/tree/master/auto/GerencianetPrestashop.zip) do módulo .
2.	Acesse a administração da sua loja, em Módulos > Gerenciador de Módulos > Enviar um módulo e envie o arquivo "GerencianetPrestashop.zip" que você acabou de baixar;
3.	Depois de enviar o módulo para sua loja, clique em "Configurar" e aguarde a finalização do processo. Automaticamente o lojista será redirecionado para a tela de configuração.
4.	Ou então, o lojista deverá acessar a interface administrativa da loja virtual e, no menu principal, acessar a opção Módulos > Gerenciador de Módulos. Procure pelo módulo da Gerencianet na lista que será exibida e depois clique em Configurar



## Configuração

1.	Instale o plugin.
2.	Na área de administração da loja, acesse "Módulos" > "Gerenciador de Módulos". Procure pelo módulo da Gerencianet na lista que será exibida e depois clique em Configurar.
3.	Na seção "Credenciais", você deverá inserir as credenciais de Aplicação Gerencianet. Para ter acesso à essas credenciais, você deverá criar uma nova Aplicação Gerencianet ou utilizar uma já existe. Para criar uma Aplicação, entre em sua conta Gerencianet, acesse o menu "API" e clique em "Minhas Aplicações" -> "Nova aplicação". Escolha um nome e crie a Nova Aplicação. Agora já já terá acesso às credenciais da Aplicação. Copie-as e insira nos respectivos campos da aba "Credenciais" em sua loja (Client_id e Client_secret de produção e desenvolvimento).
4.	Insira o Código Identificador de sua conta Gerencianet. Para encontrar o Identificador da conta, entre em sua conta Gerencianet, acesse o menu "API" e clique em "Identificador de Conta".
5.	Selecione o ambiente de emissão: Produção ou Homologação
6.	Configure as opções de pagamento que deseja receber: Boleto, Cartão de crédito e/ou Pix
Ao clicar em Salvar, será possível configurar as formas de pagamento selecionadas. Então, surgirão as seguintes seções:
7.	Configuração do Boleto, que você poderá configurar as seguintes propriedades:

    7.1.	**Número de dias**: Configure o número de dias corridos para vencimento do Boleto.

    7.2.	**Cancelar Boletos não pagos?**: Quando habilitado, cancela todos os Boletos que não foram pagos. Impedindo que o cliente pague o Boleto após a data de vencimento.

    7.3.	**Ativar desconto?**: Quando habilitado, será aplicado desconto para pagamentos com Boleto.

    7.4.	**Percentual de desconto do boleto**: Defina o percentual de desconto para pagamentos com Boleto.

    7.5.	**Percentual de multa**: Defina se deseja aplicar multa em caso de atraso no pagamento do boleto

    7.6.	**Percentual de juros**: Defina se deseja aplicar juros em caso de atraso no pagamento do boleto.

    7.7.	**Enviar boleto por e-mail?**: Quando habilitado, o boleto será enviado por e-mail ao cliente.
8.	Configuração do Pix, que você poderá configurar as seguintes propriedades:

    8.1.	**Chave Pix**: Insira sua Chave Pix cadastrada em sua conta Gerencianet. 

    8.2.	**Certificado Pix**: Insira seu certificado (arquivo .p12 ou .pem). 

    8.3.	**Ativar desconto?**: Quando habilitado, será aplicado desconto para pagamentos com Pix.

    8.4.	**Percentual de desconto do Pix**: Defina o percentual de desconto para pagamentos com Pix.

    8.5.	**Tempo de vencimento em horas**: Defina o tempo, em horas, para o vencimento do pix após a emissão.

    8.6.	**Validar mTLS**: Marque o campo "Validar mTLS" caso deseje utilizar a validação mTLS em seu servidor.

9.	Salve suas configurações e agora sua loja virtual já está pronta para receber pagamentos pela Gerencianet.
Recomendamos que antes de disponibilizar pagamentos pela Gerencianet, o lojista realize testes de cobrança com o sandbox(ambiente de testes) ativado para verificar se o procedimento de pagamento está acontecendo conforme esperado.



## Requisitos

* Versão mínima do PHP: 7.x
* Versão mínima do PrestaShop: 1.7.x
