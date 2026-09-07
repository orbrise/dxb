@extends("admin.layout.master")

@include('admin.profilesetting._lookup', [
    'entity'       => 'Ethnicity',
    'entityPlural' => 'Ethnicities',
    'items'        => $ethnicities,
    'addRoute'     => 'admin.addethnicity',
    'updateUrl'    => 'admin/updateethnicity',
    'deleteUrl'    => 'admin/delethnicity',
    'icon'         => 'fas fa-globe',
])
