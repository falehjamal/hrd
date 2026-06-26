@props(['modalId', 'url'])

<button type="button" class="btn btn-sm btn-icon-modern btn-outline-secondary" title="Edit"
    data-crud-edit
    data-crud-target="{{ $modalId }}"
    data-crud-edit-url="{{ $url }}">
    <i class="bx bx-edit-alt"></i>
</button>
