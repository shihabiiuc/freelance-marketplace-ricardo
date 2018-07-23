(function($, Models, Collections, Views) {
	$(document).ready(function() {
		/**
		 * model withdraw
		 */
		Models.withdraw = Backbone.Model.extend({
			action: 'fre-admin-withdraw-sync',
			initialize: function() {}
		});
		Collections.withdraw = Backbone.Collection.extend({
			model: Models.withdraw,
			action: 'fre-admin-fetch-withdraw',
			initialize: function() {
				this.paged = 1;
			}
		});

		// notification list control
		if( $('.fre-credit-withdraw-container').length > 0 ){
			var withdrawItem = Views.PostItem.extend({
				tagName: 'li',
				className: 'withdraw-item',
				template: _.template($('#fre-credit-withdraw-loop').html()),
				onItemBeforeRender: function() {
					// before render view
				},
				onItemRendered: function() {
					// after render view
				}
			});
			ListWithdraw = Views.ListPost.extend({
				tagName: 'li',
				itemView: withdrawItem,
				itemClass: 'withdraw-item'
			});
			if( $('.fre-credit-withdraw-container').find('.fre_credit_withdraw_dta').length > 0 ){
				var postsdata = JSON.parse($('.fre-credit-withdraw-container').find('.fre_credit_withdraw_dta').html()),
					posts = new Collections.withdraw(postsdata);
			} else {
				var posts = new Collections.withdraw();
			}
			/**
			 * init list blog view
			 */
			new ListWithdraw({
				itemView: withdrawItem,
				collection: posts,
				el: $('.fre-credit-withdraw-container').find('.list-withdraws')
			});
			/**
			 * init block control list blog
			 */
			new Views.BlockControl({
				collection: posts,
				el: $('.fre-credit-withdraw-container')
			});
		}
	});
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);
