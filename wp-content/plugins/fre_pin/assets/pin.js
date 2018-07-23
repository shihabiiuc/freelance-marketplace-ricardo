(function($, Views) {
    Views.PinForm = Views.Modal_Box.extend({
        el: jQuery('div#fre-payment-pin'),
        events: {
            'submit form#pin_form': 'submitPin'
        },
        initialize: function(options) {
            var that = this;
            Views.Modal_Box.prototype.initialize.apply(this, arguments);
            // bind event to modal
            _.bindAll(this, 'setupData', 'submitPin', 'handleSuccess');
            // Pin.setPublishableKey(ae_pin.public_key);
            this.pinApi = new Pin.Api(ae_pin.public_key, 'test');

            this.blockUi = new Views.BlockUi();
            // catch event select extend gateway
            AE.pubsub.on('ae:submitPost:extendGateway', this.setupData);
        },
        // callback when user select pin, set data and open modal
        setupData: function(data) {
            if (data.paymentType == 'pin') {
                // this.openModal();
                this.data = data,
                plans = JSON.parse($('#package_plans').html());
                var packages	=	[];
                _.each(plans, function (element) {
					if(element.sku == data.packageID ) {
						packages	=	element ;
					}
				})
				var align = parseInt(ae_pin.currency.align);
                if(align) {
                    var price       =   ae_pin.currency.icon + packages.et_price;
                }else {
                    var price       =   packages.et_price + ae_pin.currency.icon;
                }
				this.$el.find('span.plan_name').html( packages.post_title + ' (' + price +')');
				this.$el.find('span.plan_desc').html( packages.post_content );
            }
        },
        submitPin: function(event) {
            event.preventDefault();
            var $form = $(event.currentTarget),
                $container = $form.parents('.step-wrapper'),
                view = this,
                card = {
                    number:           $('#cc-number').val(),
                    name:             $('#cc-name').val(),
                    expiry_month:     $('#cc-expiry-month').val(),
                    expiry_year:      $('#cc-expiry-year').val(),
                    cvc:              $('#cc-cvc').val(),
                    address_line1:    $('#address-line1').val(),
                    // address_line2:    $('#address-line2').val(),
                    address_city:     $('#address-city').val(),
                    address_state:    $('#address-state').val(),
                    address_postcode: $('#address-postcode').val(),
                    address_country:  $('#address-country').val()
                };
            this.blockUi.block($container);
            this.pinApi.createCardToken(card).then(view.handleSuccess, view.handleError).done();
            this.blockUi.unblock($form);
        },
        handleSuccess: function(card) {
            var view = this;
            if (status != 200 && card.error !== undefined) {
                view.kj(card);
                // view.closeModal();
                return false;
            }else{
                view.submitPayment(card);
            }
        },
        handleError :function(response) {
            if (response.messages) {
                AE.pubsub.trigger('ae:notification', {
                    msg : response.error_description,
                    notice_type : 'error'
                });
                $('ul.response_error').html('');
                $('.alert_area').show();
                $.each(response.messages, function(index, paramError) {
                    $('ul.response_error').append('<li>'+ paramError.param + ": " + paramError.message +'</li>');
                });
            }
        },

        submitPayment: function(card){
            var view = this;
            view.data.token = card.token;
            $.ajax ({
                type : 'post',
                url: ae_globals.ajaxURL,
                data:view.data,
                beforeSend : function () {
                },
                success : function (res) {
                    view.blockUi.unblock();
                    if(res.success){
                        view.closeModal();
                        window.location = res.data.url;
                    }else{
                        AE.pubsub.trigger('ae:notification',{
                            msg : res.msg,
                            notice_type : 'error'
                        });
                    }
                }
            });
        }
    });
    // init pin form
    $(document).ready(function() {
        new Views.PinForm();
    });
})(jQuery, AE.Views);
