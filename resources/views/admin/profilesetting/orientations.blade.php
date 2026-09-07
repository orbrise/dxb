@extends("admin.layout.master")

@include('admin.profilesetting._lookup', [
    'entity'       => 'Orientation',
    'entityPlural' => 'Orientations',
    'items'        => $orientations,
    'addRoute'     => 'admin.addorientation',
    'updateUrl'    => 'admin/updateorientation',
    'deleteUrl'    => 'admin/delorientation',
    'icon'         => 'fas fa-heart',
])
