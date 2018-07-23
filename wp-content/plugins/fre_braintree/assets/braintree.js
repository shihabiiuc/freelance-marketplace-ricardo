(function($, Views) {
    Views.BraintreeForm = Views.Modal_Box.extend({
        el: $('div#fre-payment-braintree'),
        events: {
            'submit form#braintree_form': 'submitBraintree'
        },
        initialize: function(options) {
            Views.Modal_Box.prototype.initialize.apply(this, arguments);
            // bind event to modal
            _.bindAll(this, 'setupData', 'initBraintree');
            this.blockUi = new Views.BlockUi();
            var view = this;
            // catch event select extend gateway
            AE.pubsub.on('ae:submitPost:extendGateway', this.setupData);
            AE.pubsub.on('ae:submitPost:extendGateway', this.initBraintree);
        },
        initBraintree: function () {
            var view = this;
            if (typeof braintree != 'undefined' && typeof Braintree_params != 'undefined' && $('#dropin-container iframe').length == 0) {
                braintree.setup(Braintree_params.client_token, 'dropin', {
                    container: 'dropin-container',
                    form: 'checkout-form',
                    defaultFirst: true,
                    paymentMethodNonceReceived:function(obj,nonce){
                        if(typeof nonce != undefined){
                            view.submitBraintree(nonce);
                        }
                    },
                    onReady: function(event){},
                    onError: function(error){
                        if(error){
                             AE.pubsub.trigger('ae:notification',{
                                msg : error.message,
                                notice_type : 'error'
                            });
                        }
                    },
                    onPaymentMethodReceived: function (obj, nonce) {}
                });
            }
        },
        // callback when user select braintree, set data and open modal
        setupData: function(data) {
            if (data.paymentType == 'braintree') {
                // this.openModal();
                this.data = data,
                plans = JSON.parse($('#package_plans').html());

                var packages	=	[];
                _.each(plans, function (element) {
					if(element.sku == data.packageID ) {
						packages	=	element ;
					}
				})
				var align = parseInt(ae_braintree.currency.align);
                if(align) {
                    var price       =   ae_braintree.currency.icon + packages.et_price; 
                }else {
                    var price       =   packages.et_price + ae_braintree.currency.icon;     
                }
				this.$el.find('span.plan_name').html( packages.post_title + ' (' + price +')');
				this.$el.find('span.plan_desc').html( packages.post_content );
            }
        },
        submitBraintree: function(nonce) {
            // event.preventDefault();
            var $form = $('form#braintree_form'),
                button = $form.find('.btn-submit'),
                $container = $form.parents('.step-wrapper'),
                view = this;
            view.data['braintree-payment-nonce'] = nonce;//$('#braintree-payment-nonce').val();
            $.ajax ({
                type : 'post',
                url  : ae_globals.ajaxURL,
                data : view.data,
                beforeSend : function () {
                    view.blockUi.block($container);
                },
                success : function (res) {
                    if(res.success) {
                        window.location = res.data.url;
                    } else {
                        view.blockUi.unblock();
                        clearInterval(refreshIntervalId);
                        AE.pubsub.trigger('ae:notification',{
                            msg : res.msg,
                            notice_type : 'error'
                        });
                    }
                }
            });
        },
    });
    // init stripe form
    $(document).ready(function() {
        new Views.BraintreeForm();
    });
})(jQuery, AE.Views);