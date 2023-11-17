[{$smarty.block.parent}]

[{if $sPaymentID == 'fatpay'}]
    <script>
        document.getElementById('payment_fatpay').nextElementSibling.append(getImgElement("[{$oViewConf->getModuleUrl('fcfatpay')}]out/src/img/fatpay.svg"));

        function getImgElement(src) {
            let img = document.createElement('img');
            img.src = src;
            img.alt = '';

            return img;
        }
    </script>
[{/if}]