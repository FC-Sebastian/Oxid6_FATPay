[{if $payment->getId() == 'fatpay' || $payment->getId() == 'fatredirect'}]
    [{oxstyle include=$oViewConf->getModuleUrl('fcfatpay', 'out/src/css/fatpay.css')}]
    <button type="submit" class="btn btn-lg btn-dark float-right submitButton nextStep largeButton">
        <img class="fatpay-logo-checkout" src="[{$oViewConf->getModuleUrl('fcfatpay', 'out/src/img/')}][{$payment->getId()}].svg" alt="">
    </button>
    <script type="text/javascript">
        [{assign var=oConfig value=$oViewConf->getConfig()}]
        let fcUrl = '[{$oConfig->getConfigParam('fcfatpayApiUrl')}]';
        let fcShopVersion = '[{$oView->getShopVersion()}]';
        let fcFatPayVersion = '[{$oViewConf->fcGetFatpayVersion()}]';
        let fcActiveLang = '[{$oViewConf->getActLanguageAbbr()}]';

        let fcBillingAddress = {
            firstname: '[{$oxcmp_user->oxuser__oxfname->value}]',
            lastname: '[{$oxcmp_user->oxuser__oxlname->value}]',
            street: '[{$oxcmp_user->oxuser__oxstreet->value}] [{$oxcmp_user->oxuser__oxstreetnr->value}]',
            zip: '[{$oxcmp_user->oxuser__oxzip->value}]',
            city: '[{$oxcmp_user->oxuser__oxcity->value}]',
            country: '[{$oxcmp_user->oxuser__oxcountry->value}]'
        };

        [{if $oDelAdress}]
            let fcShippingAddress = {
                firstname: '[{$oDelAdress->oxaddress__oxfname->value}]',
                lastname: '[{$oDelAdress->oxaddress__oxlname->value}]',
                street: '[{$oDelAdress->oxaddress__oxstreet->value}] [{$oDelAdress->oxaddress__oxstreetnr->value}]',
                zip: '[{$oDelAdress->oxaddress__oxzip->value}]',
                city: '[{$oDelAdress->oxaddress__oxcity->value}]',
                country: '[{$oDelAdress->oxaddress__oxcountry->value}]'
            };
        [{else}]
            let fcShippingAddress = fcBillingAddress;
        [{/if}]

        [{assign var=fcBasketPrice value=$oxcmp_basket->getPrice()}]
        let fcEmail = '[{$oxcmp_user->oxuser__oxusername->value}]';
        let fcCustNr = '[{$oxcmp_user->oxuser__oxcustnr->value}]';
        let fcOrderSum = '[{$fcBasketPrice->getNettoPrice()}]';
        let fcCurrency = '[{$currency->name}]';
        let fcPaymentMethod = '[{$payment->getId()}]';
    </script>
    [{if $payment->getId() == 'fatpay' || $payment->getId() == 'fatredirect'}]
        <input id="fatredirect_url" name="fatredirect_url" type="hidden">
    [{/if}]
    [{oxscript include=$oViewConf->getModuleUrl('fcfatpay', 'out/src/js/fatpay.js')}]
[{else}]
    [{$smarty.block.parent}]
[{/if}]