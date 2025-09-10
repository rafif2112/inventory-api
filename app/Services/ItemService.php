<?php

namespace App\Services;

use App\Models\Item;

class ItemService
{
    public function getAllItems($search = null)
    {
        $data = Item::select('*')->orderBy('name', 'asc');

        if ($search) {
            $data->where('name', 'ILIKE', "%{$search}%");
        }

        $data = $data->get();

        return $data;
    }

    public function getItemPaginate($search = null)
    {
        $data = Item::select('*')->orderBy('name', 'asc');

        if ($search) {
            $data->where('name', 'ILIKE', "%{$search}%");
        }

        $data = $data->paginate(10);

        return $data;
    }

    public function createItem(array $data)
    {
        return Item::create($data);
    }

    public function getItemById($id)
    {
        return Item::find($id);
    }

    public function updateItem(Item $item, array $data)
    {
        $item->update($data);
        return $item;
    }

    public function deleteItem(Item $item)
    {
        return $item->delete();
    }
}
