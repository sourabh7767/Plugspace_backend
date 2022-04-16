<?php

namespace App\Repositories;

use App\Models\User_Master;
use App\Repositories\BaseRepository;

/**
 * Class User_MasterRepository
 * @package App\Repositories
 * @version August 7, 2021, 5:16 am UTC
*/

class User_MasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return User_Master::class;
    }
}
