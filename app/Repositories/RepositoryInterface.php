<?php
/**
 * Created by PhpStorm.
 * User: hectnandez
 * Date: 06/02/2019
 * Time: 13:12
 */

namespace App\Repositories;


use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function all();

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function find(int $id);
}