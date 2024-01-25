[{if $payment->getId() == 'fatpay' || $payment->getId() == 'fatredirect'}]
    [{oxstyle include=$oViewConf->getModuleUrl('fcfatpay', 'out/src/css/fatpay.css')}]
    <button type="submit" class="btn btn-lg btn-dark float-right submitButton nextStep largeButton">
        <img class="fatpay-logo-checkout" src="[{$oViewConf->getModuleUrl('fcfatpay', 'out/src/img/')}][{$payment->getId()}].svg" alt="">
    </button>
[{else}]
    [{$smarty.block.parent}]
[{/if}]