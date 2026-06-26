@php
    $openCrudModal = \App\Support\CrudModal::openId();
    if ($errors->any() && ! $openCrudModal) {
        $openCrudModal = old('_method') === 'PUT' ? 'create' : 'create';
    }
@endphp
