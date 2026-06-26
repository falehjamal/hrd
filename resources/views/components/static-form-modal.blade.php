@props([
    'modalId',
    'formId',
    'action',
    'title',
    'subtitle' => '',
    'submit' => 'Simpan',
    'size' => 'lg',
    'open' => false,
    'method' => 'PUT',
])

<div class="modal fade crud-form-modal" id="{{ $modalId }}" tabindex="-1" aria-hidden="true"
    data-static-form-modal
    data-open-on-load="{{ $open ? '1' : '' }}"
    data-validation-error="{{ $errors->any() ? '1' : '' }}">
    <div class="modal-dialog modal-dialog-centered modal-{{ $size }}">
        <div class="modal-content crud-form-modal__content">
            <div class="modal-header crud-form-modal__header">
                <div>
                    <h5 class="modal-title crud-form-modal__title">{{ $title }}</h5>
                    @if ($subtitle)
                        <p class="crud-form-modal__subtitle mb-0">{{ $subtitle }}</p>
                    @endif
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="{{ $formId }}" method="POST" action="{{ $action }}">
                @csrf
                @method($method)
                <div class="modal-body crud-form-modal__body">
                    {{ $slot }}
                </div>
                <div class="modal-footer crud-form-modal__footer">
                    <button type="button" class="btn btn-link crud-form-modal__cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary crud-form-modal__submit">
                        <i class="bx bx-save me-1"></i> {{ $submit }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
