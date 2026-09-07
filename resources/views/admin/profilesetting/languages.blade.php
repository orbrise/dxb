@extends("admin.layout.master")

@include('admin.profilesetting._lookup', [
    'entity'       => 'Language',
    'entityPlural' => 'Languages',
    'items'        => $languages,
    'addRoute'     => 'admin.addlanguage',
    'updateUrl'    => 'admin/updatelanguage',
    'deleteUrl'    => 'admin/dellanguage',
    'icon'         => 'fas fa-language',
])
