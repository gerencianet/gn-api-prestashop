var errorMessage;
var id_charge = 0;
var active = 0;

jQuery(document).ready(function ($) {

    $('.cpf-mask').mask('000.000.000-00', {
        onComplete: function (val, e, field, options) {
            if (!verifyCPF(val)) {
                showError('CPF inválido. Digite novamente.');
            } else {
                hideError();
            }
        },
        placeholder: "___.___.___-__"
    });

    $('.cnpj-mask').mask('00.000.000/0000-00', {
        onComplete: function (val, e, field, options) {
            if (!verifyCNPJ(val)) {
                showError('CNPJ inválido. Digite novamente.');
            } else {
                hideError();
            }
        },
        placeholder: "__.___.___/____-__"
    });

    $('.phone-mask').mask('(00) 90000-0000', {
        onComplete: function (val, e, field, options) {
            if (!verifyPhone(val)) {
                showError('Telefone inválido. Digite novamente.');
            } else {
                hideError();
            }
        },
        placeholder: "(__) _____-____"
    });

    $('.birth-mask').mask('00/00/0000', {
        onComplete: function (val, e, field, options) {
            if (!verifyBirthDate(val)) {
                showError('Data de nascimento inválida. Digite novamente.');
            } else {
                hideError();
            }
        },
        placeholder: "__/__/____"
    });

    jQuery('#input-payment-card-number').mask('0000000000000000000');
    jQuery('#input-payment-card-cvv').mask('00000');

    jQuery('#background-card').click(function (e) {
        if (active != 2) {
            jQuery('#collapse-payment-card').slideDown();
            jQuery('#collapse-payment-billet').slideUp();
            jQuery('#paymentMethodCardRadio').prop('checked', true);
            jQuery('#paymentMethodBilletRadio').prop('checked', false);
            jQuery('#uniform-paymentMethodCardRadio span').addClass('checked');
            jQuery('#uniform-paymentMethodBilletRadio span').removeClass('checked');
            jQuery("#background-card").css("background-color", "#f5f5f5");
            jQuery("#background-billet").css("background-color", "#ffffff");
            jQuery('#price-billet').hide();
            jQuery('#price-card').show();
            jQuery('#price-no-payment-selected').hide();
            active = 2;
        } else {
            jQuery('#collapse-payment-card').slideUp();
            jQuery('#paymentMethodCardRadio').prop('checked', false);
            jQuery('#paymentMethodBilletRadio').prop('checked', false);
            jQuery('#uniform-paymentMethodCardRadio span').removeClass('checked');
            jQuery('#uniform-paymentMethodBilletRadio span').removeClass('checked');
            jQuery("#background-card").css("background-color", "#ffffff");
            jQuery("#background-billet").css("background-color", "#ffffff");
            jQuery('#price-card').hide();
            jQuery('#price-no-payment-selected').show();
            active = 0;
        }
    });

    jQuery('#background-billet').click(function (e) {
        if (active != 1) {
            jQuery('#collapse-payment-billet').slideDown();
            jQuery('#collapse-payment-card').slideUp();
            jQuery('#paymentMethodCardRadio').prop('checked', false);
            jQuery('#paymentMethodBilletRadio').prop('checked', true);
            jQuery('#uniform-paymentMethodCardRadio span').removeClass('checked');
            jQuery('#uniform-paymentMethodBilletRadio span').addClass('checked');
            jQuery("#background-card").css("background-color", "#ffffff");
            jQuery("#background-billet").css("background-color", "#f5f5f5");
            jQuery('#price-billet').show();
            jQuery('#price-card').hide();
            jQuery('#price-no-payment-selected').hide();
            active = 1;
        } else {
            jQuery('#collapse-payment-billet').slideUp();
            jQuery('#paymentMethodCardRadio').prop('checked', false);
            jQuery('#paymentMethodBilletRadio').prop('checked', false);
            jQuery('#uniform-paymentMethodCardRadio span').removeClass('checked');
            jQuery('#uniform-paymentMethodBilletRadio span').removeClass('checked');
            jQuery("#background-card").css("background-color", "#ffffff");
            jQuery("#background-billet").css("background-color", "#ffffff");
            jQuery('#price-billet').hide();
            jQuery('#price-no-payment-selected').show();
            active = 0;
        }
    });


    $("input[name='input-payment-card-brand']").click(function () {

        jQuery("#brand-visa").removeClass("gn-card-brand-checked");
        jQuery("#brand-mastercard").removeClass("gn-card-brand-checked");
        jQuery("#brand-amex").removeClass("gn-card-brand-checked");
        jQuery("#brand-diners").removeClass("gn-card-brand-checked");
        jQuery("#brand-elo").removeClass("gn-card-brand-checked");
        jQuery("#brand-hipercard").removeClass("gn-card-brand-checked");

        jQuery("#brand-" + this.value).addClass("gn-card-brand-checked");
    });


    function justBillet() {
        jQuery('#collapse-payment-billet').slideDown();
        jQuery('#collapse-payment-card').slideUp();
        jQuery('#paymentMethodCardRadio').prop('checked', false);
        jQuery('#paymentMethodBilletRadio').prop('checked', true);
        jQuery("#background-card").css("background-color", "#ffffff");
        jQuery("#background-billet").css("background-color", "#f5f5f5");
        jQuery('#price-billet').show();
        jQuery('#price-card').hide();
        jQuery('#price-no-payment-selected').hide();
        active = 1;
    }

    function justCard() {
        jQuery('#collapse-payment-card').slideDown();
        jQuery('#collapse-payment-billet').slideUp();
        jQuery('#paymentMethodCardRadio').prop('checked', true);
        jQuery('#paymentMethodBilletRadio').prop('checked', false);
        jQuery("#background-card").css("background-color", "#f5f5f5");
        jQuery("#background-billet").css("background-color", "#ffffff");
        jQuery('#price-billet').hide();
        jQuery('#price-card').show();
        jQuery('#price-no-payment-selected').hide();
        active = 2;
    }

    $('#pay_billet_with_cnpj').click(function () {
        if ($(this).is(':checked')) {
            $('#pay_cnpj').slideDown();
        } else {
            $('#pay_cnpj').slideUp();
        }
    });

    $('#pay_card_with_cnpj').click(function () {
        if ($(this).is(':checked')) {
            $('#pay_cnpj_card').slideDown();
        } else {
            $('#pay_cnpj_card').slideUp();
        }
    });

    if (payCnpj) {
        $('#pay_billet_with_cnpj').prop('checked', true);
        $('#pay_card_with_cnpj').prop('checked', true);
        if (showCnpjFields) {
            $('#pay_cnpj_card').slideDown();
            $('#pay_cnpj').slideDown();
        }
    }

    $('input[type=radio][name=input-payment-card-brand]').change(function () {
        if (this.value == "visa" || this.value == "mastercard" || this.value == "amex" || this.value == "diners" || this.value == "elo" || this.value == "hipercard") {
            getInstallments(this.value);
        } else {
            getInstallments($('input[name=input-payment-card-brand]:checked', '#payment-card-form').val());
        }
    });

    $('#gn-pay-billet-button').click(function (e) {
        e.preventDefault();
        e.stopPropagation();

        $('#gn-pay-billet-button').prop("disabled", true);

        if (document.getElementById('paymentMethodBilletRadio').checked) {
            if (validateBilletFields()) {
                if (id_charge != 0) {
                    payBilletCharge();
                } else {
                    createCharge('billet');
                }
            }
        } else {
            showError("Selecione um método de pagamento.");
            $('#gn-pay-billet-button').prop("disabled", false);
        }

    });

    $('#gn-pay-card-button').click(function (e) {
        e.preventDefault();
        e.stopPropagation();

        $('#gn-pay-card-button').prop("disabled", true);
        if (document.getElementById('paymentMethodCardRadio').checked) {
            if (validateCardFields()) {
                if (id_charge != 0) {
                    payCardCharge();
                } else {
                    createCharge('card');
                }
            }
        } else {
            showError("Selecione um método de pagamento.");
            $('#gn-pay-card-button').prop("disabled", false);
        }

    });

    function createCharge(paymentType) {
        $('.gn-loading-request').fadeIn();

        var order_id = jQuery('input[name="wc_order_id"]').val(),
            data = {
                action: "create_charge",
                order_id: order_id
            };

        jQuery.ajax({
            type: "POST",
            url: home_url + "ajax-request.php",
            data: data,
            success: function (response) {
                var obj = $.parseJSON(response);
                if (obj.code == 200) {
                    id_charge = obj.data.charge_id;
                    if (paymentType == 'billet') {
                        payBilletCharge();
                    } else {
                        payCardCharge();
                    }
                } else {
                    $('#gn-pay-billet-button').prop("disabled", false);
                    $('#gn-pay-card-button').prop("disabled", false);
                    $('.gn-loading-request').fadeOut();
                    if (!$('.warning-payment').is(":visible")) {
                        $('.warning-payment').slideDown();
                        scrollToTop();
                    }
                    $('.warning-payment').html('Ocorreu um erro ao tentar gerar a cobrança: <b>' + obj.message + '</b>');
                }

            },
            error: function () {
                alert("error ocurred");
            }
        });

        //return false;
    }


    function payBilletCharge() {
        $('.gn-loading-request').fadeIn();

        var juridical;
        if ($('#pay_billet_with_cnpj').attr('checked')) {
            juridical = "1";
        } else {
            juridical = "0";
        }

        var data = {
            action: "pay_billet",
            charge_id: id_charge,
            order_id: jQuery('input[name="wc_order_id"]').val(),
            name: jQuery('#first_name').val(),
            cpf: jQuery('#cpf').val().replace(/[^\d]+/g, ''),
            phone_number: jQuery('#phone_number').val().replace(/[^\d]+/g, ''),
            cnpj: jQuery('#cnpj').val().replace(/[^\d]+/g, ''),
            corporate_name: jQuery('#corporate_name').val(),
            pay_billet_with_cnpj: juridical
        };

        jQuery.ajax({
            type: "POST",
            url: home_url + "ajax-request.php",
            data: data,
            success: function (response) {
                var obj = $.parseJSON(response);
                if (obj.code == 200) {
                    $('#gn_charge_id_billet').val(obj.data.charge_id);
                    $('#billet-form').submit();
                } else {
                    $('#gn-pay-billet-button').prop("disabled", false);
                    $('.form-group').show();
                    $('.gn-loading-request').fadeOut();
                    showError(obj.message);
                }
            },
            error: function () {
                alert("error ocurred");
            }
        });

        //return false;

    }

    function validateBilletFields() {
        errorMessage = '';
        if (!(($("#pay_billet_with_cnpj").is(':checked') && verifyCNPJ($('#cnpj').val())) || !($("#pay_billet_with_cnpj").is(':checked')))) {
            errorMessage = 'Digite o CNPJ da empresa.';
        } else if (!(($("#pay_billet_with_cnpj").is(':checked') && $('#corporate_name').val() != "") || !($("#pay_billet_with_cnpj").is(':checked')))) {
            errorMessage = 'Digite a razão social da empresa.';
        } else if (!(verifyCPF($('#cpf').val()))) {
            errorMessage = 'O CPF digitado é inválido.';
        } else if ($('#first_name').val() == "") {
            errorMessage = 'Digite o nome.';
        } else if (!(verifyPhone($('#phone_number').val()))) {
            errorMessage = 'O Telefone digitado é inválido.';
        } else if (!(verifyEmail($('#input-payment-billet-email').val()))) {
            errorMessage = 'O e-mail digitado é inválido.';
        }

        if (errorMessage != '') {
            showError(errorMessage);
            $('#gn-pay-billet-button').prop("disabled", false);
            return false;
        } else {
            return true;
        }
    }

    function validateCardFields() {
        errorMessage = '';
        if (!(($("#pay_card_with_cnpj").is(':checked') && verifyCNPJ($('#cnpj_card').val())) || !($("#pay_card_with_cnpj").is(':checked')))) {
            errorMessage = 'Digite o CNPJ da empresa.';
        } else if (!(($("#pay_card_with_cnpj").is(':checked') && $('#corporate_name_card').val() != "") || !($("#pay_card_with_cnpj").is(':checked')))) {
            errorMessage = 'Digite a razão social da empresa.';
        } else if (!(verifyCPF($('#input-payment-card-cpf').val()))) {
            errorMessage = 'O CPF digitado é inválido.';
        } else if ($('#input-payment-card-name').val() == "") {
            errorMessage = 'Digite o nome.';
        } else if (!(verifyPhone($('#input-payment-card-phone').val()))) {
            errorMessage = 'O Telefone digitado é inválido.';
        } else if (!(verifyEmail($('#input-payment-card-email').val()))) {
            errorMessage = 'O e-mail digitado é inválido.';
        } else if (!(verifyBirthDate($('#input-payment-card-birth').val()))) {
            errorMessage = 'A data de nascimento digitada é inválida.';
        } else if ($('#input-payment-card-street').val() == "") {
            errorMessage = 'Digite o endereço de cobrança.';
        } else if ($('#input-payment-card-address-number').val() == "") {
            errorMessage = 'Digite número do endereço de cobrança.';
        } else if ($('#input-payment-card-neighborhood').val() == "") {
            errorMessage = 'Digite bairro do endereço de cobrança.';
        } else if ($('#input-payment-card-zipcode').val() == "") {
            errorMessage = 'Digite CEP do endereço de cobrança.';
        } else if ($('#input-payment-card-city').val() == "") {
            errorMessage = 'Digite a cidade do endereço de cobrança.';
        } else if ($('#input-payment-card-state').val() == "") {
            errorMessage = 'Selecione o estado do endereço de cobrança.';
        } else if ($('input[name=input-payment-card-brand]:checked', '#payment-card-form').val() == "") {
            errorMessage = 'Selecione a bandeira do cartão de crédito.';
        } else if ($('#input-payment-card-installments').val() == "") {
            errorMessage = 'Selecione a quantidade de parcelas que deseja.';
        } else if ($('#input-payment-card-number').val() == "") {
            errorMessage = 'Digite o número do cartão de crédito.';
        } else if ($('#input-payment-card-cvv').val() == "") {
            errorMessage = 'Digite o código de segurança do cartão de crédito.';
        } else if ($('#input-payment-card-expiration-month').val() == "" || $('#input-payment-card-expiration-year').val() == "") {
            errorMessage = 'Digite os dados de validade do cartão de crédito.';
        }

        if (errorMessage != '') {
            showError(errorMessage);
            $('#gn-pay-card-button').prop("disabled", false);
            return false;
        } else {
            return true;
        }
    }

    function showError(message) {
        if (!$('#wc-gerencianet-messages').is(":visible")) {
            $('#wc-gerencianet-messages').slideDown();
        }
        scrollToTop();
        jQuery("#wc-gerencianet-messages").html(message)
    }

    function hideError() {
        $('#wc-gerencianet-messages').slideUp();
    }

    function payCardCharge() {

        $('.gn-loading-request').fadeIn();

        card_brand = $('input[name=input-payment-card-brand]:checked', '#payment-card-form').val()
        card_number = $("#input-payment-card-number").val();
        card_cvv = $("#input-payment-card-cvv").val();
        expiration_month = $("#input-payment-card-expiration-month").val();
        expiration_year = $("#input-payment-card-expiration-year").val();

        var callback = function (error, response) {
            if (error) {

                showError("Os dados do cartão digitados são inválidos. Tente novamente.");
                $('#gn-pay-card-button').prop("disabled", false);

                $('.gn-loading-request').fadeOut();

            } else {
                var dateBirth = $('#input-payment-card-birth').val().split("/");

                var juridical;
                if ($('#pay_card_with_cnpj').attr('checked')) {
                    juridical = "1";
                } else {
                    juridical = "0";
                }

                var data = {
                    action: "pay_card",
                    charge_id: id_charge,
                    name: jQuery('#input-payment-card-name').val(),
                    cpf: jQuery('#input-payment-card-cpf').val().replace(/[^\d]+/g, ''),
                    phone_number: jQuery('#input-payment-card-phone').val().replace(/[^\d]+/g, ''),
                    cnpj: jQuery('#cnpj_card').val().replace(/[^\d]+/g, ''),
                    corporate_name: jQuery('#corporate_name_cnpj').val(),
                    pay_card_with_cnpj: juridical,
                    payment_token: response.data.payment_token,
                    birth: dateBirth[2] + "-" + dateBirth[1] + "-" + dateBirth[0],
                    email: $('#input-payment-card-email').val(),
                    street: $('#input-payment-card-street').val(),
                    number: $('#input-payment-card-address-number').val(),
                    neighborhood: $('#input-payment-card-neighborhood').val(),
                    complement: $('#input-payment-card-complement').val(),
                    zipcode: $('#input-payment-card-zipcode').val().replace(/[^\d]+/g, ''),
                    city: $('#input-payment-card-city').val(),
                    state: $('#input-payment-card-state').val(),
                    installments: $('#input-payment-card-installments').val()
                };

                jQuery.ajax({
                    type: "POST",
                    url: home_url + "ajax-request.php",
                    data: data,
                    success: function (response) {
                        var obj = $.parseJSON(response);
                        if (obj.code == 200) {
                            $('#gn_charge_id_card').val(obj.data.charge_id);
                            $('#payment-card-form').submit();
                        } else {
                            $('#gn-pay-card-button').prop("disabled", false);
                            showError(obj.message);
                            $('.form-group').show();
                            $('.gn-loading-request').fadeOut();
                        }

                    },
                    error: function () {
                        alert("error ocurred");
                    }
                });
                //return false;
            }
        };

        getPaymentToken({
            brand: card_brand,
            number: card_number,
            cvv: card_cvv,
            expiration_month: expiration_month,
            expiration_year: expiration_year
        }, callback);
    }

    function getInstallments(card_brand) {
        $('#select-card-installments').html('<select name="input-payment-card-installments" id="input-payment-card-installments" class="form-control ' +
            'gn-form-select">' +
            '<option value="">Aguarde, carregando...</option> ' +
            '</select>').show();
        var order_id = jQuery('input[name="wc_order_id"]').val(),
            data = {
                action: "get_installments",
                order_id: order_id,
                brand: card_brand
            };

        jQuery.ajax({
            type: "POST",
            url: home_url + "ajax-request.php",
            data: data,
            success: function (response) {
                var obj = $.parseJSON(response);
                if (obj.code == 200) {
                    var options = '';
                    for (var i = 0; i < obj.data.installments.length; i++) {
                        options += '<option value="' + obj.data.installments[i].installment + '">' + obj.data.installments[i].installment + 'x de R$' + obj.data.installments[i].currency + '</option>';
                    }
                    $('#select-card-installments').html('<select name="input-payment-card-installments" id="input-payment-card-installments" class="form-control ' +
                        'gn-form-select">' + options + '</select>').show();
                }
            },
            error: function () {
                alert("error ocurred");
            }
        });

        //return false;
    }


    function verifyCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g, '');

        if (cpf == '' || cpf.length != 11) return false;

        var resto;
        var soma = 0;

        if (cpf == "00000000000" || cpf == "11111111111" || cpf == "22222222222" || cpf == "33333333333" || cpf == "44444444444" || cpf == "55555555555" || cpf == "66666666666" || cpf == "77777777777" || cpf == "88888888888" || cpf == "99999999999" || cpf == "12345678909") return false;

        for (i = 1; i <= 9; i++) soma = soma + parseInt(cpf.substring(i - 1, i)) * (11 - i);
        resto = (soma * 10) % 11;

        if ((resto == 10) || (resto == 11)) resto = 0;
        if (resto != parseInt(cpf.substring(9, 10))) return false;

        soma = 0;
        for (i = 1; i <= 10; i++) soma = soma + parseInt(cpf.substring(i - 1, i)) * (12 - i);
        resto = (soma * 10) % 11;

        if ((resto == 10) || (resto == 11)) resto = 0;
        if (resto != parseInt(cpf.substring(10, 11))) return false;
        return true;
    }

    function verifyCNPJ(cnpj) {
        cnpj = cnpj.replace(/[^\d]+/g, '');

        if (cnpj == '' || cnpj.length != 14) return false;

        if (cnpj == "00000000000000" || cnpj == "11111111111111" || cnpj == "22222222222222" || cnpj == "33333333333333" || cnpj == "44444444444444" || cnpj == "55555555555555" || cnpj == "66666666666666" || cnpj == "77777777777777" || cnpj == "88888888888888" || cnpj == "99999999999999") return false;

        var tamanho = cnpj.length - 2
        var numeros = cnpj.substring(0, tamanho);
        var digitos = cnpj.substring(tamanho);
        var soma = 0;
        var pos = tamanho - 7;

        for (i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2)
                pos = 9;
        }

        var resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;

        if (resultado != digitos.charAt(0)) return false;

        tamanho = tamanho + 1;
        numeros = cnpj.substring(0, tamanho);
        soma = 0;
        pos = tamanho - 7;

        for (i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }

        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;

        if (resultado != digitos.charAt(1)) return false;

        return true;
    }

    function verifyPhone(phone_number) {
        if (phone_number.length < 14) {
            showError("O telefone informado é inválido.");
            return false;
        } else {
            var pattern = new RegExp(/^[1-9]{2}9?[0-9]{8}$/);
            if (pattern.test(phone_number.replace(/[^\d]+/g, ''))) {
                hideError();
                return true;
            } else {
                return false;
            }
        }
    }

    function verifyEmail(email) {
        var pattern = new RegExp(/^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/);
        return pattern.test(email);
    }

    function verifyBirthDate(birth) {
        var pattern = new RegExp(/^[12][0-9]{3}-(?:0[1-9]|1[0-2])-(?:0[1-9]|[12][0-9]|3[01])$/);
        var date = birth.split("/");
        return pattern.test(date[2] + "-" + date[1] + "-" + date[0]);
    }

    function scrollToTop() {
        $("html, body").animate({ scrollTop: $("#wc-gerencianet-messages").offset().top - 80 }, "slow");
    }

});
