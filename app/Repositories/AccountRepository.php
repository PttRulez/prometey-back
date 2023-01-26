<?php

namespace App\Repositories;

use App\Models\Account as Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class AccountRepository
 *
 * @package App\Repositories
 */
class AccountRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    public function getIndex($filters = null)
    {
        $fields = ['id', 'nickname', 'room_id'];

        $result = $this
            ->newInstance()
            ->select($fields)
            ->toBase()
            ->get();

        return $result;
    }
    /**
     * Получить модель для редактирования
     *
     * @param int $id
     *
     * @return Model
     */
    public function getEdit($id)
    {
        return $this->newInstance()->find($id);
    }
}
