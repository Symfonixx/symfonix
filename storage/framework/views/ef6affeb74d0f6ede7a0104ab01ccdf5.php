<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'formUrl']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['title', 'formUrl']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<div class="card">
    <!--begin::Card header-->
    <div class="card-header">
        <!--begin::Card title-->
        <div class="card-title fs-3 fw-bolder"><?php echo e(__($title)); ?></div>
        <!--end::Card title-->
    </div>
    <form method="POST" action="<?php echo e($formUrl); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="card-body">
            <?php echo e($slot); ?>

        </div>
        <!--begin::Card footer-->
        <div class="card-footer d-flex justify-content-end py-6 px-9">
            <a href="<?php echo e(url()->previous()); ?>" class="btn btn-light btn-active-light-primary me-2"><?php echo e(__('Discard')); ?></a>
            <button type="submit" class="btn btn-primary" id="submit"><?php echo e(__('Save Changes')); ?> <i class="bi bi-check2-circle"></i></button>
        </div>
        <!--end::Card footer-->

    </form>
</div>
<?php /**PATH D:\websites\symfonix\resources\views/components/admin/create-card.blade.php ENDPATH**/ ?>