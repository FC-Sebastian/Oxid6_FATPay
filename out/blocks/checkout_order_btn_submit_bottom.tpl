[{if $payment->getId() == 'fatpay'}]
    [{oxstyle include=$oViewConf->getModuleUrl('fcfatpay', 'out/src/css/fatpay.css')}]
    <button type="submit" class="btn btn-lg btn-dark float-right submitButton nextStep largeButton">
        <img class="fatpay-logo-checkout" src="[{$oViewConf->getModuleUrl('fcfatpay', 'out/src/img/fatpay.svg')}]" alt="">
    </button>
    <script type="text/javascript">
        let shopversion = '[{$oView->getShopVersion()}]';

        let billingaddress = {
            firstname: '[{$oxcmp_user->oxuser__oxfname->value}]',
            lastname: '[{$oxcmp_user->oxuser__oxlname->value}]',
            street: '[{$oxcmp_user->oxuser__oxstreet->value}] [{$oxcmp_user->oxuser__oxstreetnr->value}]',
            zip: '[{$oxcmp_user->oxuser__oxzip->value}]',
            city: '[{$oxcmp_user->oxuser__oxcity->value}]',
            country: '[{$oxcmp_user->oxuser__oxcountry->value}]'
        };

        [{if $oDelAdress}]
            let shippingaddress = {
                firstname: '[{$oDelAdress->oxaddress__oxfname->value}]',
                lastname: '[{$oDelAdress->oxaddress__oxlname->value}]',
                street: '[{$oDelAdress->oxaddress__oxstreet->value}] [{$oDelAdress->oxaddress__oxstreetnr->value}]',
                zip: '[{$oDelAdress->oxaddress__oxzip->value}]',
                city: '[{$oDelAdress->oxaddress__oxcity->value}]',
                country: '[{$oDelAdress->oxaddress__oxcountry->value}]'
            };
        [{else}]
            let shippingaddress = billingaddress;
        [{/if}]

    </script>
    <p>[{$oViewConf->fatpayGetModuleVersion()}]</p>
    [{oxscript include=$oViewConf->getModuleUrl('fcfatpay', 'out/src/js/fatpay.js')}]
[{else}]
    [{$smarty.block.parent}]
[{/if}]
