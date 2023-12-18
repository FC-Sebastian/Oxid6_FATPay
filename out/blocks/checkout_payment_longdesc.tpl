[{$smarty.block.parent}]

[{if $sPaymentID == 'fatpay' || $sPaymentID == 'fatredirect'}]
    <script>
        document.getElementById('payment_[{$sPaymentID}]').nextElementSibling.append(getImgElement("[{$oViewConf->getModuleUrl('fcfatpay')}]out/src/img/[{$sPaymentID}].svg"));

        function getImgElement(src) {
            let img = document.createElement('img');
            img.src = src;
            img.alt = '';
            return img;
        }
    </script>
[{/if}]