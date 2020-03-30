<tr>
    <td>
        <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
            
            <tr>
                <td class="content-cell" align="center">
                    <?php echo e(Illuminate\Mail\Markdown::parse($slot)); ?>

                </td>
            </tr>
            <tr>
                <td>
                    <img src="<?php echo e(asset('assets/images/email/footer.jpg')); ?>">
                </td>
            </tr>
            
        </table>
    </td>
</tr>
<?php /**PATH /var/www/resources/views/vendor/mail/html/footer.blade.php ENDPATH**/ ?>