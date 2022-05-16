const WORDSIZE = 20;
const card = {
    number: '',
    brand: '',
};

var buttonConfirmation = null;
var gn = null;
var id_charge = 0;
var installmentFinal = 0;



/*  Masks   */
$(document).ready(function ($) {
    try {
        $('#inputNumberCardGn').mask('0000 0000 0000 0000');
    } catch (error) {
        executeAlert("Houve um erro no processamento e a pagina vai ser atualizada!");
        setTimeout(function () {
            location.reload();
        }, 3000);
    }
    if ($('#pills-tab').children().length == 1) {
        let mode = $($('#pills-tab').children()[ 0 ]);
        $('#payment-mode').val(mode.attr('name'));
    }

    buttonConfirmation = $($($("#payment-confirmation").children()[ 0 ]).children()[ 0 ]);


    buttonConfirmation.click(e => {
        e.stopPropagation();

        Swal.fire({
            title: 'Processando...',
            html: `
            <br/>
            <br/>
            <div class="spinner-border text-warning" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <br/>
            <br/>
            <br/>
            `,
            icon: 'info',
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            closeOnClickOutside: false
        });


        buttonConfirmation.attr('disabled', true);
        let formGn = $('#payment-form-gn').parent().attr('id');
        let posicao = formGn.split('-')[ 4 ];
        let radioCheck = $(`#payment-option-${posicao}`);



        if (!radioCheck.prop('checked')) {
            let i = 1;
            while (true && i < 50 && !$(`#payment-option-${i}`).prop('checked')) {
                i++;
            }

            if (i == 50) {
                executeAlert('Selecione uma forma de pagamento');
                buttonConfirmation.attr('disabled', false);
                return;
            }

            let divForm = $(`#pay-with-payment-option-${i}-form`);

            $(divForm.children()[ 0 ]).submit();

        } else {
            let modePayment = $('#payment-mode').val();
            let cpfCnpj;
            switch (modePayment) {
                case 'boleto':
                    cpfCnpj = $('#inputCPF_CNPJBOLETO').val();
                    if (validateCPF(cpfCnpj) || validateCNPJ(cpfCnpj)) {
                        if (id_charge !== 0) {
                            payBilletCharge();
                        } else {
                            createCharge('billet');
                        }
                    } else {
                        executeAlert('CPF/CNPJ inválido');
                    }

                    break;
                case 'cartao':
                    cpfCnpj = $('#inputCPF_CNPJCartao').val();

                    if (validateCPF(cpfCnpj) || validateCNPJ(cpfCnpj)) {
                        if (card.number.length == 0 || $(`#inputNumberCardGn`).val().length == 0 || $(`#inputNameCardGn`).val().length == 0 || $(`#inputCVVCardGn`).val().length == 0 || $(`#inputDateCardGn`).val().length == 0 || $(`#cepCobrança`).val().length == 0 || $(`#logradouroCep`).val().length == 0 || $(`#numberCobrança`).val().length == 0 || $(`#bairroCobrança`).val().length == 0 || $(`#cidadeCobrança`).val().length == 0 || validarSelect()) {
                            executeAlert('Preencha todos os campos');
                            buttonConfirmation.attr('disabled', false);
                            return;
                        }
                        if (installmentFinal == 0) {
                            executeAlert('Selecione um parcelamento');
                            buttonConfirmation.attr('disabled', false);
                            return;
                        }
                        if (id_charge !== 0) {
                            payCardCharge();
                        } else {
                            createCharge('card');
                        }
                    } else {
                        executeAlert('CPF/CNPJ inválido');
                    }



                    break
                case 'pix':
                    cpfCnpj = $('#inputCPF_CNPJPix').val();
                    if (validateCPF(cpfCnpj) || validateCNPJ(cpfCnpj)) {
                        payPixCharge();
                    }
                    break;
                default:
                    if (modePayment == '') {
                        executeAlert('Selecione um método de pagamento');
                    } else {
                        executeAlert('Para pagamentos com valores abaixo de R$5,00 é permitido somente via PIX, porém, o PIX está desativado no momento.');
                    }

                    buttonConfirmation.attr('disabled', false);
                    break;
            }

        }


    });



    $('#inputDateCardGn').mask('MA/2SZZ', {
        translation: {
            M: {
                pattern: /[0-1]/,
                optional: false,
            },
            S: {
                pattern: /[0-0]/,
                optional: false,
            },
            A: {
                pattern: /[0-9]/,
                optional: false,
            },
            Z: {
                pattern: /[0-9]/,
                optional: false,
            },
        },
    });

    $('#inputCPF_CNPJPix').keyup(event => {

        let cpfCnpj = $('#inputCPF_CNPJPix').val();
        let cpfCnpjLength = cpfCnpj.length;

        if (cpfCnpjLength < 15) {
            $('#inputCPF_CNPJPix').mask('000.000.000-00#');
        } else {
            $('#inputCPF_CNPJPix').mask('00.000.000/0000-00');
        }
    })

    $('#inputCPF_CNPJBOLETO').keyup(event => {

        let cpfCnpj = $('#inputCPF_CNPJBOLETO').val();
        let cpfCnpjLength = cpfCnpj.length;

        if (cpfCnpjLength < 15) {
            $('#inputCPF_CNPJBOLETO').mask('000.000.000-00#');
        } else {
            $('#inputCPF_CNPJBOLETO').mask('00.000.000/0000-00');
        }
    })

    $('#inputCPF_CNPJCartao').keyup(event => {

        let cpfCnpj = $('#inputCPF_CNPJCartao').val();
        let cpfCnpjLength = cpfCnpj.length;

        if (cpfCnpjLength < 15) {
            $('#inputCPF_CNPJCartao').mask('000.000.000-00#');
        } else {
            $('#inputCPF_CNPJCartao').mask('00.000.000/0000-00');
        }
    })

    $('#cepCobrança').keyup(event => {
        $('#cepCobrança').mask('00000-000');

    });
    $('#cepCobrança').mask('00000-000');


    $('#checkEnderenco').change(function () {
        if (this.checked) {
            $('#cepCobrança').val(address.postcode);
            $('#logradouroCep').val(address.address1);
            $('#cidadeCobrança').val(address.city);
            $('#complementoCobrança').val(address.address2);
        }
    });


    $('#inputNameCardGn').attr('maxlength', WORDSIZE);
    $('#inputCVVCardGn').mask('0000');
    $gn.ready(function (checkout) {

        $('#visaBrand').click(event => {

            if (card.brand !== undefined) {
                $($(`#${card.brand}Brand`).parent()[ 0 ]).removeClass("bg-warning").addClass("bg-light");
            }
            $($('#visaBrand').parent()[ 0 ]).removeClass(" bg-light").addClass("bg-warning");
            changeBrandCard('visa');
            card.brand = 'visa';
            insertInstallments();
            checkNumberForBrand()
        });

        $('#mastercardBrand').click(event => {

            if (card.brand !== undefined) {
                $($(`#${card.brand}Brand`).parent()[ 0 ]).removeClass("bg-warning").addClass("bg-light");
            }
            $($('#mastercardBrand').parent()[ 0 ]).removeClass(" bg-light").addClass("bg-warning");
            changeBrandCard('mastercard');
            card.brand = 'mastercard';
            insertInstallments();
            checkNumberForBrand()
        });

        $('#amexBrand').click(event => {

            if (card.brand !== undefined) {
                $($(`#${card.brand}Brand`).parent()[ 0 ]).removeClass("bg-warning").addClass("bg-light");
            }
            $($('#amexBrand').parent()[ 0 ]).removeClass(" bg-light").addClass("bg-warning");
            changeBrandCard('amex');
            card.brand = 'amex';
            insertInstallments();
            checkNumberForBrand()
        });

        $('#eloBrand').click(event => {

            if (card.brand !== undefined) {
                $($(`#${card.brand}Brand`).parent()[ 0 ]).removeClass("bg-warning").addClass("bg-light");
            }
            $($('#eloBrand').parent()[ 0 ]).removeClass(" bg-light").addClass("bg-warning");
            changeBrandCard('elo');
            card.brand = 'elo';
            insertInstallments();
        });

        $('#hipercardBrand').click(event => {

            if (card.brand !== undefined) {
                $($(`#${card.brand}Brand`).parent()[ 0 ]).removeClass("bg-warning").addClass("bg-light");
            }
            $($('#hipercardBrand').parent()[ 0 ]).removeClass(" bg-light").addClass("bg-warning");
            changeBrandCard('hipercard');
            card.brand = 'hipercard';
            insertInstallments();
            checkNumberForBrand()
        });
        /* Events */
        $('#inputNumberCardGn').keyup((event) => {
            changeValueNumberCard(event.target.value);
            let brandNumber = checkBrand(event.target.value);
        });

        $('#inputNameCardGn').keyup((event) => {
            changeValueNameCard(event.target.value);
        });

        $('#inputDateCardGn').keyup((event) => {
            changeValueDateCard(event.target.value);
        });

        $('#inputCVVCardGn').keyup((event) => {
            changeValueCVVCard(event.target.value);
        });

        $('.card_front').click((event) => {
            $('.card-gn').removeClass('girar');
            $('.card-gn').addClass('girar');
        });

        $('.card_back').click((event) => {
            $('.card-gn').addClass('girar');
            $('.card-gn').removeClass('girar');
        });

        $('#inputCVVCardGn').focusin((event) => {
            $('.card-gn').removeClass('girar');
            $('.card-gn').addClass('girar');
        });

        $('#inputCVVCardGn').focusout((event) => {
            $('.card-gn').addClass('girar');
            $('.card-gn').removeClass('girar');
        });


        /* Number card */
        function changeValueNumberCard(value) {
            let stringFormat = value.replace(/ /g, '');


            if (stringFormat.length == 0) {
                $('.card_number').text('**** **** **** ****');
            } else {
                $('.card_number').text(
                    stringFormat.substring(
                        stringFormat.length - 16,
                        stringFormat.length - 12
                    ) + ' ' + stringFormat.substring(
                        stringFormat.length - 12,
                        stringFormat.length - 8
                    ) + ' ' +
                    stringFormat.substring(
                        stringFormat.length - 8,
                        stringFormat.length - 4
                    ) + ' '
                    + stringFormat.substring(
                        stringFormat.length - 4,
                        stringFormat.length
                    )
                );
            }
            card.number = stringFormat;
        }

        function changeBrandCard(value) {
            $('.brandCard').attr(
                'src',
                `/modules/GerencianetPrestashop/views/img/${value}.svg`
            );
        }

        function checkBrand(dirtynumber) {
            var cardnumber = dirtynumber.replace(/[^0-9]+/g, '');
            cardnumber = cardnumber.replaceAll(' ', '');

            var brands = [
                {
                    reg: /^((509091)|(636368)|(636297)|(504175)|(438935)|(40117[8-9])|(45763[1-2])|(457393)|(431274)|(50990[0-2])|(5099[7-9][0-9])|(50996[4-9])|(509[1-8][0-9][0-9])|(5090(0[0-2]|0[4-9]|1[2-9]|[24589][0-9]|3[1-9]|6[0-46-9]|7[0-24-9]))|(5067(0[0-24-8]|1[0-24-9]|2[014-9]|3[0-379]|4[0-9]|5[0-3]|6[0-5]|7[0-8]))|(6504(0[5-9]|1[0-9]|2[0-9]|3[0-9]))|(6504(8[5-9]|9[0-9])|6505(0[0-9]|1[0-9]|2[0-9]|3[0-8]))|(6505(4[1-9]|5[0-9]|6[0-9]|7[0-9]|8[0-9]|9[0-8]))|(6507(0[0-9]|1[0-8]))|(65072[0-7])|(6509(0[1-9]|1[0-9]|20))|(6516(5[2-9]|6[0-9]|7[0-9]))|(6550(0[0-9]|1[0-9]))|(6550(2[1-9]|3[0-9]|4[0-9]|5[0-8])))/,
                    name: 'elo',
                },
                {
                    reg: /^4[0-9]{12}(?:[0-9]{3})/,
                    name: 'visa',
                },
                {
                    reg: /^5[1-5][0-9]{14}/,
                    name: 'mastercard',
                },
                {
                    reg: /^3(?:0[0-5]|[68][0-9])[0-9]{11}/,
                    name: 'diners',
                },
                {
                    reg: /^3[47][0-9]{13}/,
                    name: 'amex',
                },
                {
                    reg: /^(606282\d{10}(\d{3})?)|(3841\d{15})/,
                    name: 'hipercard',
                },
            ];

            let test = brands.find((brand) => brand.reg.test(cardnumber));
            let final = test == undefined ? 'other' : test.name;

            return final;
        }

        /* Namecard */
        function changeValueNameCard(value) {
            value.length < WORDSIZE
                ? $('.card_name').text(value)
                : $('.card_name').text(value.substring(0, WORDSIZE));
            let sign = document.querySelector('.card_secret');
            sign.setAttribute('data-conteudo', value.substring(0, WORDSIZE));
        }

        /* Date expirate    */
        function changeValueDateCard(value) {
            let date = value.substring(0, 3) + value.substring(5, value.length);
            value < 3 ? $('.card_date').text(value) : $('.card_date').text(date);
            value.length == 0
                ? $('.card_date').text('01/22')
                : $('.card_date').text(date);
        }
        /* cvv    */
        function changeValueCVVCard(value) {
            if (value.length == 0) {
                $('.card_secret_last').text('***');
            } else {
                $('.card_secret_last').text(value);
            }
        }

        /* Utils   */
        $('.dropdown-el').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).toggleClass('expanded');
            installmentFinal = $(e.target).attr('for').split('_')[ 0 ];

            $('#' + $(e.target).attr('for')).prop('checked', true);
        });
        $(document).click(function () {
            $('.dropdown-el').removeClass('expanded');
        });


        gn = checkout;


        function checkNumberForBrand() {
            let numberCard = $('#inputNumberCardGn').val();
            if (numberCard.length > 0) {
                let brandNumber = checkBrand(numberCard);
                if (brandNumber !== card.brand && card.brand !== 'other' && card.brand !== 'elo') {
                    executeAlert(`Aparentemente o número do cartão não corresponde à bandeira...`);
                }
            }
        }

        $('#inputNumberCardGn').focusout((event) => {
            checkNumberForBrand();
        });

        function insertInstallments() {

            let value = parseInt($('#valueTotal').text() * 100);

            checkout.getInstallments(
                value, // valor total da cobrança
                card.brand, // bandeira do cartão
                function (error, response) {
                    if (error) {
                        // Trata o erro ocorrido
                        clearSelect();

                        $('<input>', {
                            class: 'GN-inputInstallments',
                            type: 'radio',
                            name: 'installments',
                            value: 'Installments',
                            checked: 'checked',
                            id: 'calculando-parcelas',
                        }).appendTo('.dropdown-el');

                        $('<label>', {
                            class: 'GN-labelInstallments',
                            for: 'calculando-parcelas',
                            text:
                                'Error calcular parcelas: ' +
                                error.error_description,
                        }).appendTo('.dropdown-el');
                    } else {
                        // Trata a respostae
                        clearSelect();
                        let installments = response.data.installments;

                        installments.forEach((installment, index) => {
                            index == 0
                                ? insertValueTotal(
                                    installment.installment,
                                    installment.value
                                )
                                : '--,--';

                            $('<input>', {
                                class: 'GN-inputInstallments',
                                type: 'radio',
                                name: 'installments',
                                value: installment.installment,
                                id:
                                    installment.installment +
                                    '_' +
                                    installment.value,
                                checked: index == 0 ? true : false,
                            }).appendTo('.dropdown-el');

                            let dif =
                                (installment.value / 100) *
                                installment.installment -
                                value / 100;
                            let juros = installment.interest_percentage / 100;
                            $('<label>', {
                                class: 'GN-labelInstallments',
                                click: function () {
                                    insertValueTotal(
                                        installment.installment,
                                        installment.value
                                    );
                                },
                                for:
                                    installment.installment +
                                    '_' +
                                    installment.value,
                                text: `${installment.installment}x de ${installment.currency
                                    } - ${juros} % de juros - Acréscimo de R$ ${juros === 0 ? '0.00' : dif.toFixed(2)}`,
                            }).appendTo('.dropdown-el');
                        });
                        installmentFinal = 1;
                    }
                    return response;
                }
            );
        }

        function failInstallments() {
            clearSelect();
            installmentFinal = 0;
            $('<input>', {
                class: 'GN-inputInstallments',
                type: 'radio',
                name: 'installments',
                value: 'Installments',
                checked: 'checked',
                id: 'calculando-parcelas',
            }).appendTo('.dropdown-el');

            $('<label>', {
                class: 'GN-labelInstallments',
                for: 'calculando-parcelas',
                text: 'Favor inserir os dados do cartão para calcular o valor das parcelas',
            }).appendTo('.dropdown-el');

            $('.totalCartao').text('--,--');
        }
    });

    function validarSelect() {
        if ($('#estadoCobrança').val() == " ") {
            return true;
        }
        return false;

    }

    /* Functions   */



    function clearSelect() {
        $('.dropdown-el').empty();
    }

    function insertValueTotal(installment, value) {
        $('.totalCartao').text(
            `${installment}x de R$ ${(value / 100).toFixed(2)} - total: R$ ${(
                (value * installment) /
                100
            ).toFixed(2)}`
        );
    }




    function executeAlert(message) {
        Swal.fire({
            title: 'Oops...',
            text: message,
            icon: 'error',
            confirmButtonText: 'OK',
        });
    }


    function validateCPF(cpf) {

        if (cpf.length == 0) {
            executeAlert('Por favor, informe o CPF');
            return false;
        }
        cpf = cpf.replace(/[^\d]+/g, '');

        if (cpf.length != 11) {

            return false;
        } else if (
            cpf == '00000000000' ||
            cpf == '11111111111' ||
            cpf == '22222222222' ||
            cpf == '33333333333' ||
            cpf == '44444444444' ||
            cpf == '55555555555' ||
            cpf == '66666666666' ||
            cpf == '77777777777' ||
            cpf == '88888888888' ||
            cpf == '99999999999') {

            return false;
        } else {

            for (t = 9; t < 11; t++) {
                i = 0;
                for (d = 0; d < t; d++) i += cpf.charAt(d) * (t + 1 - d);
                r = 11 - (i % 11);
                if (r > 9) r = 0;
                if (cpf.charAt(t) != r) {

                    return false;
                }
            }
        }

        return true;
    }

    function validateCNPJ(cnpj) {
        if (cnpj.length == 0) {
            executeAlert('Por favor, informe o CNPJ');
            return false;
        }
        cnpj = cnpj.replace(/[^\d]+/g, '');

        if (cnpj.length != 14) {

            return false;
        }

        for (i = 0, j = 5, soma = 0; i < 12; i++) {
            soma += cnpj.charAt(i) * j;
            j = (j == 2) ? 9 : j - 1;
        }

        resto = soma % 11;

        if (resto < 2)
            resto = 0;
        else
            resto = 11 - resto;

        if (cnpj.charAt(12) != resto) {

            return false;
        }

        for (i = 0, j = 6, soma = 0; i < 13; i++) {
            soma += cnpj.charAt(i) * j;
            j = (j == 2) ? 9 : j - 1;
        }

        resto = soma % 11;

        if (resto < 2)
            resto = 0;
        else
            resto = 11 - resto;

        if (cnpj.charAt(13) != resto) {

            return false;
        }

        return true;

    }



    function createCharge(paymentType) {
        let order_id = $('#order_id').text();
        let data = {
            action: "create_charge",
            order_id: order_id
        };

        $.ajax({
            type: "POST",
            url: home_url + "ajax-request.php",
            data: data,
            success: function (response) {
                let obj = $.parseJSON(response);
                if (obj.code == 200) {
                    id_charge = obj.data.charge_id;
                    $('#gn_charge_id').val(id_charge);
                    if (paymentType == 'billet') {
                        payBilletCharge();
                    } else {
                        payCardCharge();
                    }
                } else {
                    executeAlert('Ocorreu um erro ao tentar gerar a cobrança: ' + obj.message)
                }
            },
            error: function (error) {

                buttonConfirmation.attr('disabled', false);
                executeAlert('Ocorreu um erro ao tentar gerar a cobrança');
            }
        });

    }


    function payBilletCharge() {
        let juridical;
        if (validateCNPJ($('#inputCPF_CNPJBOLETO').val())) {
            juridical = "1";
        } else {
            juridical = "0";
        }

        let order_id = $('#order_id').text();

        let data = {
            action: "pay_billet",
            charge_id: id_charge,
            order_id: order_id,
            name: name_client,
            cpf: $('#inputCPF_CNPJBOLETO').val().replace(/[^\d]+/g, ''),
            cnpj: $('#inputCPF_CNPJBOLETO').val().replace(/[^\d]+/g, ''),
            corporate_name: name_client,
            pay_billet_with_cnpj: juridical
        };

        $.ajax({
            type: "POST",
            url: home_url + "ajax-request.php",
            data: data,
            success: function (response) {

                let obj = $.parseJSON(response);

                if (obj.code == 200) {
                    $('#payment-form-gn').submit();
                } else {
                    executeAlert('Ocorreu um erro ao tentar gerar a cobrança: ' + obj.message)
                    buttonConfirmation.attr('disabled', false);
                }

                buttonConfirmation.attr('disabled', false);
            },
            error: function (error) {
                buttonConfirmation.attr('disabled', false);
                executeAlert('Ocorreu um erro ao tentar gerar a cobrança: ' + error.responseText);
            }
        });


    }


    function payCardCharge() {
        let callback = function (error, response) {

            if (error) {
                executeAlert('Ocorreu um erro ao tentar gerar a cobrança: ' + error.error_description);
                buttonConfirmation.attr('disabled', false);
                return;
            }

            let cpfCnpj = $('#inputCPF_CNPJCartao').val().replace(/[^\d]+/g, '')
            let juridical;
            if (validateCNPJ(cpfCnpj)) {
                juridical = "1";
            } else {
                juridical = "0";
            }
            let order_id = $('#order_id').text();

            let data = {
                action: "pay_card",
                charge_id: id_charge,
                order_id: order_id,
                payment_token: response.data.payment_token,
                pay_card_with_cnpj: juridical,
                cpf: cpfCnpj,
                cnpj: cpfCnpj,
                installments: installmentFinal,
                cep: $('#cepCobrança').val().replace(/[^\d]+/g, ''),
                rua: $('#logradouroCep').val(),
                numero: $('#numberCobrança').val(),
                complemento: $('#complementoCobrança').val(),
                bairro: $('#bairroCobrança').val(),
                cidade: $('#cidadeCobrança').val(),
                estado: $('#estadoCobrança').val(),
            }


            $.ajax({
                type: "POST",
                url: home_url + "ajax-request.php",
                data: data,
                success: function (response) {
                    let obj = $.parseJSON(response);

                    if (obj.code == 200) {
                        $('#payment-form-gn').submit();
                    } else {
                        executeAlert('Ocorreu um erro ao tentar gerar a cobrança: ' + obj.message)
                        buttonConfirmation.attr('disabled', false);
                    }

                    buttonConfirmation.attr('disabled', false);
                },
                error: function (error) {
                    executeAlert('Ocorreu um erro ao tentar gerar a cobrança');
                    buttonConfirmation.attr('disabled', false);
                }
            });
        }


        gn.getPaymentToken({
            brand: card.brand, // bandeira do cartão
            number: card.number, // número do cartão
            cvv: $('#inputCVVCardGn').val(), // código de segurança
            expiration_month: $('#inputDateCardGn').val().split("/")[ 0 ], // mês de vencimento
            expiration_year: $('#inputDateCardGn').val().split("/")[ 1 ] // ano de vencimento
        }, callback);
    }

    function payPixCharge() {
        let juridical;
        if (validateCNPJ($('#inputCPF_CNPJPix').val())) {
            juridical = "1";
        } else {
            juridical = "0";
        }

        let order_id = $('#order_id').text();

        let data = {
            action: "pay_pix",
            charge_id: id_charge,
            order_id: order_id,
            pay_card_with_cnpj: juridical,
            cpf: $('#inputCPF_CNPJPix').val().replace(/[^\d]+/g, ''),
            cnpj: $('#inputCPF_CNPJPix').val().replace(/[^\d]+/g, ''),
        }


        $.ajax({
            type: "POST",
            url: home_url + "ajax-request.php",
            data: data,
            success: function (response) {

                let obj = $.parseJSON(response);

                id_charge = obj.txid;
                $('#gn_charge_id').val(id_charge);


                if (obj.txid != undefined) {
                    $('#payment-form-gn').submit();
                } else {
                    executeAlert('Ocorreu um erro ao tentar gerar a cobrança: ' + obj.message)
                    buttonConfirmation.attr('disabled', false);
                }

                buttonConfirmation.attr('disabled', false);
            },
            error: function (error) {
                buttonConfirmation.attr('disabled', false);
                executeAlert('Ocorreu um erro ao tentar gerar a cobrança: ' + error.responseText);
            }
        });
    }

});

function changeModePayment(mode) {
    $('#payment-mode').val(mode);
}

