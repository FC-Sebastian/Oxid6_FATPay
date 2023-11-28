[{if $payment->getId() == 'fatpay'}]
    [{oxstyle include=$oViewConf->getModuleUrl('fcfatpay', 'out/src/css/fatpay.css')}]
    <button type="submit" class="btn btn-lg btn-dark float-right submitButton nextStep largeButton">
        <img class="fatpay-logo-checkout" src="[{$oViewConf->getModuleUrl('fcfatpay', 'out/src/img/fatpay.svg')}]" alt="">
    </button>
    <script type="text/javascript">
        console.log('[{$oViewConf->fatpayGetModuleVersion()}]');
        let shopversion = '[{$oView->getShopVersion()}]';

        let billingaddress = {
            firstname: '[{$oxcmp_user->oxuser__oxfname->value|default: ''}',
            lastname: '[{$oxcmp_user->oxuser__oxlname->value|default: ''}]',
            street: '[{$oxcmp_user->oxuser__oxstreet->value|default: ''}] [{$oxcmp_user->oxuser__oxstreetnr->value|default: ''}]',
            zip: '[{$oxcmp_user->oxuser__oxzip->value|default: ''}]',
            city: '[{$oxcmp_user->oxuser__oxcity->value|default: ''}]',
            country: '[{$oxcmp_user->oxuser__oxcountry->value|default: ''}]'
        };

        [{if $oDelAdress}]
            let shippingaddress = {
                firstname: '[{$oDelAdress->oxaddress__oxfname->value|default: ''}',
                lastname: '[{$oDelAdress->oxaddress__oxlname->value|default: ''}]',
                street: '[{$oDelAdress->oxaddress__oxstreet->value|default: ''}] [{$oDelAdress->oxaddress__oxstreetnr->value|default: ''}]',
                zip: '[{$oDelAdress->oxaddress__oxzip->value|default: ''}]',
                city: '[{$oDelAdress->oxaddress__oxcity->value|default: ''}]',
                country: '[{$oDelAdress->oxaddress__oxcountry->value|default: ''}]'
            };
        [{else}]
            let shippingaddress = billingaddress;
        [{/if}]

    </script>
    [{oxscript include=$oViewConf->getModuleUrl('fcfatpay', 'out/src/js/fatpay.js')}]
[{else}]
    [{$smarty.block.parent}]
[{/if}]
