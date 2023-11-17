[{if $payment->getId() == 'fatpay'}]
    <p>POOP</p>
[{else}]
    [{$smarty.block.parent}]
[{/if}]
