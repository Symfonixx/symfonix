<?php $__env->startSection('title' , __('Dashboard')); ?>
<?php $__env->startSection('toolbar'); ?>
    <?php if (isset($component)) { $__componentOriginaldbbc880c47f621cda59b70d6eb356b2f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldbbc880c47f621cda59b70d6eb356b2f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.breadcrumb','data' => ['pageTitle' => __('Dashboard'),'breadcrumbItems' => []]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Dashboard')),'breadcrumbItems' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([])]); ?>
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
    <div class="row gy-5 g-xl-8">
        <div class="col-xxl-4">
            <div class="card card-xl-stretch mb-xl-8">
                <!--begin::Beader-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1"><?php echo e(__('Visitors Overview')); ?></span>
                    </h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body p-0 d-flex flex-column">
                    <!--begin::Stats-->
                    <div class="card-p pt-5 bg-body flex-grow-1">
                        <!--begin::Row-->
                        <div class="row g-0">
                            <!--begin::Col-->
                            <div class="col mr-8">
                                <!--begin::Label-->
                                <div class="fs-7 text-muted fw-bold"><?php echo e(__('Total Visitors')); ?></div>
                                <!--end::Label-->
                                <!--begin::Stat-->
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder"><?php echo e($visitorsStats['totalVisitorsCount']); ?></div>
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->

                                    <!--end::Svg Icon-->
                                </div>
                                <!--end::Stat-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col">
                                <!--begin::Label-->
                                <div class="fs-7 text-muted fw-bold"><?php echo e(__('Today Visitors')); ?></div>
                                <!--end::Label-->
                                <!--begin::Stat-->
                                <div class="fs-4 fw-bolder"><?php echo e($visitorsStats['todayVisitorsCount']); ?></div>
                                <!--end::Stat-->
                            </div>
                            <!--end::Col-->

                        </div>
                        <!--end::Row-->
                        <!--begin::Row-->

                        <!--end::Row-->
                    </div>
                    <!--end::Stats-->
                    <!--begin::Chart-->
                    <div class="card">

                        <div class="card-body p-0 d-flex flex-column">
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table table-bordered">
                                    <!--begin::Table head-->
                                    <thead>
                                    <tr>
                                        <th class="w-50px"><?php echo e(__('Page')); ?></th>
                                        <th class="w-50px"><?php echo e(__('Visiting Count')); ?></th>
                                    </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $topVisitedPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topVisitedPage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>


                                                <a href="<?php echo e($topVisitedPage->url); ?>" target="_blank">
                                                    <?php echo e($topVisitedPage->url); ?>

                                                </a>
                                            </td>
                                            <td>
                                                <?php echo e($topVisitedPage->total); ?>

                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <!--end::Chart-->
                </div>
                <!--end::Body-->
            </div>
        </div>

        <div class="col-xxl-4">
            <div class="card card-xxl-stretch">
                <!--begin::Header-->
                <div class="card-header border-0 bg-primary py-5">
                    <h3 class="card-title fw-bolder text-white"><?php echo e(__('Overall CMS Statistics')); ?></h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body p-0">
                    <!--begin::Chart-->
                    <div class="mixed-widget-2-chart card-rounded-bottom bg-primary" data-kt-color="danger"
                         style="height: 100px"></div>
                    <!--end::Chart-->
                    <!--begin::Stats-->
                    <div class="card-p mt-n20 position-relative">
                        <!--begin::Row-->
                        <div class="row g-0">
                            <!--begin::Col-->
                            <div class="col bg-light-warning px-6 py-8 rounded-2 me-7 mb-7">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen032.svg-->

                                <div class="fs-2hx fw-bolder"><?php echo e($stats['pages'] ?? 0); ?></div>

                                <!--end::Svg Icon-->
                                <a href="<?php echo e(route('admin.pages.index')); ?>" target="_blank" class="text-warning fw-bold fs-6"> <?php echo e(__('Pages')); ?> </a>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col bg-light-primary px-6 py-8 rounded-2 mb-7">
                                <!--begin::Svg Icon | path: icons/duotune/finance/fin006.svg-->
                                <div class="fs-2hx fw-bolder">  <?php echo e($stats['blogs'] ?? 0); ?> </div>
                                <!--end::Svg Icon-->
                                <a href="<?php echo e(route('admin.blogs.index')); ?>" target="_blank" class="text-primary fw-bold fs-6"><?php echo e(__('Blogs')); ?></a>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                        <!--begin::Row-->
                        <div class="row g-0">
                            <!--begin::Col-->
                            <div class="col bg-light-danger px-6 py-8 rounded-2 me-7">
                                <!--begin::Svg Icon | path: icons/duotune/abstract/abs027.svg-->
                                <div class="fs-2hx fw-bolder">  0 </div>
                                <!--end::Svg Icon-->
                                <a href="" target="_blank" class="text-danger fw-bold fs-6 mt-2"><?php echo e(__('Products')); ?></a>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col bg-light-success px-6 py-8 rounded-2">
                                <!--begin::Svg Icon | path: icons/duotune/communication/com010.svg-->
                                <div class="fs-2hx fw-bolder">  <?php echo e($stats['services'] ?? 0); ?> </div>
                                <!--end::Svg Icon-->
                                <a href="<?php echo e(route('admin.services.index')); ?>" target="_blank" class="text-success fw-bold fs-6 mt-2"><?php echo e(__('Services')); ?></a>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                    </div>
                    <!--end::Stats-->
                </div>
                <!--end::Body-->
            </div>
        </div>

        <div class="col-xxl-4">
            <div class="card card-xxl-stretch">
                <!--begin::Header-->
                <div class="card-header border-0 bg-warning py-5">
                    <h3 class="card-title fw-bolder text-white"><?php echo e(__('Overall Users Statistics')); ?></h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body p-0">
                    <!--begin::Chart-->
                    <div class="mixed-widget-2-chart card-rounded-bottom bg-warning" data-kt-color="warning"
                         style="height: 100px"></div>
                    <!--end::Chart-->
                    <!--begin::Stats-->
                    <div class="card-p mt-n20 position-relative">
                        <!--begin::Row-->
                        <div class="row g-0">
                            <!--begin::Col-->
                            <div class="col bg-light-warning px-6 py-8 rounded-2 me-7 mb-7">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen032.svg-->

                                <div class="fs-2hx fw-bolder"><?php echo e($stats['admins'] ?? 0); ?></div>

                                <!--end::Svg Icon-->
                                <a href="<?php echo e(route('admin.staffs.index')); ?>" target="_blank" class="text-warning fw-bold fs-6"> <?php echo e(__('Staffs')); ?> </a>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col bg-light-primary px-6 py-8 rounded-2 mb-7">
                                <!--begin::Svg Icon | path: icons/duotune/finance/fin006.svg-->
                                <div class="fs-2hx fw-bolder">  <?php echo e($stats['users'] ?? 0); ?> </div>
                                <!--end::Svg Icon-->
                                <a href="<?php echo e(route('admin.users.index')); ?>" target="_blank" class="text-primary fw-bold fs-6"><?php echo e(__('Users')); ?></a>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                        <!--begin::Row-->
                        <div class="row g-0">
                            <!--begin::Col-->
                            <div class="col bg-light-danger px-6 py-8 rounded-2 me-7">
                                <!--begin::Svg Icon | path: icons/duotune/abstract/abs027.svg-->
                                <div class="fs-2hx fw-bolder">  <?php echo e($stats['subscribers'] ?? 0); ?> </div>
                                <!--end::Svg Icon-->
                                <a href="<?php echo e(route('admin.subscribers.index')); ?>" target="_blank"  class="text-danger fw-bold fs-6 mt-2"><?php echo e(__('Newsletter Subscribers')); ?></a>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col bg-light-success px-6 py-8 rounded-2">
                                <!--begin::Svg Icon | path: icons/duotune/communication/com010.svg-->
                                <div class="fs-2hx fw-bolder">  <?php echo e($stats['contacts'] ?? 0); ?> </div>
                                <!--end::Svg Icon-->
                                <a href="<?php echo e(route('admin.contact_forms.index')); ?>" target="_blank" class="text-success fw-bold fs-6 mt-2"><?php echo e(__('Contacts')); ?></a>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                    </div>
                    <!--end::Stats-->
                </div>
                <!--end::Body-->
            </div>
        </div>
    </div>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('App Monitoring')): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-5 g-xl-9 mb-10">
            <div class="col">
                <!--begin::Card-->
                <div class="card h-md-100">
                    <!--begin::Card body-->
                    <div class="card-body d-flex flex-center">
                        <!--begin::Button-->
                        <a target="_blank" href="/<?php echo e(config('telescope.path')); ?>">
                            <!--begin::Label-->
                            <span class="fw-bolder fs-3 text-gray-600 text-hover-primary">Telescope</span>
                            <!--end::Label-->
                        </a>
                        <!--begin::Button-->
                    </div>
                    <!--begin::Card body-->
                </div>
                <!--begin::Card-->
            </div>
            <div class="col">
                <!--begin::Card-->
                <div class="card h-md-100">
                    <!--begin::Card body-->
                    <div class="card-body d-flex flex-center">
                        <!--begin::Button-->
                        <a target="_blank" href="/<?php echo e(config('pulse.path')); ?>">

                            <!--begin::Label-->
                            <span class="fw-bolder fs-3 text-gray-600 text-hover-primary">Pulse</span>
                            <!--end::Label-->
                        </a>
                        <!--begin::Button-->
                    </div>
                    <!--begin::Card body-->
                </div>
                <!--begin::Card-->
            </div>
        </div>
    <?php endif; ?>
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
<?php /**PATH D:\websites\symfonix\Modules/User\resources/views/admin/dashboard/index.blade.php ENDPATH**/ ?>