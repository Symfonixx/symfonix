<?php $__env->startComponent('mail::message'); ?>
# New contact form

**Name:** <?php echo new \Illuminate\Support\EncodedHtmlString($contact->name); ?>  
**Email:** <?php echo new \Illuminate\Support\EncodedHtmlString($contact->email); ?>  
**Mobile:** <?php echo new \Illuminate\Support\EncodedHtmlString($contact->mobile); ?>  
**Subject:** <?php echo new \Illuminate\Support\EncodedHtmlString($contact->subject); ?>


**Message:**  
<?php echo new \Illuminate\Support\EncodedHtmlString($contact->message); ?>


<?php $__env->startComponent('mail::button', ['url' => $adminUrl]); ?>
View in admin
<?php echo $__env->renderComponent(); ?>

Thanks,<br>
<?php echo new \Illuminate\Support\EncodedHtmlString(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH D:\websites\symfonix\resources\views/emails/admin/contact-form.blade.php ENDPATH**/ ?>