@props([
    'modalId',
    'formId',
    'routePrefix',
    'openModal' => null,
    'resourceKey' => '',
    'titleCreate',
    'titleEdit' => 'Edit Data',
    'subtitleCreate' => '',
    'subtitleEdit' => 'Perbaiki data lalu simpan kembali.',
    'submitCreate' => 'Simpan',
    'submitEdit' => 'Simpan Perubahan',
    'size' => 'lg',
    'enctype' => null,
    'storeUrl' => null,
    'updateRoutePrefix' => null,
    'updateBaseUrl' => null,
])

@php
    $isEdit = is_numeric($openModal);
    $resolvedStoreUrl = $storeUrl ?? route($routePrefix.'.store');
    $resolvedUpdatePrefix = $updateRoutePrefix ?? $routePrefix;
    $formAction = $isEdit ? route($resolvedUpdatePrefix.'.update', $openModal) : $resolvedStoreUrl;
    $editShowUrl = $isEdit ? route($resolvedUpdatePrefix.'.show', $openModal) : '';
    $updatePlaceholderId = is_numeric($openModal) ? (int) $openModal : 1;
    $resolvedUpdateBase = $updateBaseUrl ?? preg_replace('#/\d+$#', '', route($resolvedUpdatePrefix.'.update', $updatePlaceholderId));
@endphp

<div class="modal fade crud-form-modal" id="{{ $modalId }}" tabindex="-1" aria-hidden="true"
    data-crud-modal
    data-resource-key="{{ $resourceKey }}"
    data-store-url="{{ $resolvedStoreUrl }}"
    data-update-base="{{ $resolvedUpdateBase }}"
    data-open-modal="{{ ($openModal ?? null) === 'create' ? 'create' : '' }}"
    data-edit-url="{{ $editShowUrl }}"
    data-validation-error="{{ $errors->any() ? '1' : '' }}"
    data-create-title="{{ $titleCreate }}"
    data-edit-title="{{ $titleEdit }}"
    data-subtitle-create="{{ $subtitleCreate }}"
    data-subtitle-edit="{{ $subtitleEdit }}"
    data-submit-create="{{ $submitCreate }}"
    data-submit-edit="{{ $submitEdit }}">
    <div class="modal-dialog modal-dialog-centered modal-{{ $size }}">
        <div class="modal-content crud-form-modal__content">
            <div class="modal-header crud-form-modal__header">
                <div>
                    <h5 class="modal-title crud-form-modal__title">{{ $titleCreate }}</h5>
                    @if ($subtitleCreate)
                        <p class="crud-form-modal__subtitle mb-0">{{ $subtitleCreate }}</p>
                    @else
                        <p class="crud-form-modal__subtitle mb-0 d-none"></p>
                    @endif
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="{{ $formId }}" method="POST" action="{{ $formAction }}" data-crud-form data-store-url="{{ $resolvedStoreUrl }}" @if($enctype) enctype="{{ $enctype }}" @endif>
                @csrf
                <input type="hidden" name="_method" class="crud-form-method" value="{{ $isEdit ? 'PUT' : 'POST' }}" @disabled(! $isEdit) />
                <div class="modal-body crud-form-modal__body">
                    {{ $slot }}
                </div>
                <div class="modal-footer crud-form-modal__footer">
                    <button type="button" class="btn btn-link crud-form-modal__cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary crud-form-modal__submit">
                        <i class="bx bx-save me-1"></i> <span class="crud-form-submit-label">{{ $isEdit ? $submitEdit : $submitCreate }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
