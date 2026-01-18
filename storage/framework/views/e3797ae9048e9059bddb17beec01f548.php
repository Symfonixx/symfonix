<?php $__env->startSection('title' , __('Contacts')); ?>

<?php $__env->startSection('toolbar'); ?>
    <?php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Contacts'],
        ];
    ?>
    <?php if (isset($component)) { $__componentOriginaldbbc880c47f621cda59b70d6eb356b2f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldbbc880c47f621cda59b70d6eb356b2f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.breadcrumb','data' => ['pageTitle' => __('Contacts'),'breadcrumbItems' => $breadcrumbItems]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Contacts')),'breadcrumbItems' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($breadcrumbItems)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldbbc880c47f621cda59b70d6eb356b2f)): ?>
<?php $attributes = $__attributesOriginaldbbc880c47f621cda59b70d6eb356b2f; ?>
<?php unset($__attributesOriginaldbbc880c47f621cda59b70d6eb356b2f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldbbc880c47f621cda59b70d6eb356b2f)): ?>
<?php $component = $__componentOriginaldbbc880c47f621cda59b70d6eb356b2f; ?>
<?php unset($__componentOriginaldbbc880c47f621cda59b70d6eb356b2f); ?>
<?php endif; ?>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a href="<?php echo e(route('admin.contact_forms.export')); ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-file-earmark-excel"></i> <?php echo e(__('Export to Excel')); ?>

        </a>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
<script>
	function blockIp(ip) {
		var form = document.createElement('form');
		form.method = 'POST';
		form.action = '<?php echo e(route('admin.firewall.block')); ?>';
		var token = document.createElement('input');
		token.type = 'hidden';
		token.name = '_token';
		token.value = '<?php echo e(csrf_token()); ?>';
		var ipInput = document.createElement('input');
		ipInput.type = 'hidden';
		ipInput.name = 'ip';
		ipInput.value = ip;
		form.appendChild(token);
		form.appendChild(ipInput);
		document.body.appendChild(form);
		form.submit();
	}
</script>
<?php $__env->stopSection(); ?>
<?php if (isset($component)) { $__componentOriginal91fdd17964e43374ae18c674f95cdaa3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal91fdd17964e43374ae18c674f95cdaa3 = $attributes; } ?>
<?php $component = App\View\Components\AdminLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AdminLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php if (isset($component)) { $__componentOriginal53cf72b3da4b8700c9115c02c0eead10 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53cf72b3da4b8700c9115c02c0eead10 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.table','data' => ['model' => $model,'search' => 'Search In Contacts','formUrl' => route('admin.contact_forms.deleteMulti')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($model),'search' => 'Search In Contacts','formUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.contact_forms.deleteMulti'))]); ?>
        <!--begin::Table head-->
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>

            <th><?php echo e(__('Details')); ?></th>
            <th><?php echo e(__('Ip Address')); ?></th>
            <th><?php echo e(__('Subject')); ?></th>
            <th><?php echo e(__('Created At')); ?></th>
            <th></th>
        </tr>
        </thead>
        <!--end::Table head-->
        <!--begin::Table body-->
        <tbody class="text-gray-600 fw-semibold">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $model; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="<?php echo e($contact->id); ?>"/>
                    </div>
                </td>

                <td>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 mb-1">
                            <?php echo e($contact->name); ?>

                        </span>
                        <a class="text-hover-primary text-gray-500" target="_blank"
                           href="tel:<?php echo e($contact->mobile); ?>"><?php echo e($contact->mobile); ?></a>
                        <a class="text-hover-primary text-gray-500" target="_blank"
                           href="mailto:<?php echo e($contact->email); ?>"><?php echo e($contact->email); ?></a>
                    </div>
                </td>

                <td>
                    <a href="https://whatismyipaddress.com/ip/<?php echo e($contact->ip_address); ?>" target="_blank">
                        <?php echo e($contact->ip_address); ?>

                    </a>
                </td>

                <td>
                    <?php echo e($contact->subject); ?>

                </td>
                <td>
                    <?php echo e($contact->created_at); ?>

                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#contactModal<?php echo e($contact->id); ?>">
                            <i class="bi bi-eye"></i> <?php echo e(__('View Details')); ?>

                        </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($contact->ip_address)): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($contact->blocked)): ?>
                                <span class="badge badge-light-danger fs-7 fw-bold"><?php echo e(__('Blocked')); ?></span>
                            <?php else: ?>
                                <button type="button" class="btn btn-sm btn-danger" onclick="blockIp('<?php echo e($contact->ip_address); ?>')">
                                   <i class="bi bi-lock"></i> <?php echo e(__('Block IP')); ?>

                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </td>
            </tr>

            <!-- Contact Details Modal -->
            <div class="modal fade" id="contactModal<?php echo e($contact->id); ?>" tabindex="-1" aria-labelledby="contactModalLabel<?php echo e($contact->id); ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="contactModalLabel<?php echo e($contact->id); ?>"><?php echo e(__('Contact Details')); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong><?php echo e(__('Name')); ?>:</strong>
                                    <p><?php echo e($contact->name); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <strong><?php echo e(__('Email')); ?>:</strong>
                                    <p><a href="mailto:<?php echo e($contact->email); ?>" target="_blank"><?php echo e($contact->email); ?></a></p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong><?php echo e(__('Mobile')); ?>:</strong>
                                    <p><a href="tel:<?php echo e($contact->mobile); ?>" target="_blank"><?php echo e($contact->mobile); ?></a></p>
                                </div>
                                <div class="col-md-6">
                                    <strong><?php echo e(__('IP Address')); ?>:</strong>
                                    <p><a href="https://whatismyipaddress.com/ip/<?php echo e($contact->ip_address); ?>" target="_blank"><?php echo e($contact->ip_address); ?></a></p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong><?php echo e(__('Subject')); ?>:</strong>
                                <p><?php echo e($contact->subject); ?></p>
                            </div>
                            <div class="mb-3">
                                <strong><?php echo e(__('Message')); ?>:</strong>
                                <div class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                                    <?php echo e($contact->message); ?>

                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong><?php echo e(__('Blocked')); ?>:</strong>
                                    <p><span class="badge badge-light-<?php echo e($contact->blocked ? 'danger' : 'success'); ?>"><?php echo e($contact->blocked ? __('Yes') : __('No')); ?></span></p>
                                </div>
                                <div class="col-md-6">
                                    <strong><?php echo e(__('Created At')); ?>:</strong>
                                    <p><?php echo e($contact->created_at ? $contact->created_at->format('Y-m-d H:i:s') : 'N/A'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('Close')); ?></button>
                            <?php if(!empty($contact->ip_address) && !$contact->blocked): ?>
                                <button type="button" class="btn btn-danger" onclick="blockIp('<?php echo e($contact->ip_address); ?>')">
                                    <i class="bi bi-lock"></i> <?php echo e(__('Block IP')); ?>

                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
        <!--end::Table body-->
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53cf72b3da4b8700c9115c02c0eead10)): ?>
<?php $attributes = $__attributesOriginal53cf72b3da4b8700c9115c02c0eead10; ?>
<?php unset($__attributesOriginal53cf72b3da4b8700c9115c02c0eead10); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53cf72b3da4b8700c9115c02c0eead10)): ?>
<?php $component = $__componentOriginal53cf72b3da4b8700c9115c02c0eead10; ?>
<?php unset($__componentOriginal53cf72b3da4b8700c9115c02c0eead10); ?>
<?php endif; ?>
    <!--end::Card-->
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal91fdd17964e43374ae18c674f95cdaa3)): ?>
<?php $attributes = $__attributesOriginal91fdd17964e43374ae18c674f95cdaa3; ?>
<?php unset($__attributesOriginal91fdd17964e43374ae18c674f95cdaa3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal91fdd17964e43374ae18c674f95cdaa3)): ?>
<?php $component = $__componentOriginal91fdd17964e43374ae18c674f95cdaa3; ?>
<?php unset($__componentOriginal91fdd17964e43374ae18c674f95cdaa3); ?>
<?php endif; ?>


<?php /**PATH D:\websites\symfonix\Modules/Support\resources/views/admin/contact_form/index.blade.php ENDPATH**/ ?>