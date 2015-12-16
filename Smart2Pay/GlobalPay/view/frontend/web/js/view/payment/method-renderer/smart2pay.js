define(
    [
        'ko',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'Smart2Pay_GlobalPay/js/model/payment/method-list'
    ],
    function (ko, Component, quote, methodsList) {
        'use strict';

        return Component.extend({

            defaults: {
                template: 'Smart2Pay_GlobalPay/payment/smart2pay'
            },

            // Overwrite properties / functions

            redirectAfterPlaceOrder: false,

            isPlaceOrderActionAllowed: ko.observable(false),

            // END Overwrite properties / functions

            selectedS2PPaymentMethod: ko.observable(null),

            isPaymentMethodSelected: ko.observable(false),

            //    console.log( "selfComponent: " );
            //    console.log( selfComponent );
            //    for (var i in selfComponent){
            //        console.log(i);
            //        console.log(selfComponent[i]);
            //
            //    }
            //    console.log( selfComponent.isPaymentMethodSelected );
            //    return quote.billingAddress() != null && selfComponent.isPaymentMethodSelected();
            //}),

            s2pSelectPaymentMethod: function( method )
            {
                selectedS2PPaymentMethod(method);

                if( method == 0 )
                {
                    isPaymentMethodSelected( false );
                    isPlaceOrderActionAllowed( false );
                } else
                {
                    isPaymentMethodSelected( true );
                    isPlaceOrderActionAllowed( quote.billingAddress() != null );
                }
            },

            /**
             * Get payment method data
             */
            getData: function() {

                var self = this;

                return {
                    "method": this.item.method,
                    "po_number": null,
                    "additional_data": null,
                    "s2p_payment_method": self.selectedS2PPaymentMethod
                };
            },

            pulamea: function ()
            {
                console.log( quote.billingAddress() );
                console.log( this.isPaymentMethodSelected() );
                console.log( this.isPlaceOrderActionAllowed() );
                return 'o2k2';
            },

            getInstructions: function ()
            {
                return '';
            },

            getS2PMethods: function()
            {
                return methodsList;
            }
        });
    }
);
