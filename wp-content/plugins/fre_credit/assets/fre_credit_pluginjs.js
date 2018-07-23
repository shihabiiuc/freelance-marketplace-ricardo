(function($, Models, Collections, Views) {
	Views.freCreditSubmitProject = Views.Modal_Box.extend({
		el: '#fre-payment-frecredit',
		events: {
			'submit form#submit_fre_credit_form': 'submitFreCreditPayment'
		},
		initialize: function(){
			Views.Modal_Box.prototype.initialize.apply(this, arguments);
			_.bindAll(this, 'setupData', 'choosePlan');
			this.blockUi = new Views.BlockUi();
			AE.pubsub.on('ae:submitPost:extendGateway', this.setupData);
			AE.pubsub.on('ae:submitPost:choosePlan', this.choosePlan);
			this.$form = this.$el.find('form#submit_fre_credit_form');
			//this.initValidate();
		},
		choosePlan : function ($step, $li, view){
			var price = $li.attr('data-price');
			if(parseFloat(price) > parseFloat(fre_credit_globals.available_of_user)){
				$('.fre-credit-payment-onsite').addClass('not-money');
				$('.fre-credit-payment-onsite a').addClass('btn-not-money');
				$('.fre-credit-payment-onsite a').removeClass('other-payment');
				$('.fre-credit-payment-onsite a').attr('href','');
				$('.fre-credit-payment-onsite .error').show();
			}else{
				$('.fre-credit-payment-onsite').removeClass('not-money');
				$('.fre-credit-payment-onsite a').removeClass('btn-not-money');
				$('.fre-credit-payment-onsite a').addClass('other-payment');
				$('.fre-credit-payment-onsite a').attr('href', '#fre-payment-frecredit');
				$('.fre-credit-payment-onsite .error').hide();
			}
		},
		setupData: function(data){
			var view = this;
			if (data.paymentType == 'frecredit') {
				view.data = data;
				var plans = JSON.parse($('#package_plans').html()),
					packages = [];
				_.each(plans, function (element) {
					if (element.sku == data.packageID) {
						packages = element;
					}
				});
				var align = parseInt(fre_credit_globals.currency.align);
				if(align) {
					var price       =   fre_credit_globals.currency.icon + parseFloat(packages.et_price).toFixed(2);
					var balance 	= 	fre_credit_globals.currency.icon + parseFloat(fre_credit_globals.available_of_user - packages.et_price).toFixed(2);
				}else {
					var price       =   parseFloat(packages.et_price).toFixed(2) + fre_credit_globals.currency.icon;
					var balance 	=  	parseFloat(fre_credit_globals.available_of_user - packages.et_price).toFixed(2) + fre_credit_globals.currency.icon;
				}
				this.$el.find('span.package_price').html( price );
				this.$el.find('span.estimate_balance').html( balance );
			}
		},
		submitFreCreditPayment: function(event){
			event.preventDefault();
			var view = this;
			var $form = $(event.currentTarget),
				$container = $form.parents('.step-wrapper');
			if( view.$form.valid() ) {				
				view.data.secureCode = $form.find('#fre_credit_secure_code').val();
				$.ajax({
					url: ae_globals.ajaxURL,
					type: 'post',
					data: view.data,
					beforeSend: function () {
						view.blockUi.block($container);
					},
					success: function (res) {
						if (res.success) {
							window.location = res.data.url;
						}else {
							AE.pubsub.trigger('ae:notification', {
								msg: res.msg,
								notice_type: 'error'
							});
							view.blockUi.unblock();
						}
						
					}
				});
			}
			return false;
		},
		initValidate: function(){
			var view = this;
			view.form_validator = view.$form.validate({
				errorElement: "p",
				rules: {
					fre_credit_secure_code: 'required'

				},
				highlight:function(element, errorClass, validClass){
					var $target = $(element );
					var $parent = $(element ).parent();
					$parent.addClass('has-error');
					$target.addClass('has-visited');
				},
				unhighlight:function(element, errorClass, validClass){
					// position error label after generated textarea
					var $target = $(element );
					var $parent = $(element ).parent();
					$parent.removeClass('has-error');
					$target.removeClass('has-visited');
				}
			});
		},

	});
	/**
	 * model withdraw
	 */
	Models.Withdraw = Backbone.Model.extend({
		action: 'fre-withdraw-sync',
		initialize: function() {}
	});
	Views.Modal_Withdraw = Views.Modal_Box.extend({
		events: {
			'change input[name="amount"]': 'changeValue',
			'submit form#fre_credit_withdraw_form': 'sendWithdrawRequest'
		},

		/**
		 * init view setup Block Ui and Model User
		 */
		initialize: function () {
			AE.Views.Modal_Box.prototype.initialize.call();
			this.model = new Models.Withdraw();
			this.$form = this.$el.find('form#fre_credit_withdraw_form');
			setTimeout(function(){
				$('form#fre_credit_withdraw_form select option:nth-child(1)').prop('selected', true);
			},1000);
			this.data = {};
			this.blockUi = new Views.BlockUi();
		},
		onOpen: function(){
			var view = this;
			view.openModal();
			view.setupFields();
		},
		setupFields: function(){
			var view = this;
			view.$el.find('p.warning').hide();
			view.$el.find('input[name="amount"]').prop('disabled', false);
			$.ajax({
				url: ae_globals.ajaxURL,
				type: 'get',
				data: {
					action: 'fre-credit-get-balance-info'
				},
				beforeSend: function() {},
				success: function(res) {
					view.data = res;
					//view.$el.find('.fre_credit_total').html(res.total_text);
					//view.$el.find('.fre_credit_available').html(res.available_text);
					//view.$el.find('.fre_credit_freezable').html(res.freezable_text);
					//view.$el.find('.fre_credit_min_withdraw').html(res.min_withdraw_text);
					// view.$el.find('input[name="amount"]').attr('max', Number(res.available.balance));
					view.$el.find('input[name="amount"]').attr('min', Number(res.min_withdraw));
					if(parseInt(res.min_withdraw) > parseInt(res.available.balance)){
						view.$el.find('p.warning').html(fre_credit_globals.unable_withdraw_text).show();
						view.$el.find('input[name="amount"]').prop('disabled', true);
					}
				}
			});
			view.$form.find('input, textarea, select').each(function() {
				$(this).val('');
			});
		},
		sendWithdrawRequest: function(e){
			e.preventDefault();
			var view = this;
			$target = $(e.currentTarget);

			//update css select choson
            $("#payment_method").css("display",'block').css('height',0);
            $("#payment_method").closest('div').find('label').css("margin-bottom",'0');

            view.initValidate();
			/**
			 * scan all fields in form and set the value to model user
			 */
			$target.find('input, textarea, select').each(function() {
				view.model.set($(this).attr('name'), $(this).val());
			});
			if( view.$form.valid() ){
				view.model.save('', '', {
					beforeSend: function () {
						view.blockUi.block($target);
					},
					success: function (result, resp, jqXHR) {
						if( resp.success ) {
							AE.pubsub.trigger('ae:notification', {
								msg: resp.msg,
								notice_type: 'success'
							});
							view.data = resp.data;
							AE.pubsub.trigger('sendWithdrawRequest:success', resp.data);
							view.closeModal();

                            window.location.href = '';
						}
						else{
							AE.pubsub.trigger('ae:notification', {
								msg: resp.msg,
								notice_type: 'error'
							});
						}
						view.blockUi.unblock();
					}
				});
			}
		},
		initValidate: function(){
			var view = this;
			view.form_validator = view.$form.validate({
				errorElement: "p",
				rules: {
					amount: {
						required: true,
						number: true,
						// max: Number(view.data.available.balance)
					},
                    payment_method: 'required',
					//payment_info: 'required',
					secureCode: 'required'
				},
				highlight:function(element, errorClass, validClass){
					// var $target = $(element );
					// var $parent = $(element ).parent();
					// var $container = $(element).closest('div');
					// $parent.addClass('has-error');
					// $target.addClass('has-visited');
					$(element).closest('.fre-input-field').addClass('error');
					// $container.find('i.fa-exclamation-triangle').remove();
					// $container.append('<i class="fa fa-exclamation-triangle" ></i>');
				},
				unhighlight:function(element, errorClass, validClass){
					// position error label after generated textarea
					// var $target = $(element );
					// var $parent = $(element ).parent();
					// $parent.removeClass('has-error');
					// $target.removeClass('has-visited');
					// var $container = $(element).closest('div');
					// $container.find('i.fa-exclamation-triangle').remove();
					$(element).closest('.fre-input-field').removeClass('error');
				}
			});
		},
		changeValue: function(e){
			var view = this;
			$target = $(e.currentTarget);
			var amount = Number($target.val());
			if(parseFloat(amount) < fre_credit_globals.minimum_withdraw && $target.val() != ''){
				//alert(fre_credit_globals.number_mgs);
				return false;
			}else if(parseFloat(amount) > view.data.available.balance){
				//alert(fre_credit_globals.not_enought_mgs);
				return false;
			}
			var available = Number(view.data.available.balance) - amount;
			available = view.around(available);
			var freezable = Number(view.data.freezable.balance) + amount;
			freezable = view.around(freezable);
			var align = parseInt(fre_credit_globals.currency.align);
			if(align) {
				var available       =   fre_credit_globals.currency.icon + available;
				var freezable       =   fre_credit_globals.currency.icon + freezable;
			}else {
				var available       =   available + fre_credit_globals.currency.icon;
				var freezable       =   freezable + fre_credit_globals.currency.icon;
			}
			view.$el.find('.fre_credit_available').html(available);
			view.$el.find('.fre_credit_freezable').html(freezable);
		},
		around: function(x){
			var n = parseFloat(x);
			x = parseFloat(Math.round(n * 100)/100).toFixed(2);
			return x;
		}
	});
	Views.Modal_Edit_EmailCredit = Views.Modal_Box.extend({
		events: {
			'submit form#fre_credit_edit_paypal_form': 'sendRequest'
		},
		/**
		 * init view setup Block Ui and Model User
		 */
		initialize: function () {
			var view = this;
			this.user = AE.App.user;
			AE.Views.Modal_Box.prototype.initialize.call();			
			this.profile = new Models.Profile();
			this.$form = this.$el.find('form#fre_credit_edit_paypal_form');
			this.blockUi = new Views.BlockUi();
			view.initValidate();
		},
		onOpen: function(){
			var view = this;
			view.openModal();
			view.setupFields();
		},	
		setupFields: function(){
			var view = this;
			$.ajax({
				url: ae_globals.ajaxURL,
				type: 'get',
				data: {
					action: 'fre-credit-get-profile-info'
				},
				beforeSend: function() {},
				success: function(res) {
					var data = res.data;
					if(res.success){
						view.$el.find('#email_paypal').val(data.email_paypal);
					}
				}
			});
			view.$form.find('input, textarea, select').each(function() {
				$(this).val('');
			});
		},
		initValidate: function(){
			var view = this;
			view.form_validator = view.$form.validate({
				rules: {
					email_paypal: {
						required: true,
						email : true
					},
					secure_code: 'required'
				}
			});
		},
		sendRequest : function(e){
			var view = this;
			$target = $(e.currentTarget);
			e.preventDefault();
			view.initValidate();
			
			$.ajax({
				url: ae_globals.ajaxURL,
				type: 'get',
				data: {
					action: 'fre-credit-update-email-paypal',
					paypal : $target.find('#email_paypal').val(),
					secure_core : $target.find('#secure_code').val()
				},
				beforeSend: function() {
					view.blockUi.block($target);
				},
				success: function(res) {
					view.data = res;
					if( res.success ) {
						AE.pubsub.trigger('ae:notification', {
							msg: res.msg,
							notice_type: 'success'
						});
						view.data = res.data;
						if(res.data){
							$('.email-paypal-credit .budget').html($target.find('#email_paypal').val());
							view.closeModal();
						}

                        window.location.href = '';
                    }
					else{
						AE.pubsub.trigger('ae:notification', {
							msg: res.msg,
							notice_type: 'error'
						});
					}
					view.blockUi.unblock();
				}
			});
		}	
	});
	Views.Modal_Update_Bank = Views.Modal_Box.extend({
		events: {
			'submit form#fre_credit_updat_bank_form': 'sendRequest'
		},
		/**
		 * init view setup Block Ui and Model User
		 */
		initialize: function () {
			var view = this;
			this.user = AE.App.user;
			AE.Views.Modal_Box.prototype.initialize.call();			
			this.profile = new Models.Profile();
			this.$form = this.$el.find('form#fre_credit_updat_bank_form');
			this.blockUi = new Views.BlockUi();
			view.initValidate();
		},
		onOpen: function(){
			var view = this;
			view.openModal();
			view.setupFields();
		},	
		setupFields: function(){
			var view = this;
			$.ajax({
				url: ae_globals.ajaxURL,
				type: 'get',
				data: {
					action: 'fre-credit-get-profile-info'
				},
				beforeSend: function() {},
				success: function(res) {
					var data = res.data;
					if(res.success){
						view.$el.find('#account_number').val(data.banking_info.account_number);
						view.$el.find('#banking_information').val(data.banking_info.banking_information);
						view.$el.find('#benficial_owner').val(data.banking_info.benficial_owner);
					}
				}
			});
			view.$form.find('input, textarea, select').each(function() {
				$(this).val('');
			});
		},
		initValidate: function(){
			var view = this;
			view.form_validator = view.$form.validate({
				rules: {
					benficial_owner : 'required',
					account_number : {
						required: true,
						number: true
					},
					banking_information : 'required',
					secure_code: 'required'
				}
			});
		},
		sendRequest : function(e){
			var view = this;
			$target = $(e.currentTarget);
			e.preventDefault();
			view.initValidate();
			
			$.ajax({
				url: ae_globals.ajaxURL,
				type: 'get',
				data: {
					action: 'fre-credit-update-bank',
					benficial_owner : $target.find('#benficial_owner').val(),
					account_number : $target.find('#account_number').val(),
					banking_information : $target.find('#banking_information').val(),
					secure_core : $target.find('#secure_code').val()
				},
				beforeSend: function() {
					view.blockUi.block($target);
				},
				success: function(res) {
					view.data = res;
					if( res.success ) {
						AE.pubsub.trigger('ae:notification', {
							msg: res.msg,
							notice_type: 'success'
						});
						view.data = res.data;
						if(res.data){
							$('.bank-info-credit .budget ').html($target.find('#benficial_owner').val());
							view.closeModal();
						}

                        window.location.href = '';
					}
					else{
						AE.pubsub.trigger('ae:notification', {
							msg: res.msg,
							notice_type: 'error'
						});
					}
					view.blockUi.unblock();
				}
			});
		}	
	});

	Views.freCreditPage = Backbone.View.extend({
		el: '.tabs-credits',
		events: {
			'click .btn-withdraw-action': 'showModal',
			'click .btn-edit-email-credit' : 'showModalEditCredit',
			'click .btn-update-bank' : 'showModalUpdateBank',
			'click .transaction-filter-clear' : 'clearFilter'
		},
		initialize: function(){
			var view = this;
			AE.pubsub.on('sendWithdrawRequest:success', this.updateInfo, this);
			var from = new Date();
			var to = new Date();

			var start = $('#fre_credit_from').datetimepicker({
				// defaultDate: from,
				format: 'MM/DD/YYYY',
				icons: {
					previous: 'fa fa-angle-left',
					next: 'fa fa-angle-right',
				},
				useCurrent: false,
			}).on("dp.change", function(e) {
				$('#fre_credit_to').datetimepicker('show');
				end.data("DateTimePicker").minDate(e.date);
				//if($('#fre_credit_to').val()){
					view.filterDate('fre_credit_from', $('#fre_credit_from').val());
				//}
			});

			var end = $('#fre_credit_to').datetimepicker({
				// defaultDate: to,
				format: 'MM/DD/YYYY',
				icons: {
					previous: 'fa fa-angle-left',
					next: 'fa fa-angle-right',
				},
				useCurrent: false,
				widgetPositioning: {
			        vertical: 'auto',
			        horizontal: 'right'
			    }
			}).on("dp.change", function(e) {
				start.data("DateTimePicker").maxDate(e.date);
				view.filterDate('fre_credit_to', $('#fre_credit_to').val());
			});
		},
		filterDate: function (name, value) {
			historyCt.page = 1;
			historyCt.query[name] = value;
			historyCt.fetch($('#'+name).closest('#fre-credit-transaction'));
		},
		showModal: function(e){
			e.preventDefault();
			var view = this;
			if( typeof this.Modal_Withdraw === 'undefined' ) {
				this.Modal_Withdraw = new Views.Modal_Withdraw({
					el: "#myModal"
				});
			}
			this.Modal_Withdraw.onOpen();
		},
		showModalEditCredit: function(e){
			e.preventDefault();
			var view = this;
			if( typeof this.Modal_Edit_EmailCredit === 'undefined' ) {
				this.Modal_Edit_EmailCredit = new Views.Modal_Edit_EmailCredit({
					el: "#modalEditPaypal"
				});
			}
			this.Modal_Edit_EmailCredit.onOpen();
		},
		showModalUpdateBank : function(e){
			e.preventDefault();
			var view = this;
			if( typeof this.Modal_Update_Bank === 'undefined' ) {
				this.Modal_Update_Bank = new Views.Modal_Update_Bank({
					el: "#modalUpdateBank"
				});
			}
			this.Modal_Update_Bank.onOpen();
		},
		updateInfo: function(data){
			this.$el.find('.fre_credit_total_text').html(data.total_text);
			this.$el.find('.fre_credit_available_text').html(data.available_text);
			this.$el.find('.fre_credit_freezable_text').html(data.freezable_text);
		},
        clearFilter :function(event){
            event.preventDefault();
            var   $target = $(event.currentTarget);
            // reset input, select
            historyCt.$el.find('form')[0].reset();
            historyCt.$el.find('form select').trigger('chosen:updated');
            // reset query
            // request
            historyCt.page = 1;
            historyCt.query["history_type"] = '';
            historyCt.query["fre_credit_to"] = '';
            historyCt.query["fre_credit_from"] = '';
            historyCt.query["history_status"] = '';
            historyCt.fetch($target.closest('#fre-credit-transaction'));
        }
	});
	Views.creditBody = Backbone.View.extend({
		el: 'body',
		events: {
			'click .request-secure-code': 'requestSecureCode'
		},
		initialize: function(){
			this.blockUi = new Views.BlockUi();
		},
		requestSecureCode: function(e){
			e.preventDefault();
			var view = this;
			$target = $(e.currentTarget);
			var data = {
				action: 'fre-credit-request-secure-code'
			};
			$.ajax({
				url: ae_globals.ajaxURL,
				type: 'post',
				data: data,
				beforeSend: function () {
					view.blockUi.block($target);
				},
				success: function (res) {
					if (res.success) {
						AE.pubsub.trigger('ae:notification', {
							msg: res.msg,
							notice_type: 'success'
						});
					}
					else {
						AE.pubsub.trigger('ae:notification', {
							msg: res.msg,
							notice_type: 'error'
						});
					}
					view.blockUi.unblock();
				}
			});

		}
	});
	$(document).ready(function() {
		new Views.freCreditSubmitProject();
		new Views.creditBody();
		/**
		 * model withdraw
		 */
		Models.history = Backbone.Model.extend({
			action: 'fre-history-sync',
			initialize: function() {}
		});
		Collections.history = Backbone.Collection.extend({
			model: Models.history,
			action: 'fre-fetch-history',
			initialize: function() {
				this.paged = 1;
			}
		});
		if($('#fre-credit-history-loop').length > 0){
			var historyItem = Views.PostItem.extend({
				tagName: 'div',
				className: 'fre-table-row',
				template: _.template($('#fre-credit-history-loop').html()),
				onItemBeforeRender: function() {
					// before render view
				},
				onItemRendered: function() {
					// after render view
				}
			});
			ListHistory = Views.ListPost.extend({
				tagName: 'div',
				itemView: historyItem,
				itemClass: 'fre-table-row'
			});
			// notification list control
			if( $('.fre-credit-history-wrapper').length > 0 ){

				if( $('.fre-credit-history-wrapper').find('.fre_credit_history_data').length > 0 ){
					var postsdata = JSON.parse($('.fre-credit-history-wrapper').find('.fre_credit_history_data').html()),
						posts = new Collections.history(postsdata);
				} else {
					var posts = new Collections.history();
				}
				/**
				 * init list blog view
				 */
				new ListHistory({
					itemView: historyItem,
					collection: posts,
					el: $('.fre-credit-history-wrapper').find('.list-histories')
				});
				/**
				 * init block control list blog
				 */
				Views.historyControl = Views.BlockControl.extend({
					events: function() {
						return _.extend({}, _.result(Views.BlockControl.prototype, 'events') || {}, {
							'change .fre-credit-history-filter-type': 'historyFilterType',
							'change .fre-credit-history-filter-status': 'historyFilterStatus'
						});
					},
                    historyFilterType: function(event){
						event.preventDefault();
						var $target = $(event.currentTarget),
							name = 'history_type',
							view = this;
						if (name !== 'undefined') {
							view.query['history_type'] = $target.val();
							view.page = 1;
							// fetch page
							view.fetch($target.closest('#fre-credit-transaction'));
						}
					},
                    historyFilterStatus: function(event){
						event.preventDefault();
						var $target = $(event.currentTarget),
							name = 'history_status',
							view = this;
						if (name !== 'undefined') {
							view.query['history_status'] = $target.val();
							view.page = 1;
							// fetch page
							view.fetch($target.closest('#fre-credit-transaction'));
						}
					},
					onBeforeFetch: function(){
	                    if($('.fre-credit-history-wrapper').find('.no-transaction').length > 0 ){
	                        $('.fre-credit-history-wrapper').find('.no-transaction').remove();
	                    }
	                },
	                onAfterFetch: function(result, res){
	                    if( !res.success || result.length == 0){
	                        //$('.fre-credit-history-wrapper').find('.list-histories').find('.fre-table-row').remove();
	                        //$('.fre-credit-history-wrapper').find('.list-histories').closest('div').append(fre_credit_globals.no_transaction_msg);
							$(fre_credit_globals.no_transaction_msg).insertAfter($('.fre-credit-history-wrapper').find('.list-histories'));
	                    }
                    }
				});
				historyCt = new Views.historyControl({
					collection: posts,
					el: $('.fre-credit-history-wrapper')
				});
				new Views.freCreditPage();
			}
		}
	});
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);
