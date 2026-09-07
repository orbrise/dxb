@extends("admin.layout.master")

@include('admin.profilesetting._lookup', [
    'entity'       => 'Gender',
    'entityPlural' => 'Genders',
    'items'        => $genders,
    'addRoute'     => 'admin.addgender',
    'updateUrl'    => 'admin/updategender',
    'deleteUrl'    => 'admin/delgender',
    'icon'         => 'fas fa-venus-mars',
])
