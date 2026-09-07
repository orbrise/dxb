@extends("admin.layout.master")

@include('admin.profilesetting._lookup', [
    'entity'       => 'Bust',
    'entityPlural' => 'Busts',
    'items'        => $busts,
    'addRoute'     => 'admin.addbust',
    'updateUrl'    => 'admin/updatebust',
    'deleteUrl'    => 'admin/delbust',
    'icon'         => 'fas fa-ruler',
])
