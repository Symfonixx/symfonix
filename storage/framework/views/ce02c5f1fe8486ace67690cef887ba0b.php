<div class="menu-item">
    <a class="menu-link <?php echo e(isset($active['dashboard']) ? 'active' : ''); ?>"
       href="<?php echo e(route('admin.dashboard.index')); ?>">
        <span class="menu-icon">
            <i class="bi bi-speedometer2"></i>
        </span>
        <span class="menu-title"><?php echo e(__('Dashboard')); ?></span>
    </a>
</div>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Settings Management')): ?>
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion <?php echo e(isset($active['settings']) ? 'here show' : ''); ?>">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-gear"></i></span>
            <span class="menu-title"><?php echo e(__('Settings')); ?></span>
            <span class="menu-arrow"></span>
        </span>

        <div
            class="menu-sub menu-sub-accordion <?php echo e(isset($active['websiteConfigurations']) || isset($active['seo']) || isset($active['branches']) ? 'show' : ''); ?>">
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['websiteConfigurations']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.settings.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Website Configurations')); ?></span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['seo']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.seo.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Seo Configurations')); ?></span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['branches']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.branches.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Branches')); ?></span>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Media Management')): ?>
    <div class="menu-item">
        <a class="menu-link <?php echo e(isset($active['filemanager']) ? 'active' : ''); ?>"
           href="<?php echo e(route('admin.filemanager.index')); ?>">
            <span class="menu-icon"><i class="bi bi-collection-play"></i></span>
            <span class="menu-title"><?php echo e(__('File Manager')); ?></span>
        </a>
    </div>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('CMS Management')): ?>
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion <?php echo e(isset($active['cms']) ? 'here show' : ''); ?>">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-intersect"></i></span>
            <span class="menu-title"><?php echo e(__('CMS')); ?></span>
            <span class="menu-arrow"></span>
        </span>

        <div
            class="menu-sub menu-sub-accordion <?php echo e(isset($active['pages']) || isset($active['blogs_categories']) || isset($active['blogs']) || isset($active['slides']) ? 'show' : ''); ?>">
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['pages']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.pages.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Pages')); ?></span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['blogs_categories']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.blogs_categories.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Blog Categories')); ?></span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['blogs']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.blogs.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Blogs')); ?></span>
                </a>
            </div>

        </div>
    </div>
<?php endif; ?>


<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Services Management')): ?>
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion <?php echo e(isset($active['services']) || isset($active['service_categories']) ? 'here show' : ''); ?>">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-window-stack"></i></span>
            <span class="menu-title"><?php echo e(__('Services')); ?></span>
            <span class="menu-arrow"></span>
        </span>
        <div
            class="menu-sub menu-sub-accordion <?php echo e(isset($active['services']) || isset($active['service_categories']) ? 'show' : ''); ?>">
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['service_categories']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.service_categories.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Service Categories')); ?></span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['services']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.services.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Services')); ?></span>
                </a>
            </div>

        </div>
    </div>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Testimonials Management')): ?>
    <div class="menu-item">
        <a class="menu-link <?php echo e(isset($active['testimonials']) ? 'active' : ''); ?>"
           href="<?php echo e(route('admin.testimonials.index')); ?>">
            <span class="menu-icon"><i class="bi bi-chat-quote"></i></span>
            <span class="menu-title"><?php echo e(__('Testimonials')); ?></span>
        </a>
    </div>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Team Management')): ?>
    <div class="menu-item">
        <a class="menu-link <?php echo e(isset($active['teams']) ? 'active' : ''); ?>"
           href="<?php echo e(route('admin.teams.index')); ?>">
            <span class="menu-icon"><i class="bi bi-people"></i></span>
            <span class="menu-title"><?php echo e(__('Team')); ?></span>
        </a>
    </div>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Hr Management')): ?>
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion <?php echo e(isset($active['hr']) ? 'here show' : ''); ?>">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-journal-text"></i></span>
            <span class="menu-title"><?php echo e(__('HR')); ?></span>
            <span class="menu-arrow"></span>
        </span>

        <div
            class="menu-sub menu-sub-accordion <?php echo e(isset($active['roles']) || isset($active['staffs']) || isset($active['users']) ? 'show' : ''); ?>">
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['roles']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.roles.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Roles')); ?></span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['staffs']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.staffs.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Staffs')); ?></span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['users']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.users.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Users')); ?></span>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Support Management')): ?>
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion <?php echo e(isset($active['support']) ? 'here show' : ''); ?>">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-headset"></i></span>
            <span class="menu-title"><?php echo e(__('Support Hub')); ?></span>
            <span class="menu-arrow"></span>
        </span>

        <div
            class="menu-sub menu-sub-accordion <?php echo e(isset($active['contact_forms']) || isset($active['subscribers']) || isset($active['blocked_ips']) || isset($active['firewall_logs']) || isset($active['search_keywords']) || isset($active['complaints']) || isset($active['leads']) ? 'show' : ''); ?>">
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['contact_forms']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.contact_forms.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Contacts')); ?></span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['leads']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.leads.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Leads')); ?></span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['subscribers']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.subscribers.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Newsletter Subscribers')); ?></span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['blocked_ips']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.blocked_ips.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Blocked IPs')); ?></span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['firewall_logs']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.firewall_logs.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Firewall Logs')); ?></span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link <?php echo e(isset($active['search_keywords']) ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.search_keywords.index')); ?>">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title"><?php echo e(__('Search Keywords')); ?></span>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Logs Management')): ?>
    <div class="menu-item">
        <a class="menu-link <?php echo e(isset($active['logs']) ? 'active' : ''); ?>"
           href="<?php echo e(route('admin.logs.index')); ?>">
            <span class="menu-icon"><i class="bi bi-window-stack"></i></span>
            <span class="menu-title"><?php echo e(__('Logs & Bugs')); ?></span>
        </a>
    </div>
<?php endif; ?>
<?php /**PATH D:\websites\symfonix\resources\views/components/admin/side-nav.blade.php ENDPATH**/ ?>