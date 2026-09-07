@extends("admin.layout.master")

@include('admin.profilesetting._lookup', [
    'entity'       => 'Hair Color',
    'entityPlural' => 'Hair Colors',
    'items'        => $hairColors,
    'addRoute'     => 'admin.addhaircolor',
    'updateUrl'    => 'admin/updatehaircolor',
    'deleteUrl'    => 'admin/delhaircolor',
    'icon'         => 'fas fa-palette',
])
