[{extends file="payment.tpl"}]
[{block name="select_payment"}]
    <div>
        <div>
            [{$smarty.block.parent}]
        </div>
        <div>
            <img src="[{$oViewConf->getModuleUrl('fcfatpay')}]out/src/img/fatpay.svg" alt="">
        </div>
    </div>
[{/block}]