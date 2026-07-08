@if(isset($modal) && $modal)

    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.blogs_categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h3 class="modal-title fw-bold" id="createCategoryModalLabel">{{ __('Add New Blog Category') }}</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-5">
                        <div class="mb-6">
                            <label for="name" class="form-label fw-semibold required">{{ __('Name') }}</label>
                            <input type="text" class="form-control form-control-solid" name="name" required
                                   placeholder="{{ __('Category name') }}">
                        </div>

                        <div class="mb-6">
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input type="hidden" name="auto_translate" value="0">
                                <input class="form-check-input" type="checkbox" name="auto_translate" value="1" id="createCatAutoTranslate" checked>
                                <label class="form-check-label" for="createCatAutoTranslate">{{ __('Auto translate to other languages') }}</label>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label for="gslug" class="form-label fw-semibold required">{{ __('Url') }}</label>
                            <input type="text" class="form-control form-control-solid" id="gslug" name="gslug" required
                                   placeholder="category-slug">
                            <input type="hidden" name="slug" value="{{ old('slug') }}" id="slug">
                            <div class="my-2 fs-7" id="link">{{ old('slug') }}</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light btn-active-light-primary"
                                data-bs-dismiss="modal">{{ __('Discard') }}</button>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Save Changes') }} <i class="bi bi-check2-circle ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
