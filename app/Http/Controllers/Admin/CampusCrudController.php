<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CampusRequest as StoreRequest;
use App\Models\Campus;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class CampusCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel(Campus::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/campus');
        CRUD::setEntityNameStrings(__('campus'), __('campuses'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        CRUD::addColumn(['name' => 'name',
            'label' => 'Name', ]);
        CRUD::addField(['name' => 'name',
            'label' => 'Name',
            'type' => 'text', ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
