( function( $, CherryJsCore ) {
	'use strict';
	setTimeout(function(){
		CherryJsCore.utilites.namespace('shiftBackScripts');
		CherryJsCore.shiftBackScripts = {
			saveHandlerId: 'shift_wall_settings_form',
			authHandlerId: 'shift_wall_settings_auth',
			saveButtonId: '#shift-save-buttons',
			authButtonId: '#shift-auth-buttons',
			formId: '#shift_wall_settings_form',
			saveOptionsInstance: null,
			authOptionsInstance: null,
			cherryHadlerInit: function () {
				// Add function to the event CherryHandlerInit
				$( document ).on( 'CherryHandlerInit', this.init.bind( this ) );
			},
			init: function () {
				
				this.saveOptionsInstance = new CherryJsCore.CherryAjaxHandler({
					handlerId: this.saveHandlerId,
					successCallback: this.saveSuccessCallback.bind( this )
				});
				
				this.authOptionsInstance = new CherryJsCore.CherryAjaxHandler({
					handlerId: this.authHandlerId,
					successCallback: this.authSuccessCallback.bind( this )
				});

				this.addEvents();
				
			},
			addEvents: function () {
				$( 'body' )
					.on( 'click', this.saveButtonId, this.saveOptionsHandler.bind( this ) )
					.on( 'click', this.authButtonId, this.authOptionsHandler.bind( this ) )
			},
			saveOptionsHandler: function( event ) {
				this.saveOptionsInstance.sendFormData( this.formId );
			},
			authOptionsHandler: function( event ) {
				this.authOptionsInstance.sendFormData( this.formId );
			},
			saveSuccessCallback: function(resp) {

			},
			authSuccessCallback: function(resp) {
				if(typeof resp.data != 'undefined'){
					window.location.href = resp.data.auth_url;
				}
			}
		}
		CherryJsCore.shiftBackScripts.init();
	}, 500)
} ( jQuery, window.CherryJsCore ) );