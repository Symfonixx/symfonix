<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([ 'model' ,'dataTable' => true, 'class' => null, 'formUrl' => null, 'search' => null,'title' => null]));

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

foreach (array_filter(([ 'model' ,'dataTable' => true, 'class' => null, 'formUrl' => null, 'search' => null,'title' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<div class="card">

    <div class="card-header border-0 pt-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || $title): ?>

        <div class="card-title">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search): ?>
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <input type="text" data-kt-data-table-filter="search"
                           class="form-control form-control-solid w-250px ps-12"
                           placeholder="<?php echo e(__($search)); ?>"/>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title): ?>
                <h2><?php echo e($title); ?></h2>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php elseif(!$search && !$title && $formUrl): ?>
            <div></div>
          <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($formUrl): ?>
            <div class="card-toolbar">

                <div class="d-flex justify-content-end" data-kt-comp-table-toolbar="base"></div>
                <div class="d-flex justify-content-end align-items-center d-none"
                     data-kt-comp-table-toolbar="selected">
                    <div class="fw-bold me-5">
                        <span class="me-2" data-kt-comp-table-toolbar="selected_count"></span><?php echo e(__('Selected')); ?>

                    </div>
                    <button type="button" class="btn btn-danger"
                            data-kt-comp-table-toolbar="delete_selected"><?php echo e(__('Delete Selected')); ?>

                    </button>
                </div>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($formUrl): ?>
        <form method="post" id="delete_all" action="<?php echo e($formUrl); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="card-body">
                <table class="table align-middle table-row-dashed fs-6 gy-5 <?php echo e($class ?? null); ?>"
                       <?php if(isset($dataTable)): ?> id="dataTable" <?php endif; ?>>
                    <?php echo e($slot); ?>

                </table>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($formUrl): ?>
        </form>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($model) && $model instanceof \Illuminate\Pagination\LengthAwarePaginator): ?>

        <div class="card-footer">
            <?php echo $model->withQueryString()->links(); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dataTable): ?>
    <?php $__env->startPush('scripts'); ?>
        <script>
            $(document).ready(function () {
                t = document.getElementById("dataTable");
                e = $('#dataTable').DataTable({
                    "responsive": true,
                    "paging": false,  // Disable pagination
                    "searching": true, // Enable searching
                    "info": false,
                    "order": [],          // Disable initial auto-ordering
                    "ordering": true

                });

                var l = (dataTable) => {
                    const r = t.querySelectorAll('[type="checkbox"]');
                    n = document.querySelector('[data-kt-comp-table-toolbar="base"]');
                    o = document.querySelector('[data-kt-comp-table-toolbar="selected"]');
                    c = document.querySelector('[data-kt-comp-table-toolbar="selected_count"]');
                    const a = document.querySelector('[data-kt-comp-table-toolbar="delete_selected"]');

                    if (!n || !o || !c || !a) {
                        console.log(n, o, c, a);
                        console.error("One or more elements not found.");
                        return;
                    }

                    r.forEach((checkbox => {
                        checkbox.addEventListener("click", (() => {
                            setTimeout(() => {
                                const checkboxes = t.querySelectorAll('tbody [type="checkbox"]');
                                let isChecked = false;
                                let checkedCount = 0;

                                checkboxes.forEach((checkbox => {
                                    if (checkbox.checked) {
                                        isChecked = true;
                                        checkedCount++;
                                    }
                                }));

                                if (isChecked) {
                                    c.innerHTML = checkedCount;
                                    n.classList.add("d-none");
                                    o.classList.remove("d-none");
                                } else {
                                    n.classList.remove("d-none");
                                    o.classList.add("d-none");
                                }
                            }, 50);
                        }));
                    }));

                    a.addEventListener("click", (() => {
                        Swal.fire({
                            text: "<?php echo e(__('This action cannot be undone.')); ?>",
                            icon: "warning",
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonText: "<?php echo e(__('Yes Delete!')); ?>",
                            cancelButtonText: "<?php echo e(__('No Cancel')); ?>",
                            customClass: {
                                confirmButton: "btn fw-bold btn-danger",
                                cancelButton: "btn fw-bold btn-active-light-primary"
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#delete_all').submit();
                            }
                        });
                    }));
                };

                l();

                if (e) {
                    document.querySelector('[data-kt-data-table-filter="search"]').addEventListener("keyup", ((event) => {
                        e.search(event.target.value).draw();
                    }));
                }
            });
        </script>

    <?php $__env->stopPush(); ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH D:\websites\symfonix\resources\views/components/admin/table.blade.php ENDPATH**/ ?>